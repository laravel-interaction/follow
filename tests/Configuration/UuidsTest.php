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
        self::assertSame('string', $following->getKeyType());
    }

    public function testIncrementing(): void
    {
        $following = new Following();
        self::assertFalse($following->getIncrementing());
    }

    public function testKeyName(): void
    {
        $following = new Following();
        self::assertSame('uuid', $following->getKeyName());
    }

    public function testKey(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->follow($channel);
        self::assertIsString($user->followerFollowings()->firstOrFail()->getKey());
    }
}
