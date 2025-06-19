<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Model Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the models used for roles and permissions.
    | By default, Spatie's models are used. Only change if you need custom models.
    |
    */

    'models' => [
        'role' => Spatie\Permission\Models\Role::class,
        'permission' => Spatie\Permission\Models\Permission::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | You may specify custom table names here for storing roles, permissions,
    | and their relationships.
    |
    */
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Roles and Permissions
    |--------------------------------------------------------------------------
    |
    | Define default roles and permissions to be created on migration/seed.
    |
    */
    'defaults' => [
        'roles' => [
            'admin',
            'user',
            'stakeholder',
        ],
        'permissions' => [
            'view_dashboard',
            'manage_users',
            'manage_products',
            'manage_orders',
            'view_reports',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Column Names
    |--------------------------------------------------------------------------
    |
    | You may customize the column names here.
    |
    */
    'column_names' => [
        'model_morph_key' => 'model_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Caching configuration for roles and permissions.
    | The 'key' setting MUST be non-null for Spatie\Permission to work.
    |
    */
    'cache' => [
        'enabled' => true,
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'model_key' => 'name',
        'store' => 'default',
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Define middleware aliases for roles and permissions.
    |
    */
    'middleware' => [
        'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ],
];
