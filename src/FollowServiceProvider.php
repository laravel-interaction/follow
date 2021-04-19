<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow;

use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\InteractionServiceProvider;

class FollowServiceProvider extends InteractionServiceProvider
{
    protected $interaction = InteractionList::FOLLOW;
}
