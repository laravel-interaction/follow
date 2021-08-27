<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Events;

use Illuminate\Database\Eloquent\Model;

class Followed
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $following;

    public function __construct(Model $following)
    {
        $this->following = $following;
    }
}
