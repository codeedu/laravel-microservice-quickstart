 <?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

 use Faker\Generator as Faker;

$types = [\App\Models\CastMember::TYPE_DIRECTOR, \App\Models\CastMember::TYPE_ACTOR];

$factory->define(\App\Models\CastMember::class, function (Faker $faker) use ($types) {
    return [
        'name' => $faker->lastName,
        'type' => $types[array_rand($types)]
    ];
});
