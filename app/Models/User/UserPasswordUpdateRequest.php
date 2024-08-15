<?php

namespace App\Models\User;

use MA\PHPQUICK\MVC\Model;
use MA\PHPQUICK\Traits\PropertyAccessor;

class UserPasswordUpdateRequest extends Model
{
    use PropertyAccessor;

    public ?string $id = null;
    public ?string $oldPassword = null;
    public ?string $newPassword = null;
}
