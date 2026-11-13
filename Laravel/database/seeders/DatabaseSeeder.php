<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GradeSeeder::class,
            UserSeeder::class,
            ProductOwnerSeeder::class,
            AuctionSessionSeeder::class,
            ProductSeeder::class,
            ProductMediaSeeder::class,
            BidSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
