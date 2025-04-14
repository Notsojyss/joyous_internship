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
            ['name' => 'Giant Eye Head','price' =>'7000.00','image' => 'https://i.ibb.co/GfqFTnVz/geh.png', 'description' => ' A massive, mystical eye worn as a helmet. Said to grant the wearer uncanny perception and the ability to see hidden truths.', 'rarity' => 'legendary'],
            ['name' => 'Focused Eye','price' =>'800.00', 'image' => 'https://i.ibb.co/1JTwZm2P/feye.png','description' => 'A sharp and intense eye artifact that boosts concentration and accuracy. Often used by marksmen and scholars alike.', 'rarity' => 'rare'],
            ['name' => 'Raymans Fist','price' =>'8000.00', 'image' => 'https://i.ibb.co/wZb1TW89/raymanfist.png','description' =>'A powerful floating glove, inspired by legendary heroes. Delivers punches with shocking force, despite being detached from any arm',  'rarity' => 'epic'],
            ['name' => 'Phoenix Wings','price' =>'12000.00', 'image' => 'https://i.ibb.co/twQ2tGW6/phoenixwings.png','description' => ' Fiery wings radiating with rebirth energy. Grants the ability to rise from defeat, like the mythical bird itself.', 'rarity' => 'legendary'],
            ['name' => 'Da Vinci Wings','price' =>'10000.00', 'image' => 'https://i.ibb.co/4Rh7HgLc/davinciwings.png','description' =>'Mechanical wings based on Da Vinci’s genius sketches. Blends old-world engineering with graceful flight.', 'rarity' => 'legendary'],
            ['name' => 'Rift Cape','price' =>'1200.00', 'image' => 'https://i.ibb.co/PzsMyL7v/riftcape.png','description' =>'A cloak woven from the fabric of space-time. Allows short-distance teleportation and passage through rifts.', 'rarity' => 'legendary'],
            ['name' => 'Sonic Buster Sword','price' =>'5000.00', 'image' => 'https://i.ibb.co/xKxChgpw/sonicbustersword.png','description' =>'A heavy sword that hums with high-frequency vibrations. Cuts through enemies at sonic speeds.', 'rarity' => 'epic'],
            ['name' => 'Ultra Violet Aura','price' =>'3600.00', 'image' => 'https://i.ibb.co/2T8Pt00/uva.png','description' => 'An aura that glows with an invisible yet radiant energy. Enhances the user’s charisma and intimidation factor.', 'rarity' => 'epic']
        ];

        foreach ($items as $index => $item) {
            DB::table('items')->insert([
                'item_name' => $item['name'],
                'description' => $item['description'],
                'rarity' => $item['rarity'],
                'price' => $item['price'],
                'image' => $item['image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
