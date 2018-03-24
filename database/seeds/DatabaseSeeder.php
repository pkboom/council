<?php

use Illuminate\Database\Seeder;
use App\Reply;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
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
