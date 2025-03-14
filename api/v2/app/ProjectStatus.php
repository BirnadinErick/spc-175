<?php

namespace app;

enum ProjectStatus: string
{
    case Pending = 'Pending';
    case Active = 'Active';
    case Completed = 'Completed';
    case Denied = 'Denied';
}
