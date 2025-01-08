<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Knitting',
            'Crocheting',
            'Sewing',
            'Quilting',
            'Embroidery',
            'Scrapbooking',
            'Card Making',
            'Jewelry Making',
            'Woodworking',
            'Pottery',
            'Painting',
            'Drawing',
            'Calligraphy',
            'Origami',
            'Weaving',
            'Macrame',
            'Leather Crafting',
            'Glass Blowing',
            'Metalworking',
            'Candle Making',
            'Soap Making',
            'Paper Mache',
            'Basket Weaving',
            'Beadwork',
            'Felting',
            'Stained Glass',
            'Mosaic',
            'Decoupage',
            'Doll Making',
            'Toy Making',
            'Model Building',
            'Miniatures',
            'Flower Arranging',
            'Cake Decorating',
            'Baking',
            'Cooking',
            'Gardening',
            'Herb Crafting',
            'Spinning',
            'Dyeing',
            'Tatting',
            'Lacemaking',
            'Bookbinding',
            'Printmaking',
            'Screen Printing',
            'Tie-Dye',
            'Upcycling',
            'Recycling Crafts',
            'Digital Art',
            'Other',
        ];

        foreach ($categories as $category) {
            DB::table('project_categories')->insert([
                'name' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
