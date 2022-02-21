<?php

namespace TheClinicUseCases\Exceptions\Accounts;

class UserIsNotAuthenticated extends \RuntimeException
{
    public function __construct(
        string $message = "The current authenticated user is not authenticated.",
        int $code = 401
    ) {
        $this->message = $message;
        $this->code = $code;
    }
}
