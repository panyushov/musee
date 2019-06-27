<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\SMTPConfig;
use App\Services\MusementService;
use Faker\Generator as Faker;

$factory->define(SMTPConfig::class, function (Faker $faker) {
    return [
        "host" => $faker->domainName,
        "port" => $faker->numberBetween(0, 65535),
        "username" => $faker->userName,
        "password" => $faker->password,
        "encryption" => $faker->text(6),
        "from" => $faker->unique()->safeEmail,
        "locale" => null,
        "notify" => null,
        "corrupt" => $faker->text(10),
    ];
});


$factory->state(SMTPConfig::class, 'with-recipients-and-locale', function ($faker) {
    return [
        "notify" => sprintf("%s, %s, %s", $faker->unique()->safeEmail, $faker->unique()->safeEmail, $faker->unique()->safeEmail),
        "locale" => MusementService::ITALIAN_LOCALE,
    ];
});


$factory->state(SMTPConfig::class, 'no-corrupt', function ($faker) {
    return [
        "corrupt" => null,
    ];
});