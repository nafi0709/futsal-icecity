<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Field;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        User::create(['name' => 'Admin Ice', 'email' => 'admin@icecity.com', 'password' => bcrypt('password'), 'role' => 'admin']);
        User::create(['name' => 'User Biasa', 'email' => 'user@icecity.com', 'password' => bcrypt('password'), 'role' => 'user']);
        
        Field::create(['name' => 'Lapangan Orbital A', 'price_per_hour' => 150000, 'description' => 'Lapangan sintetis standar dengan pencahayaan neon.', 'is_active' => true]);
        Field::create(['name' => 'Lapangan Cyber B', 'price_per_hour' => 200000, 'description' => 'Lapangan premium dengan pendingin ruangan.', 'is_active' => true]);
    }
}