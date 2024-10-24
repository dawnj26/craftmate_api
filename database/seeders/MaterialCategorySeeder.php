<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('material_categories')->insert([
            [
                'name' => 'Wood',
                'description' => 'Materials made from wood or wood-based products.',
                'hex_color' => hexdec('FF8B4513'), // SaddleBrown
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Metal',
                'description' => 'Metallic materials such as iron, steel, copper, and aluminum.',
                'hex_color' => hexdec('FFB0C4DE'), // LightSteelBlue
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fabric',
                'description' => 'Various types of fabrics including cotton, silk, wool, and synthetic materials.',
                'hex_color' => hexdec('FFFF69B4'), // HotPink
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plastic',
                'description' => 'Materials made from synthetic polymers and plastics.',
                'hex_color' => hexdec('FFFFD700'), // Gold
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Glass',
                'description' => 'Glass materials used for crafting, such as stained glass or glass beads.',
                'hex_color' => hexdec('FFADD8E6'), // LightBlue
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ceramic',
                'description' => 'Materials made from clay and ceramics, like tiles and pottery.',
                'hex_color' => hexdec('FFD2691E'), // Chocolate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stone',
                'description' => 'Natural stone materials such as granite, marble, and sandstone.',
                'hex_color' => hexdec('FF808080'), // Gray
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paper',
                'description' => 'Paper products including cardstock, origami paper, and scrapbooking materials.',
                'hex_color' => hexdec('FFFFFACD'), // LemonChiffon
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leather',
                'description' => 'Natural and synthetic leather materials used for crafting and sewing.',
                'hex_color' => hexdec('FFA0522D'), // Sienna
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Adhesives',
                'description' => 'Glues, tapes, and other adhesive materials used to bond items together.',
                'hex_color' => hexdec('FFFFF8DC'), // Cornsilk
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paints & Dyes',
                'description' => 'Paints, dyes, and pigments for coloring and finishing surfaces.',
                'hex_color' => hexdec('FFDA70D6'), // Orchid
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tools',
                'description' => 'Crafting tools and equipment like scissors, knives, brushes, and hammers.',
                'hex_color' => hexdec('FF4682B4'), // SteelBlue
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
