<?php

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
        factory(App\Post::class, 100)->create();
        $this->command->info('테스트를 위해 100개의 포스트를 만들었습니다.');
    }
}
