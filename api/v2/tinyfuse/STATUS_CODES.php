<?php

namespace tinyfuse;
enum  STATUS_CODES: int
{
    case OK = 200;
    case CREATED = 201;

    case NOT_ALLOWED = 400;
    case UNAUTHD = 403;
    case NOT_FOUND = 404;

    case UNKNOWN_ERROR = 500;
}