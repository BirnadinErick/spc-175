<?php

namespace tinyfuse;

enum UserRole: int
{
    case VISITOR = 0;  // default role (can read pages/posts/projects or own users record)
    case EDITOR = 1 << 0;  // write permission to posts and page
    case PROJECTADMIN = 1 << 1;  // can change status, est. value and deadline etc. in project Long
    case SUPERADMIN = 1 << 2; // write permission on users !!CAREFUL

}