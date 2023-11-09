<?php

namespace Modules\Core\Contracts;

interface HasPermissions
{
    public static function getPermissionPrefixes(): array;
}
