<?php

namespace tinyfuse\lib;

enum Constants: int
{
    case InternalError = 500;
    case NotFound = 404;
    case BadRequest = 400;
    case NotAllowed = 403;
    case OK = 200;
    case Created = 201;
}