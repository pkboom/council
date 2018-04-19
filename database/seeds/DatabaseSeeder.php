<?php

use App\User;
use App\Reply;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Reply::class, 10)->create();

        factory(User::class)->create([
            'name' => 'a',
            'username' => 'a',
            'email' => 'a@a.com',
            'password' => bcrypt('aaaaaa'),
            ]);

        factory(User::class)->create([
            'name' => 'b',
            'username' => 'b',
            'email' => 'b@b.com',
            'password' => bcrypt('bbbbbb'),
        ]);
    }
}
