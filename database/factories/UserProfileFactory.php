<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserProfile;
// use Faker\Generator as Faker;

$factory->define(UserProfile::class, function () {
    return [
        "user_id" => 1,
        "image" => "image/user/no-image.png"
    ];
});
