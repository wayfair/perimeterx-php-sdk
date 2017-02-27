<?php

namespace Perimeterx;

define('pxErrorPrefix', '');

final class PerimeterxException extends \Exception
{
    public static $MCRYPT_MISSING = 'mcrypt php extension required';
    public static $SEMAPHORE_MISSING = "semaphore php extension required";
    public static $APP_ID_MISSING = 'perimeterx application id is required';
    public static $AUTH_TOKEN_MISSING = 'perimeterx auth token is required';
    public static $COOKIE_MISSING  = 'perimeterx cookie key is required';
    public static $INVALID_LOGGER  = 'perimeterx logger must implement \Psr\Log\LoggerInterface';
    public static $MESSAGE_QUEUE_ID = "message_queue_id is missing in pxConfig.";
    public static $QUEUE_OVERFLOW = "Memory queue is overflow.";
}
