<?php

namespace Components;


class Permission
{
    public static function getConfig()
    {
        $config = [];

        foreach (self::configs() as $blocks) {
            foreach ($blocks as $groups) {
                foreach ($groups as $blockName => $res) {
                    $config[$blockName] = $res['resource'];
                }
            }
        }

        return $config;
    }

    public static function whiteList()
    {
        $resource = [];
        $resource[] = '/user/profile/login';
        $resource[] = '/user/profile/logout';
        $resource[] = '/user/profile/index';
        $resource[] = '/user/profile/edit';
        $resource[] = '/site/home/index';

        $resource[] = '/deploy/process/release';
        $resource[] = '/deploy/release/tags';

        return $resource;
    }

    public static function check($userId)
    {

    }

    public static function configs()
    {
        $configs = [];

        // block
        $configs['system'] = [
            //group
            'user' => [
                'account-manager' => [
                    'resource' => [
                        'account-list' => '/user/account/index',
                        'account-create' => '/user/account/create',
                        'account-edit' => '/user/account/edit',
                        'account-delete' => '/user/account/delete',
                    ],
                    'menu-index' => '/user/account/index',
                ],
                'role-manager' => [
                    'resource' => [
                        'role-list' => '/user/role/index',
                        'role-create' => '/user/role/create',
                        'role-edit' => '/user/role/edit',
                        'role-delete' => '/user/role/delete',
                    ],
                    'menu-index' => '/user/role/index',
                ],
            ],
        ];

        $configs['main'] = [
            'deploy' => [
                'project-manager' => [
                    'resource' => [
                        'project-list' => '/deploy/project/index',
                        'project-create' => '/deploy/project/create',
                        'project-edit' => '/deploy/project/edit',
                        'project-delete' => '/deploy/project/delete',
                    ],
                    'menu-index' => '/deploy/project/index',
                ],
                'release-project' => [
                    'resource' => [
                        'my-list' => '/deploy/release/index',
                        'release-project' => '/deploy/release/project',
                        'release-diff' => '/deploy/release/diff',
                        'sync-project' => '/deploy/release/sync',
                        'rollback-project' => '/deploy/release/rollback',
                        //'do-review' => '/deploy/review/do',
                        //'send-review' => '/deploy/review/send',                        
                    ],
                    'menu-index' => '/deploy/release/index',
                ],
                /*
                'project-log' => [
                    'resource' => [
                        'project-log' => '/deploy/log/project',
                    ],
                    'menu-index' => '/deploy/log/project',
                ],
                */
            ],
        ];

        return $configs;
    }
}
