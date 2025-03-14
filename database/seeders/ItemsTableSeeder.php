<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $items = [
//            ['name' => 'Giant Eye Head', 'image' => 'https://i.ibb.co/GfqFTnVz/geh.png'],
//            ['name' => 'Focused Eye', 'image' => 'https://i.ibb.co/1JTwZm2P/feye.png'],
//            ['name' => 'Raymans Fist', 'image' => 'https://i.ibb.co/wZb1TW89/raymanfist.png'],
//            ['name' => 'Phoenix Wings', 'image' => 'https://i.ibb.co/twQ2tGW6/phoenixwings.png'],
//            ['name' => 'Da Vinci Wings', 'image' => 'https://i.ibb.co/4Rh7HgLc/davinciwings.png'],
            ['name' => 'Rift Cape', 'image' => 'https://i.ibb.co/PzsMyL7v/riftcape.png'],
            ['name' => 'Sonic Buster Sword', 'image' => 'https://i.ibb.co/xKxChgpw/sonicbustersword.png'],
            ['name' => 'Ultra Violet Aura', 'image' => 'https://i.ibb.co/2T8Pt00/uva.png']
        ];

        foreach ($items as $index => $item) {
            DB::table('items')->insert([
                'user_id' => 16, // Assuming user IDs exist
                'item_name' => $item['name'],
                'description' => 'A rare and powerful item known as ' . $item['name'] . '.',
                'rarity' => 'epic',
                'price' => rand(1000, 10000) . '.00',
                'image' => $item['image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
