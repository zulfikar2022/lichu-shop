<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Division;
use App\Models\Upazila;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(OrderStateSeeder::class);

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@lichu-shop.test'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'is_superadmin' => true,
                'is_active' => true,
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@lichu-shop.test'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'is_admin' => true,
                'is_active' => true,
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@lichu-shop.test'],
            [
                'name' => 'Customer User',
                'password' => 'password',
                'is_customer' => true,
                'is_active' => true,
            ]
        );

        $superAdmin->setRole(User::ROLE_SUPERADMIN);
        $superAdmin->save();

        $admin->setRole(User::ROLE_ADMIN);
        $admin->save();

        $customer->setRole(User::ROLE_CUSTOMER);
        $customer->save();

        $division = Division::firstOrCreate(['name' => 'Dhaka']);
        $district = District::firstOrCreate(['name' => 'Dhaka District', 'division_id' => $division->id]);

        Upazila::firstOrCreate(['name' => 'Dhanmondi', 'district_id' => $district->id]);
        Upazila::firstOrCreate(['name' => 'Gulshan', 'district_id' => $district->id]);
    }
}
