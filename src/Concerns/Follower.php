<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use LaravelInteraction\Follow\Following;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Follow\Following[] $followerFollowings
 * @property-read int|null $follower_followings_count
 */
trait Follower
{
    public function hasNotFollowed(Model $object): bool
    {
        return ! $this->hasFollowed($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasFollowed(Model $object): bool
    {
        return ($this->relationLoaded(
            'followerFollowings'
        ) ? $this->followerFollowings : $this->followerFollowings())
            ->where('followable_id', $object->getKey())
            ->where('followable_type', $object->getMorphClass())
            ->count() > 0;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return \LaravelInteraction\Follow\Following
     */
    public function follow(Model $object): Following
    {
        $attributes = [
            'followable_id' => $object->getKey(),
            'followable_type' => $object->getMorphClass(),
        ];

        return $this->followerFollowings()
            ->where($attributes)
            ->firstOr(function () use ($attributes) {
                $followerFollowingsLoaded = $this->relationLoaded('followerFollowings');
                if ($followerFollowingsLoaded) {
                    $this->unsetRelation('followerFollowings');
                }

                return $this->followerFollowings()
                    ->create($attributes);
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followerFollowings(): HasMany
    {
        return $this->hasMany(
            config('follow.models.following'),
            config('follow.column_names.user_foreign_key'),
            $this->getKeyName()
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool|\LaravelInteraction\Follow\Following
     */
    public function toggleFollow(Model $object)
    {
        return $this->hasFollowed($object) ? $this->unfollow($object) : $this->follow($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function unfollow(Model $object): bool
    {
        $hasNotFollowed = $this->hasNotFollowed($object);
        if ($hasNotFollowed) {
            return true;
        }
        $followerFollowingsLoaded = $this->relationLoaded('followerFollowings');
        if ($followerFollowingsLoaded) {
            $this->unsetRelation('followerFollowings');
        }

        return (bool) $this->followedItems(get_class($object))
            ->detach($object->getKey());
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function followedItems(string $class): MorphToMany
    {
        return $this->morphedByMany(
            $class,
            'followable',
            config('follow.models.following'),
            config('follow.column_names.user_foreign_key')
        )
            ->withTimestamps();
    }
}
