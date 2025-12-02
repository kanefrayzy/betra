<?php

namespace App\Enums;

enum HttpStatus
{
    const int OK = 200;
    const int CREATED = 201;
    const int NO_CONTENT = 204;

    // Client Error 4xx
    const int BAD_REQUEST = 400;
    const int UNAUTHORIZED = 401;
    const int FORBIDDEN = 403;
    const int NOT_FOUND = 404;
    const int METHOD_NOT_ALLOWED = 405;
    const int CONFLICT = 409;
    const int UNPROCESSABLE_ENTITY = 422;

    // Server Error 5xx
    const int INTERNAL_SERVER_ERROR = 500;
    const int SERVICE_UNAVAILABLE = 503;
}
