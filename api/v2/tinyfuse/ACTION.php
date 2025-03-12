<?php

namespace tinyfuse;

enum ACTION: int
{
    case NEW_USER = 1;
    case RESET_PASSWORD = 2;
}