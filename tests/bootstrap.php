<?php

include __DIR__.'/../vendor/autoload.php';

set_error_handler(function ($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            if ($errstr === 'The each() function is deprecated. This message will be suppressed on further calls') {
                return true;
            }

            break;

        case E_WARNING:
        case E_USER_WARNING:
            if ($errstr === 'count(): Parameter must be an array or an object that implements Countable') {
                return true;
            }

            break;
    }

    return false;
});
