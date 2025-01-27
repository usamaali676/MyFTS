<?php

namespace Database\Seeders;

use App\Models\BusinessCategory;
use App\Models\SubCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categoriesWithSubcategories = [
            'Insurance' => [
                'Property insurance',
                'Life insurance',
                'Credit insurance',
                'Renters insurance',
                'Health insurance',
                'Auto insurance',
                'Home insurance',
                'Business insurance',
            ],
            'Financial consulting' => [
                'Wealth manager',
                'Investment advisor',
                'Financial planner or advisor',
                'Broker',
                'Chartered Financial Consultant',
                'Independent Financial Adviser',
                'Asset management',
                'Registered investment advisor (RIA)',
                'Tax planning',
            ],
        ];

        // Create categories and their subcategories
        foreach ($categoriesWithSubcategories as $categoryName => $subcategories) {
            $category = BusinessCategory::create(['name' => $categoryName]);

            foreach ($subcategories as $subcategoryName) {
                SubCategory::create([
                    'business_category_id' => $category->id,
                    'name' => $subcategoryName,
                ]);
            }
        }
    }
}
