<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Events;

use Illuminate\Database\Eloquent\Model;

class Unfollowed
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $following;

    /**
     * Liked constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $following
     */
    public function __construct(Model $following)
    {
        $this->following = $following;
    }
}
