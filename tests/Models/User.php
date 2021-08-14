<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Follow\Concerns\Followable;
use LaravelInteraction\Follow\Concerns\Follower;

/**
 * @method static \LaravelInteraction\Follow\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Follower;

    use Followable;
}
