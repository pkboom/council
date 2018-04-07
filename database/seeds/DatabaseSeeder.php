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
        Cache::flush();

        factory(Reply::class, 30)->create();

        factory(User::class)->create([
            'name' => 'a',
            'email' => 'a@a.com',
            'password' => bcrypt('aaaaaa'),
            ]);

        factory(User::class)->create([
            'name' => 'b',
            'email' => 'b@b.com',
            'password' => bcrypt('bbbbbb'),
        ]);
    }
}
