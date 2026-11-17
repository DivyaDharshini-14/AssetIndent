<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
$u = User::where('name', 'like', '%John%')->first() ?? User::where('email', 'like', '%john%')->first() ?? User::first();
echo json_encode(['id' => $u->id, 'name' => $u->name, 'roles' => $u->roles->pluck('name'), 'permissions' => $u->getAllPermissions()->pluck('name')]);
