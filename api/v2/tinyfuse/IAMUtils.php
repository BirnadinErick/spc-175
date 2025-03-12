<?php

namespace tinyfuse;

trait IAMUtils
{
    protected function get_user_roles_matrix(UserRole $user_role): array
    {
        $matrix = [];
        switch ($user_role) {
            case UserRole::VISITOR:
                $matrix = [
                    'is_user_editor' => false,
                    'is_user_sadmin' => false,
                    'is_user_padmin' => false,
                ];
                break;
            case UserRole::EDITOR:
                $matrix = [
                    'is_user_editor' => true,
                    'is_user_sadmin' => false,
                    'is_user_padmin' => false,
                ];
                break;
            case UserRole::PROJECT_ADMIN:
                $matrix = [
                    'is_user_editor' => false,
                    'is_user_sadmin' => false,
                    'is_user_padmin' => true,
                ];
                break;
            case UserRole::SUPER_ADMIN:
                $matrix = [
                    'is_user_editor' => true,
                    'is_user_sadmin' => true,
                    'is_user_padmin' => true,
                ];

        }
        return $matrix;
    }

}