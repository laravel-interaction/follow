<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use LaravelInteraction\Follow\Events\Followed;
use LaravelInteraction\Follow\Events\Unfollowed;

/**
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $follower
 * @property \Illuminate\Database\Eloquent\Model $followable
 *
 * @method static \LaravelInteraction\Follow\Following|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Follow\Following|\Illuminate\Database\Eloquent\Builder query()
 */
class Following extends MorphPivot
{
    protected $dispatchesEvents = [
        'created' => Followed::class,
        'deleted' => Unfollowed::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(
            function (self $like): void {
                if ($like->uuids()) {
                    $like->{$like->getKeyName()} = Str::orderedUuid();
                }
            }
        );
    }

    public function getIncrementing(): bool
    {
        if ($this->uuids()) {
            return true;
        }

        return parent::getIncrementing();
    }

    public function getKeyName(): string
    {
        return $this->uuids() ? 'uuid' : parent::getKeyName();
    }

    public function getKeyType(): string
    {
        return $this->uuids() ? 'string' : parent::getKeyType();
    }

    public function getTable()
    {
        return config('follow.table_names.followings') ?: parent::getTable();
    }

    public function isFollowedBy(Model $user): bool
    {
        return $user->is($this->follower);
    }

    public function isFollowedTo(Model $object): bool
    {
        return $object->is($this->followable);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('followable_type', app($type)->getMorphClass());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function followable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function follower(): BelongsTo
    {
        return $this->user();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('follow.models.user'), config('follow.column_names.user_foreign_key'));
    }

    protected function uuids(): bool
    {
        return (bool) config('follow.uuids');
    }
}
