<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LaravelInteraction\Support\Interaction;
use function is_a;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Follow\Following[] $followableFollowings
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Follow\Concerns\Follower[] $followers
 * @property-read string|int|null $followers_count
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereFollowedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotFollowedBy(\Illuminate\Database\Eloquent\Model $user)
 */
trait Followable
{
    public function isNotFollowedBy(Model $user): bool
    {
        return ! $this->isFollowedBy($user);
    }

    public function isFollowedBy(Model $user): bool
    {
        if (! is_a($user, config('follow.models.user'))) {
            return false;
        }

        $followersLoaded = $this->relationLoaded('followers');

        if ($followersLoaded) {
            return $this->followers->contains($user);
        }

        return ($this->relationLoaded(
            'followableFollowings'
        ) ? $this->followableFollowings : $this->followableFollowings())
            ->where(config('follow.column_names.user_foreign_key'), $user->getKey())
            ->count() > 0;
    }

    public function scopeWhereNotFollowedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'followers',
            static function (Builder $query) use ($user): Builder {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function scopeWhereFollowedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'followers',
            static function (Builder $query) use ($user): Builder {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function followableFollowings(): MorphMany
    {
        return $this->morphMany(config('follow.models.pivot'), 'followable');
    }

    public function followers(): BelongsToMany
    {
        return $this->morphToMany(
            config('follow.models.user'),
            'followable',
            config('follow.models.pivot'),
            null,
            config('follow.column_names.user_foreign_key')
        )->withTimestamps();
    }

    public function followersCount(): int
    {
        if ($this->followers_count !== null) {
            return (int) $this->followers_count;
        }

        $this->loadCount('followers');

        return (int) $this->followers_count;
    }

    /**
     * @phpstan-param 1|2|3|4 $mode
     *
     * @param array<int, string>|null $divisors
     */
    public function followersCountForHumans(int $precision = 1, int $mode = PHP_ROUND_HALF_UP, $divisors = null): string
    {
        return Interaction::numberForHumans(
            $this->followersCount(),
            $precision,
            $mode,
            $divisors ?? config('follow.divisors')
        );
    }
}
