<?php

namespace App\Exceptions\Identity;

use RuntimeException;

class RefreshTokenRevokedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Refresh token has been revoked.');
    }
}
