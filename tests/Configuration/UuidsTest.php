<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Tests\Configuration;

use LaravelInteraction\Follow\Following;
use LaravelInteraction\Follow\Tests\Models\Channel;
use LaravelInteraction\Follow\Tests\Models\User;
use LaravelInteraction\Follow\Tests\TestCase;

/**
 * @internal
 */
final class UuidsTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        config([
            'follow.uuids' => true,
        ]);
    }

    public function testKeyType(): void
    {
        $following = new Following();
        $this->assertSame('string', $following->getKeyType());
    }

    public function testIncrementing(): void
    {
        $following = new Following();
        $this->assertFalse($following->getIncrementing());
    }

    public function testKeyName(): void
    {
        $following = new Following();
        $this->assertSame('uuid', $following->getKeyName());
    }

    public function testKey(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->follow($channel);
        $this->assertIsString($user->followerFollowings()->firstOrFail()->getKey());
    }
}
