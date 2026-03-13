<?php
declare(strict_types=1);

namespace App\Services;

use Closure;
use Illuminate\Cache\CacheManager;

readonly class CategoryCacheService
{
    public function __construct(
        private CacheManager $cache
    ) {}

    /**
     * @param Closure(): array $callback
     */
    public function remember(int $userId, Closure $callback): array
    {
        return $this->cache->remember($this->getKey($userId), 3600, $callback);
    }

    public function clear(int $userId): void
    {
        $this->cache->forget($this->getKey($userId));
    }

    private function getKey(int $userId): string
    {
        return 'categories.user.' . $userId;
    }
}
