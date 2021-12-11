<?php

use Illuminate\Database\Seeder;

class CastMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\CastMember::class, 200)->create();
    }
}
