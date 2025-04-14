<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $items = [
            ['name' => 'Giant Eye Head','price' =>'7000.00','image' => 'https://i.ibb.co/GfqFTnVz/geh.png', 'description' => ' A massive, mystical eye worn as a helmet. Said to grant the wearer uncanny perception and the ability to see hidden truths.', 'rarity' => 'legendary'],
            ['name' => 'Focused Eye','price' =>'800.00', 'image' => 'https://i.ibb.co/1JTwZm2P/feye.png','description' => 'A sharp and intense eye artifact that boosts concentration and accuracy. Often used by marksmen and scholars alike.', 'rarity' => 'rare'],
            ['name' => 'Raymans Fist','price' =>'8000.00', 'image' => 'https://i.ibb.co/wZb1TW89/raymanfist.png','description' =>'A powerful floating glove, inspired by legendary heroes. Delivers punches with shocking force, despite being detached from any arm', 'rarity' => 'epic'],
            ['name' => 'Phoenix Wings','price' =>'12000.00', 'image' => 'https://i.ibb.co/twQ2tGW6/phoenixwings.png','description' => ' Fiery wings radiating with rebirth energy. Grants the ability to rise from defeat, like the mythical bird itself.', 'rarity' => 'legendary'],
            ['name' => 'Da Vinci Wings','price' =>'10000.00', 'image' => 'https://i.ibb.co/4Rh7HgLc/davinciwings.png','description' => 'Mechanical wings based on Da Vinci’s genius sketches. Blends old-world engineering with graceful flight.', 'rarity' => 'legendary'],
            ['name' => 'Rift Cape','price' =>'1200.00', 'image' => 'https://i.ibb.co/PzsMyL7v/riftcape.png','description' => 'A cloak woven from the fabric of space-time. Allows short-distance teleportation and passage through rifts.', 'rarity' => 'rare'],
            ['name' => 'Sonic Buster Sword','price' =>'5000.00', 'image' => 'https://i.ibb.co/xKxChgpw/sonicbustersword.png','description' => 'A heavy sword that hums with high-frequency vibrations. Cuts through enemies at sonic speeds.', 'rarity' => 'epic'],
            ['name' => 'Ultra Violet Aura','price' =>'3600.00', 'image' => 'https://i.ibb.co/2T8Pt00/uva.png','description' => 'An aura that glows with an invisible yet radiant energy. Enhances the user’s charisma and intimidation factor.', 'rarity' => 'epic'],
            ['name' => 'Pickaxe','price' =>'10.00', 'image' => 'https://i.ibb.co/1GkyrMqK/pickaxe-removebg-preview.png','description' => 'Pickaxe grants the Enhanced Digging mod, which allows the player to break blocks faster', 'rarity' => 'common'],
            ['name' => 'Challenge Crown','price' =>'1400.00', 'image' => 'https://i.ibb.co/r2qF8yqG/image-removebg-preview.png','description' => 'The Challenge Crown is an unsplicable hat item which represent that You were #1 in a Daily Challenge! Show off with a shiny crown', 'rarity' => 'rare']
            ,['name' => 'Angel Wings','price' =>'400.00', 'image' => 'https://i.ibb.co/ZprRtk3s/image-removebg-preview-1.png','description' => 'Better than a Halo, these will actually let you double jump!', 'rarity' => 'uncommon']
            ,['name' => 'Devil Wings','price' =>'500.00', 'image' => 'https://i.ibb.co/BdPfFQK/image-removebg-preview-2.png','description' => 'The Devil Wings is an unsplicable back item, lets you double jump and its pretty cool.', 'rarity' => 'uncommon']

        ];

        foreach ($items as $item) {
            // Check if the item already exists by name
            $existingItem = DB::table('items')->where('item_name', $item['name'])->first();

            if ($existingItem) {
                // If item exists, just update the existing record
                DB::table('items')->where('item_name', $item['name'])->update([
                    'description' => $item['description'],
                    'rarity' => $item['rarity'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'updated_at' => now(),
                ]);
            } else {
                // If item does not exist, insert the new item with created_at
                DB::table('items')->insert([
                    'item_name' => $item['name'],
                    'description' => $item['description'],
                    'rarity' => $item['rarity'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'created_at' => now(), // Set created_at only for new items
                    'updated_at' => now(), // Set the same value for updated_at initially
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
