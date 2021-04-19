<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Follow\Events\Followed;
use LaravelInteraction\Follow\Tests\Models\Channel;
use LaravelInteraction\Follow\Tests\Models\User;
use LaravelInteraction\Follow\Tests\TestCase;

class FollowedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Followed::class]);
        $user->follow($channel);
        Event::assertDispatchedTimes(Followed::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Followed::class]);
        $user->follow($channel);
        $user->follow($channel);
        $user->follow($channel);
        Event::assertDispatchedTimes(Followed::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Followed::class]);
        $user->toggleFollow($channel);
        Event::assertDispatchedTimes(Followed::class);
    }
}
