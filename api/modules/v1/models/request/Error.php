<?php
namespace api\modules\v1\models\request;

abstract class Error
{
    // Base errors
    const BASE = 100;
    const BASE_INTERNAL_FAILURE = 1;
    const BASE_BAD_REQUEST      = 2;
    const BASE_BAD_TOKEN        = 3;
    const BASE_TOKEN_EXPIRED    = 4;
    const BASE_INVALID_VALUE    = 5;
    const BASE_ACTION_REJECTED  = 6;


    // Auth errors
    const AUTH = 200;
    const AUTH_BAD_CREDENTIALS         = 1;
    const AUTH_LICENSE_REQUIRED        = 2;
    const AUTH_PASSWORD_RESET_FAILURE  = 3;
    const AUTH_PASSWORD_MAIL_FAILURE   = 4;
    const AUTH_REGISTER_FAILURE        = 5;


    // Error messages
    public static $messages = [
        self::BASE => [
            self::BASE_INTERNAL_FAILURE => 'Internal error',
            self::BASE_BAD_REQUEST      => 'Bad request',
            self::BASE_BAD_TOKEN        => 'Bad token',
            self::BASE_TOKEN_EXPIRED    => 'Token expired',
            self::BASE_INVALID_VALUE    => 'Invalid value',
            self::BASE_ACTION_REJECTED  => 'Action rejected',

        ],
        self::AUTH => [
            self::AUTH_BAD_CREDENTIALS        => 'Bad credentials',
            self::AUTH_LICENSE_REQUIRED       => 'License acceptance required',
            self::AUTH_PASSWORD_RESET_FAILURE => 'Password reset failure',
            self::AUTH_PASSWORD_MAIL_FAILURE  => 'Password mail failure',
            self::AUTH_REGISTER_FAILURE       => 'Failure register request',
        ],

    ];

    /**
     * @param $code integer
     * @return string
     */
    public static function message($base, $code)
    {
        return isset(self::$messages[$base][$code]) ? self::$messages[$base][$code] : '';
    }
}
