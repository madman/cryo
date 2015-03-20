<?php

namespace Users;

class Roles
{
    /**
     * Each user that is allowed to auth must have this role
     */
    const ROLE_USER = 'ROLE_USER';

    /**
     * Allows access to any /admin resource
     */
    const ROLE_ADMIN = 'ROLE_ADMIN';
}
