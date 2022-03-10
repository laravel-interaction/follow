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
    /**
     * @var \LaravelInteraction\Follow\Tests\Models\User
     */
    private $user;

    /**
     * @var \LaravelInteraction\Follow\Tests\Models\Channel
     */
    private $channel;

    /**
     * @var \LaravelInteraction\Follow\Following
     */
    private $following;

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
        self::assertInstanceOf(Carbon::class, $this->following->created_at);
        self::assertInstanceOf(Carbon::class, $this->following->updated_at);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Following::query()->withType(Channel::class)->count());
        self::assertSame(0, Following::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        self::assertSame(config('follow.table_names.pivot'), $this->following->getTable());
    }

    public function testFollower(): void
    {
        self::assertInstanceOf(User::class, $this->following->follower);
    }

    public function testFollowable(): void
    {
        self::assertInstanceOf(Channel::class, $this->following->followable);
    }

    public function testUser(): void
    {
        self::assertInstanceOf(User::class, $this->following->user);
    }

    public function testIsFollowedTo(): void
    {
        self::assertTrue($this->following->isFollowedTo($this->channel));
        self::assertFalse($this->following->isFollowedTo($this->user));
    }

    public function testIsFollowedBy(): void
    {
        self::assertFalse($this->following->isFollowedBy($this->channel));
        self::assertTrue($this->following->isFollowedBy($this->user));
    }
}
