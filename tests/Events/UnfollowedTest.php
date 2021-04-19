<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Follow\Events\Unfollowed;
use LaravelInteraction\Follow\Tests\Models\Channel;
use LaravelInteraction\Follow\Tests\Models\User;
use LaravelInteraction\Follow\Tests\TestCase;

class UnfollowedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->follow($channel);
        Event::fake([Unfollowed::class]);
        $user->unfollow($channel);
        Event::assertDispatchedTimes(Unfollowed::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->follow($channel);
        Event::fake([Unfollowed::class]);
        $user->unfollow($channel);
        $user->unfollow($channel);
        Event::assertDispatchedTimes(Unfollowed::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Unfollowed::class]);
        $user->toggleFollow($channel);
        $user->toggleFollow($channel);
        Event::assertDispatchedTimes(Unfollowed::class);
    }
}
