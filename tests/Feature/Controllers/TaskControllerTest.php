<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Enums\TaskStatus;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
//    use RefreshDatabase;
    use DatabaseTransactions;
    use WithFaker;

    #[Test]
    public function authenticated_user_can_view_tasks(): void
    {
        // Arrange
        $user = User::factory()->create();
        Task::factory(3)->for($user)->create();

        // Act
        $response = $this->actingAs($user)->get(route('tasks.index'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('tasks.index');
        $response->assertViewHas('tasks');
    }

    #[Test]
    public function authenticated_user_can_view_exact_tasks(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tasks = Task::factory(3)->for($user)->create();
        $tasks->load('category');

        // Act
        $response = $this->actingAs($user)->get(route('tasks.index'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('tasks.index');
        $response->assertViewHas('tasks', $tasks->toResourceCollection()->resolve());
    }

    #[Test]
    public function authenticated_user_can_create_task(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();

        $taskData = [
            'title' => $this->faker->words(asText: true),
            'category_id' => $category->uuid,
            'description' => $this->faker->sentence(),
            'task_date' => $this->faker->date(),
        ];

        $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Task created successfully.');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => $taskData['title'],
        ]);
    }

    #[Test]
    public function authenticated_user_can_delete_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public static function invalidTaskDataProvider(): array
    {
        return [
            'missing title' => [
                ['title' => '', 'task_date' => fake()->date()],
                'title'
            ],
            'title too long' => [
                ['title' => str_repeat('', 256), 'task_date' => fake()->date()],
                'title'
            ],
            'missing task date' => [
                ['title' => 'Valid Title', 'task_date' => ''],
                'task_date'
            ],
            'invalid task date' => [
                ['title' => 'Valid Title', 'task_date' => 'not-a-valid-date'],
                'task_date'
            ]
        ];
    }

    #[Test]
    #[DataProvider('invalidTaskDataProvider')]
    public function task_creation_fails_with_invalid_data(array $data, string $expectedErrorField): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), $data);

        $response->assertInvalid($expectedErrorField);
        $this->assertDatabaseCount('tasks', 0);
    }

    #[Test]
    public function user_can_not_edit_another_users_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->get(route('tasks.edit', $task));

        $response->assertForbidden();
    }

    #[Test]
    public function user_can_not_delete_another_users_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->get(route('tasks.destroy', $task));

        $response->assertStatus(405);
    }

    #[Test]
    public function user_can_not_create_a_task_with_another_users_category(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->post(route('tasks.store'), [
            'title' => $this->faker->words(asText: true),
            'category_id' => $category->uuid,
            'task_date' => $this->faker->date(),
        ]);

        $response->assertInvalid('category');
        $this->assertDatabaseCount('tasks', 0);
    }

    #[Test]
    public function user_can_toggle_task_completion()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->assertNull($task->completed_at);

        $response = $this->actingAs($user)->patch(route('tasks.toggle-completion', $task));

        $response->assertOk();
        $response->assertJson(['completed' => true]);
        $this->assertNotNull($task->fresh()->completed_at);

        $response = $this->actingAs($user)->patch(route('tasks.toggle-completion', $task));

        $response->assertOk();
        $response->assertJson(['completed' => false]);
        $this->assertNull($task->fresh()->completed_at);
    }

    #[Test]
    public function task_index_can_filter_by_completed_status(): void
    {
        $user = User::factory()->create();

        Task::factory()->count(2)->for($user)->create();
        Task::factory()->count(2)->completed()->for($user)->create();

        $response = $this->actingAs($user)->get(route('tasks.index', ['status' => TaskStatus::Completed->value]));

        $response->assertOk();
        $this->assertCount(2, $response->viewData('tasks'));
    }

    #[Test]
    public function guest_can_not_view_tasks(): void
    {
        $this->get(route('tasks.index'))->assertRedirect(route('login'));
    }
}
