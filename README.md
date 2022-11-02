# Laravel Follow

User follow/unfollow behaviour for Laravel.

<p align="center">
<a href="https://packagist.org/packages/laravel-interaction/follow"><img src="https://poser.pugx.org/laravel-interaction/follow/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/follow"><img src="https://poser.pugx.org/laravel-interaction/follow/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel-interaction/follow"><img src="https://poser.pugx.org/laravel-interaction/follow/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/follow"><img src="https://poser.pugx.org/laravel-interaction/follow/license" alt="License"></a>
</p>

## Introduction

It let people express how they feel about the model(documentation/subject/topic).

![](https://img.shields.io/badge/Follow-1.2k-brightgreen?style=social)

## Installation

### Requirements

- [PHP 8.0+](https://php.net/releases/)
- [Composer](https://getcomposer.org)
- [Laravel 8.0+](https://laravel.com/docs/releases)

### Instructions

Require Laravel Follow using [Composer](https://getcomposer.org).

```bash
composer require laravel-interaction/follow
```

Publish configuration and migrations

```bash
php artisan vendor:publish --tag=follow-config
php artisan vendor:publish --tag=follow-migrations
```

Run database migrations.

```bash
php artisan migrate
```

## Usage

### Setup Follower

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Follow\Concerns\Follower;

class User extends Model
{
    use Follower;
}
```

### Setup Followable

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Follow\Concerns\Followable;

class Channel extends Model
{
    use Followable;
}
```

### Follower

```php
use LaravelInteraction\Follow\Tests\Models\Channel;
/** @var \LaravelInteraction\Follow\Tests\Models\User $user */
/** @var \LaravelInteraction\Follow\Tests\Models\Channel $channel */
// Follow to Followable
$user->follow($channel);
$user->unfollow($channel);
$user->toggleFollow($channel);

// Compare Followable
$user->hasFollowed($channel);
$user->hasNotFollowed($channel);

// Get followed info
$user->followerFollowings()->count(); 

// with type
$user->followerFollowings()->withType(Channel::class)->count(); 

// get followed channels
Channel::query()->whereFollowedBy($user)->get();

// get followed channels doesnt followed
Channel::query()->whereNotFollowedBy($user)->get();
```

### Followable

```php
use LaravelInteraction\Follow\Tests\Models\User;
use LaravelInteraction\Follow\Tests\Models\Channel;
/** @var \LaravelInteraction\Follow\Tests\Models\User $user */
/** @var \LaravelInteraction\Follow\Tests\Models\Channel $channel */
// Compare Follower
$channel->isFollowedBy($user); 
$channel->isNotFollowedBy($user);
// Get followers info
$channel->followers->each(function (User $user){
    echo $user->getKey();
});

$channels = Channel::query()->withCount('followers')->get();
$channels->each(function (Channel $channel){
    echo $channel->followers()->count(); // 1100
    echo $channel->followers_count; // "1100"
    echo $channel->followersCount(); // 1100
    echo $channel->followersCountForHumans(); // "1.1K"
});
```

### Events

| Event | Fired |
| --- | --- |
| `LaravelInteraction\Follow\Events\Followed` | When an object get followed. |
| `LaravelInteraction\Follow\Events\Unfollowed` | When an object get unfollowed. |

## License

Laravel Follow is an open-sourced software licensed under the [MIT license](LICENSE).
