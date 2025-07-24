<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the admin role
$adminRole = \Spatie\Permission\Models\Role::findByName('admin');

// Make sure the admin role exists
if (!$adminRole) {
    echo "Admin role not found\n";
    exit(1);
}

// Get all user permissions
$userPermissions = \Spatie\Permission\Models\Permission::whereIn('name', [
    'users.view', 
    'users.create', 
    'users.edit', 
    'users.delete'
])->get();

// Assign permissions to admin role
foreach ($userPermissions as $permission) {
    $adminRole->givePermissionTo($permission);
    echo "Added permission {$permission->name} to admin role\n";
}

echo "All user permissions have been added to the admin role\n"; 