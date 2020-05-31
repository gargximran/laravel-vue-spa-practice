<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Role;

$factory->define(Role::class, function () {
    return [
        "name" => 'Admin'
    ];
});
