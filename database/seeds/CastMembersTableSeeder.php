<?php

use Illuminate\Database\Seeder;

class CastMembersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\CastMember::class,100)->create();
    }
}
