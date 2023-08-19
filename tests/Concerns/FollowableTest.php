<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Tests\Concerns;

use LaravelInteraction\Follow\Tests\Models\Channel;
use LaravelInteraction\Follow\Tests\Models\User;
use LaravelInteraction\Follow\Tests\TestCase;

/**
 * @internal
 */
final class FollowableTest extends TestCase
{
    /**
     * @return \Iterator<array<class-string<\LaravelInteraction\Follow\Tests\Models\Channel|\LaravelInteraction\Follow\Tests\Models\User>>>
     */
    public static function provideModelClasses(): \Iterator
    {
        yield [Channel::class];

        yield [User::class];
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel> $modelClass
     */
    public function testFollowings(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        $this->assertSame(1, $model->followableFollowings()->count());
        $this->assertSame(1, $model->followableFollowings->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel> $modelClass
     */
    public function testFollowersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        $this->assertSame(1, $model->followersCount());
        $user->unfollow($model);
        $this->assertSame(1, $model->followersCount());
        $model->loadCount('followers');
        $this->assertSame(0, $model->followersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel> $modelClass
     */
    public function testFollowersCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        $this->assertSame('1', $model->followersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel> $modelClass
     */
    public function testIsFollowedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertFalse($model->isFollowedBy($model));
        $user->follow($model);
        $this->assertTrue($model->isFollowedBy($user));
        $model->load('followers');
        $user->unfollow($model);
        $this->assertTrue($model->isFollowedBy($user));
        $model->load('followers');
        $this->assertFalse($model->isFollowedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel> $modelClass
     */
    public function testIsNotFollowedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertTrue($model->isNotFollowedBy($model));
        $user->follow($model);
        $this->assertFalse($model->isNotFollowedBy($user));
        $model->load('followers');
        $user->unfollow($model);
        $this->assertFalse($model->isNotFollowedBy($user));
        $model->load('followers');
        $this->assertTrue($model->isNotFollowedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel> $modelClass
     */
    public function testFollowers(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        $this->assertSame(1, $model->followers()->count());
        $user->unfollow($model);
        $this->assertSame(0, $model->followers()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereFollowedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        $this->assertSame(1, $modelClass::query()->whereFollowedBy($user)->count());
        $this->assertSame(0, $modelClass::query()->whereFollowedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Follow\Tests\Models\User|\LaravelInteraction\Follow\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotFollowedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->follow($model);
        $this->assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotFollowedBy($user)->count()
        );
        $this->assertSame($modelClass::query()->count(), $modelClass::query()->whereNotFollowedBy($other)->count());
    }
}
