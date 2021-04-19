<?php

declare(strict_types=1);

namespace LaravelInteraction\Follow\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Follow\Concerns\Followable;

/**
 * @method static \LaravelInteraction\Follow\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Followable;
}
