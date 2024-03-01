<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Follow\Following;
use LaravelInteraction\Follow\Tests\Models\Channel;
use LaravelInteraction\Follow\Tests\Models\User;

/**
 * @internal
 */
final class FollowingTest extends TestCase
{
    private User $user;

    private Channel $channel;

    private Following $following;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->follow($this->channel);
        $this->following = Following::query()->firstOrFail();
    }

    public function testFollowingTimestamp(): void
    {
        $this->assertInstanceOf(Carbon::class, $this->following->created_at);
        $this->assertInstanceOf(Carbon::class, $this->following->updated_at);
    }

    public function testScopeWithType(): void
    {
        $this->assertSame(1, Following::query()->withType(Channel::class)->count());
        $this->assertSame(0, Following::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        $this->assertSame(config('follow.table_names.pivot'), $this->following->getTable());
    }

    public function testFollower(): void
    {
        $this->assertInstanceOf(User::class, $this->following->follower);
    }

    public function testFollowable(): void
    {
        $this->assertInstanceOf(Channel::class, $this->following->followable);
    }

    public function testUser(): void
    {
        $this->assertInstanceOf(User::class, $this->following->user);
    }

    public function testIsFollowedTo(): void
    {
        $this->assertTrue($this->following->isFollowedTo($this->channel));
        $this->assertFalse($this->following->isFollowedTo($this->user));
    }

    public function testIsFollowedBy(): void
    {
        $this->assertFalse($this->following->isFollowedBy($this->channel));
        $this->assertTrue($this->following->isFollowedBy($this->user));
    }
}
