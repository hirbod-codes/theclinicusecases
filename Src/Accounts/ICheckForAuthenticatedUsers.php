<?php

namespace TheClinicUseCases\Accounts;

interface ICheckForAuthenticatedUsers
{
    /**
     * Returns true if there is NO authenticated user.
     *
     * @return boolean
     */
    public function checkIfNoOneIsAuthenticated(): bool;
}
