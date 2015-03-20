<?php

namespace Users;

final class UsersEvents
{
    /**
     * Triggered before new user saved to db
     */
    const NEW_USER_BEFORE_REGISTER = 'users.user.new.before';

    /**
     * Triggered right after new user saved to db
     */
    const NEW_USER_REGISTERED = 'users.user.new';

    /**
     * Triggered when user forgot password
     */
    const SEND_REMIND_CODE = 'users.send.remind.code';

    /**
     * Triggered after user balance has been changed
     */
    const USER_BALANCE_CHANGED = 'users.balance.changed';

    /**
     * Triggered at change user balance
     */
    const USER_BALANCE_CHANGE = 'users.balance.change';

    /**
     * Triggered after user successfully log in
     */
    const USER_AUTHORIZATION = 'users.user.auth';

    /**
     * Triggered after user failed log in
     */
    const USER_AUTHORIZATION_FAIL = 'users.user.authfail';
}
