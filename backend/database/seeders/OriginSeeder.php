<?php

namespace Database\Seeders;

use App\Models\Origin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OriginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $origins = [
            [
                'id'          => (string) Str::uuid(),
                'name' => 'Zillow',
                'image' => 'https://logo.clearbit.com/zillow.com',
                'description' => 'Real estate listings and property data from Zillow',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => (string) Str::uuid(),
                'name' => 'Redfin',
                'image' => 'https://logo.clearbit.com/redfin.com',
                'description' => 'Property listings and market data from Redfin',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => (string) Str::uuid(),
                'name' => 'Realtor.com',
                'image' => 'https://logo.clearbit.com/realtor.com',
                'description' => 'Real estate search and property information',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => (string) Str::uuid(),
                'name' => 'Trulia',
                'image' => 'https://logo.clearbit.com/trulia.com',
                'description' => 'Home search and neighborhood insights',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => (string) Str::uuid(),
                'name' => 'Apartments.com',
                'image' => 'https://logo.clearbit.com/apartments.com',
                'description' => 'Apartment and rental property listings',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];
        Origin::insert($origins);
    }
}
