<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskFrequency;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int|null $category_id
 * @property string $title
 * @property string|null $description
 * @property string $frequency
 * @property array<array-key, mixed>|null $frequency_config
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Category|null $category
 * @property-read Collection<int, Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read User $user
 * @method static Builder<static>|RecurringTask newModelQuery()
 * @method static Builder<static>|RecurringTask newQuery()
 * @method static Builder<static>|RecurringTask onlyTrashed()
 * @method static Builder<static>|RecurringTask query()
 * @method static Builder<static>|RecurringTask whereCategoryId($value)
 * @method static Builder<static>|RecurringTask whereCreatedAt($value)
 * @method static Builder<static>|RecurringTask whereDeletedAt($value)
 * @method static Builder<static>|RecurringTask whereDescription($value)
 * @method static Builder<static>|RecurringTask whereEndDate($value)
 * @method static Builder<static>|RecurringTask whereFrequency($value)
 * @method static Builder<static>|RecurringTask whereFrequencyConfig($value)
 * @method static Builder<static>|RecurringTask whereId($value)
 * @method static Builder<static>|RecurringTask whereStartDate($value)
 * @method static Builder<static>|RecurringTask whereTitle($value)
 * @method static Builder<static>|RecurringTask whereUpdatedAt($value)
 * @method static Builder<static>|RecurringTask whereUserId($value)
 * @method static Builder<static>|RecurringTask whereUuid($value)
 * @method static Builder<static>|RecurringTask withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|RecurringTask withoutTrashed()
 * @mixin \Eloquent
 */
class RecurringTask extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'frequency',
        'frequency_config',
        'start_date',
        'end_date',
    ];

    public function casts(): array
    {
        return [
            'frequency' => TaskFrequency::class,
            'frequency_config' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

//    #[Scope]
//    protected function active(Builder $query, Carbon $date): void
//    {
//        $query->where(fn(Builder $query) => $query->whereNull('start_date')
//            ->orWhere('start_date', '<=', $date));
//        $query->where(fn(Builder $query) => $query->whereNull('end_date')
//            ->orWhere('end_date', '>=', $date));
//    }
}
