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
            'Cleaning' => [
                'Residential Cleaning',
                'Commercial Cleaning',
                'Window Cleaning',
                'Carpet Cleaning',
                'Deep Cleaning',
            ],
            'Plumbing' => [
                'Leak Repair',
                'Drain Cleaning',
                'Pipe Installation',
                'Water Heater Services',
                'Sewer Line Repair',
            ],
            'Electrical' => [
                'Wiring Installation',
                'Lighting Installation',
                'Circuit Breaker Replacement',
                'Electrical Inspections',
                'Emergency Repairs',
            ],
            'Landscaping' => [
                'Lawn Care',
                'Garden Design',
                'Tree Removal',
                'Irrigation Systems',
                'Hardscaping',
            ],
            'Carpentry' => [
                'Furniture Making',
                'Cabinet Installation',
                'Custom Shelving',
                'Deck Construction',
                'Trim and Molding',
            ],
            'Pest Control' => [
                'Termite Control',
                'Rodent Removal',
                'Insect Extermination',
                'Preventive Treatments',
                'Wildlife Control',
            ],
            'Painting' => [
                'Interior Painting',
                'Exterior Painting',
                'Commercial Painting',
                'Specialty Finishes',
                'Wallpaper Installation',
            ],
            'Roofing' => [
                'Roof Installation',
                'Roof Repair',
                'Gutter Installation',
                'Roof Inspections',
                'Emergency Roof Repair',
            ],
            'HVAC' => [
                'AC Installation',
                'Heating Repair',
                'Duct Cleaning',
                'Furnace Installation',
                'Thermostat Installation',
            ],
            'Moving Services' => [
                'Residential Moving',
                'Commercial Moving',
                'Packing Services',
                'Storage Solutions',
                'Loading and Unloading',
            ],
            // Add more categories and subcategories as needed...
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
