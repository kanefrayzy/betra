<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'id' => 1,
                'domain' => 'MartinKazino',
                'sitename' => 'MartinKazino',
                'title' => 'MartinKazino - Online Gambling',
                'desc' => 'MartinKazino - Сервис быстрых лотерей оригинал, Играйте и выигрывайте! Захватывающая онлайн игра с выводом реальных денег, которая увлечет всех',
                'keys' => 'сервис мгновенных игр, stepx100, csgostep, disbet, nvuti, rubli-x, рубликс, нвути, rublix, заработок денег, в интернете, лотто, мгновенные игры, халява, бонус, игры с хэшем, instant game service, earnings on the internet, crash mode',
                'vk_key' => '112153310',
                'vk_secret' => NULL,
                'vk_url' => 'https://vk.com/',
                'fake' => 1,
                'order_id' => 2021,
                'mrh_ID' => 47537,
                'mrh_secret1' => '12312312',
                'mrh_secret2' => 'iaglqx3b',
                'fk_api' => '12312312312',
                'fk_wallet' => '12313121',
                'roulette_timer' => 20,
                'roulette_rotate' => 2208,
                'roulette_rotate2' => 48,
                'roulette_rotate_start' => 1700473831,
                'animals_rotate' => 342,
                'animals_rotate2' => 342,
                'animals_rotate_start' => 1700473817,
                'double_min_bet' => 0.01,
                'double_max_bet' => 1000,
                'double_fake_min' => 0.01,
                'double_fake_max' => 25,
                'dice_fake_min' => 0,
                'dice_fake_max' => 11,
                'crash_min_bet' => 1,
                'crash_max_bet' => 5000,
                'crash_timer' => 10,
                'battle_timer' => 15,
                'battle_min_bet' => 1,
                'battle_max_bet' => 1000,
                'battle_commission' => 10,
            ]
        ]);
    }
}
