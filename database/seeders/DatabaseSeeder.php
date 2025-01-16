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
            'Contractors' => [
                'Residential General Contracting',
                'Commercial General Contracting',
                'Custom Home Building',
                'Renovation Services',
                'Green Building Specialists',
                'Historical Restoration Contractors',
            ],
            'Remodeling' => [
                'Residential Kitchen Remodeling',
                'Commercial Bathroom Remodeling',
                'Basement Finishing',
                'Whole-Home Renovations',
                'Luxury Kitchen Remodeling',
                'Outdoor Living Space Remodeling',
                'ADA Compliant Bathroom Remodeling',
                'Garage Conversions',
            ],
            'Home Inspection' => [
                'Residential Pre-Purchase Inspections',
                'Commercial Property Inspections',
                'Mold Inspections',
                'Electrical System Inspections',
                'HVAC System Inspections',
                'Pool and Spa Inspections',
                'Energy Efficiency Assessments',
                'Pest and Termite Inspections',
            ],
            'Damage Restoration' => [
                'Residential Water Damage Restoration',
                'Commercial Fire Damage Restoration',
                'Mold Remediation',
                'Storm Damage Repair',
                'Asbestos Removal Services',
                'Residential Content Restoration',
                'Commercial Document Recovery',
                'Flood Mitigation Services',
            ],
            'Home Health Care' => [
                'Residential Elderly Care',
                'Pediatric Home Care Services',
                'Post-Surgery Assistance',
                'Disability Support Services',
                'Physical Therapy at Home',
                'In-Home Medication Management',
                'Hospice Care Services',
                'Meal Preparation Assistance',
            ],
            'Appliances Repair' => [
                'Residential Appliance Repair',
                'Commercial HVAC Repair',
                'Washing Machine Repair',
                'Microwave Oven Repair',
                'Dishwasher Repair Services',
                'Ice Machine Repair',
                'Range Hood Repairs',
                'Garbage Disposal Repairs',
            ],
            'Mortgage Brokers' => [
                'Residential Mortgages',
                'Commercial Property Loans',
                'Refinancing Services',
                'First-Time Home Buyer Loans',
                'VA Loan Specialists',
                'FHA Loan Services',
                'Reverse Mortgage Consulting',
                'Jumbo Loan Financing',
            ],
            'Garage Door' => [
                'Residential Garage Door Installation',
                'Commercial Garage Door Installation',
                'Garage Door Repairs',
                'Smart Garage Systems',
                'Garage Door Panel Replacement',
                'Spring and Opener Repairs',
                'High-Speed Commercial Garage Doors',
                'Insulated Garage Door Installation',
            ],
            'Cash for Junk Cars' => [
                'Residential Vehicle Towing',
                'Commercial Fleet Removal',
                'Scrap Metal Recycling',
                'Used Auto Parts Sales',
                'Cash on Spot',
                'Free Towing',
                'Old Mattress Removal',
                'Salvage Title Processing',
            ],
            'Bail Bonds' => [
                'Residential Surety Bonds',
                'Commercial Property Bonds',
                'Immigration Bonds',
                'Federal Bonds',
                'Online Bail Bonds Services',
                '24/7 Bail Assistance',
                'Domestic Violence Bonds',
                'Appeal Bonds',
            ],
            'Deck and Railing' => [
                'Residential Deck Construction',
                'Commercial Deck Repairs',
                'Railing Installation Services',
                'Custom Deck Designs',
            ],
            'Electricians' => [
                'Residential Electrical Repairs',
                'Commercial Electrical Installations',
                'Lighting Upgrades',
                'Smart Home Wiring',
            ],
            'Flooring' => [
                'Residential Flooring Installation',
                'Commercial Flooring Repairs',
                'Hardwood Floor Refinishing',
                'Tile and Carpet Installation',
            ],
            'Glass & Mirror' => [
                'Residential Glass Replacement',
                'Commercial Window Installations',
                'Custom Mirror Fabrication',
                'Glass Shower Enclosures',
            ],
            'Home Theater Installation' => [
                'Residential Home Theater Setup',
                'Commercial Audio-Visual Installations',
                'Surround Sound Systems',
                'Smart TV Mounting',
            ],
            'Masonry Concrete' => [
                'Residential Masonry Repairs',
                'Commercial Concrete Slabs',
                'Driveway and Patio Installation',
                'Decorative Concrete Services',
            ],
            'Insulation Service' => [
                'Residential Attic Insulation',
                'Commercial Building Insulation',
                'Spray Foam Installation',
                'Insulation Removal Services',
            ],
            'Chimney Sweep' => [
                'Residential Chimney Cleaning',
                'Commercial Fireplace Maintenance',
                'Chimney Repairs',
                'Smoke Ventilation Inspection',
            ],
            'Security Cameras CCTV' => [
                'Residential CCTV Installation',
                'Commercial Security System Integration',
                'Smart Camera Solutions',
                'Surveillance System Upgrades',
            ],
            'Gutter Services' => [
                'Residential Gutter Cleaning',
                'Commercial Gutter Installation',
                'Seamless Gutter Repairs',
                'Downspout Replacement',
            ],
            'Fireplace Services' => [
                'Residential Fireplace Cleaning',
                'Commercial Fireplace Installation',
                'Gas Log Maintenance',
                'Chimney Liner Replacement',
            ],
            'Solar Installation Services' => [
                'Residential Solar Panel Installation',
                'Commercial Solar System Design',
                'Battery Backup Solutions',
                'Solar Maintenance Services',
            ],
            'Home Automation' => [
                'Residential Smart Home Integration',
                'Commercial Automation Solutions',
                'Lighting and Climate Control Systems',
                'Security Automation',
            ],
            'Pressure Washing' => [
                'Residential Driveway Cleaning',
                'Commercial Building Washing',
                'Deck and Patio Cleaning',
                'Roof Cleaning Services',
            ],
            'Towing Service' => [
                'Emergency Roadside Assistance',
                'Long-Distance Towing',
                'Winch Out Services',
                'Car Lockout',
                'Fuel Delivery',
                'Jump Start',
                'Tire Change',
                'Battery Services',
                'Local Towing',
            ],
            'Auto Detailing' => [
                'Residential Car Detailing',
                'Commercial Fleet Cleaning',
                'Interior and Exterior Detailing',
                'Paint Protection Services',
            ],
            'Window Tinting' => [
                'Residential Window Tinting',
                'Commercial Glass Tinting',
                'Automotive Window Tinting',
                'UV Protection Films',
            ],
            'Fences and Gates' => [
                'Residential Fence Installation',
                'Commercial Security Gates',
                'Custom Fence Designs',
                'Fence Repairs',
            ],
            'Swimming Pool' => [
                'Residential Pool Maintenance',
                'Commercial Pool Construction',
                'Pool Resurfacing',
                'Spa Installation',
                'Pool Cleaning Services',
            ],
            'Auto Glass' => [
                'Residential Auto Glass Repair',
                'Commercial Fleet Glass Replacement',
                'Windshield Repairs',
                'Side and Rear Glass Installation',
            ],
            'Windows and Doors' => [
                'Residential Window Replacement',
                'Commercial Door Installation',
                'Custom Window Designs',
                'Sliding Door Repairs',
            ],
            'Cabinetry' => [
                'Residential Kitchen Cabinets',
                'Commercial Custom Cabinetry',
                'Cabinet Refacing',
                'Closet Storage Solutions',
            ],
            'Shades & Blinds' => [
                'Residential Window Treatments',
                'Commercial Roller Shades',
                'Smart Blinds Installation',
                'Blackout Shades',
            ],
            'Handyman Services' => [
                'Residential Repairs and Maintenance',
                'Commercial Facility Maintenance',
                'Small Carpentry Projects',
                'Furniture Assembly',
            ],
            'Junk Removal' => [
                'Residential Junk Hauling',
                'Commercial Waste Disposal',
                'Construction Debris Removal',
                'Appliance Recycling',
            ],
            'Countertop Services' => [
                'Residential Countertop Installation',
                'Commercial Countertop Fabrication',
                'Stone and Quartz Polishing',
                'Countertop Repairs',
            ],
            'Drywall Services' => [
                'Residential Drywall Installation',
                'Commercial Drywall Repairs',
                'Ceiling Texture Application',
                'Drywall Finishing',
            ],
            'Dumpster Rental' => [
                'Residential Dumpster Rental',
                'Commercial Dumpster Services',
                'Construction Site Rentals',
                'Yard Waste Disposal',
            ],
            'Real Estate Services' => [
                'Residential Property Listings',
                'Commercial Property Management',
                'Real Estate Consulting',
                'Leasing and Rentals',
            ],
            'Event Management' => [
                'Residential Party Planning',
                'Corporate Event Coordination',
                'Wedding Management Services',
                'Venue Setup and Design',
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
