<?php

namespace TheClinicUseCases\Exceptions\Accounts;

class UserIsNotAuthorized extends \RuntimeException
{
    public function __construct(
        string $message = "The current authenticated user is not authorized for reading accounts.",
        int $code = 403
    ) {
        $this->message = $message;
        $this->code = $code;
    }
}
