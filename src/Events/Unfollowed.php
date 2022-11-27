<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Events;

use Illuminate\Database\Eloquent\Model;

class Unfollowed
{
    public function __construct(
        public Model $model
    ) {
    }
}
