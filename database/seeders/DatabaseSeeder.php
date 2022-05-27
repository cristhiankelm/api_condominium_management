<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('units')->insert([
            'name' => 'APT 100',
            'owner_id' => 1
        ]);
        DB::table('units')->insert([
            'name' => 'APT 101',
            'owner_id' => 1
        ]);
        DB::table('units')->insert([
            'name' => 'APT 200',
            'owner_id' => ''
        ]);
        DB::table('units')->insert([
            'name' => 'APT 201',
            'owner_id' => ''
        ]);

        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Academia',
            'cover' => 'gym.jpg',
            'days' => '1,2,4,5',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Piscina',
            'cover' => 'pool.jpg',
            'days' => '1,2,3,4,5',
            'start_time' => '07:00:00',
            'end_time' => '23:00:00',
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Churrasqueira',
            'cover' => 'barbecue.jpg',
            'days' => '1,2,4,5',
            'start_time' => '09:00:00',
            'end_time' => '23:00:00',
        ]);

        DB::table('walls')->insert([
            'title' => 'Titulo de Aviso de Teste',
            'body' => 'Falar mais baixo',
            'datecreated' => '2022-04-02 15:00:00',
        ]);
        DB::table('walls')->insert([
            'title' => 'Alerta geral para todos',
            'body' => 'Sem bagunça depois das 22hs!',
            'datecreated' => '2022-04-02 18:00:00',
        ]);
    }
}
