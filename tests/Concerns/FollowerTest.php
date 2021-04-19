<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Tests\Concerns;

use LaravelInteraction\Follow\Following;
use LaravelInteraction\Follow\Tests\Models\Channel;
use LaravelInteraction\Follow\Tests\Models\User;
use LaravelInteraction\Follow\Tests\TestCase;

class FollowerTest extends TestCase
{
    public function testFollow(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->follow($channel);
        $this->assertDatabaseHas(
            Following::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'followable_type' => $channel->getMorphClass(),
                'followable_id' => $channel->getKey(),
            ]
        );
        $user->load('followerFollowings');
        $user->unfollow($channel);
        $user->load('followerFollowings');
        $user->follow($channel);
    }

    public function testUnfollow(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->follow($channel);
        $this->assertDatabaseHas(
            Following::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'followable_type' => $channel->getMorphClass(),
                'followable_id' => $channel->getKey(),
            ]
        );
        $user->unfollow($channel);
        $this->assertDatabaseMissing(
            Following::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'followable_type' => $channel->getMorphClass(),
                'followable_id' => $channel->getKey(),
            ]
        );
    }

    public function testToggleFollow(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleFollow($channel);
        $this->assertDatabaseHas(
            Following::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'followable_type' => $channel->getMorphClass(),
                'followable_id' => $channel->getKey(),
            ]
        );
        $user->toggleFollow($channel);
        $this->assertDatabaseMissing(
            Following::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'followable_type' => $channel->getMorphClass(),
                'followable_id' => $channel->getKey(),
            ]
        );
    }

    public function testFollowings(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleFollow($channel);
        self::assertSame(1, $user->followerFollowings()->count());
        self::assertSame(1, $user->followerFollowings->count());
    }

    public function testHasFollowed(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleFollow($channel);
        self::assertTrue($user->hasFollowed($channel));
        $user->toggleFollow($channel);
        $user->load('followerFollowings');
        self::assertFalse($user->hasFollowed($channel));
    }

    public function testHasNotFollowed(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleFollow($channel);
        self::assertFalse($user->hasNotFollowed($channel));
        $user->toggleFollow($channel);
        self::assertTrue($user->hasNotFollowed($channel));
    }
}
