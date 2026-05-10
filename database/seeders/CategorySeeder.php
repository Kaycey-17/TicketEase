<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technical Support',
                'description' => 'Hardware, software, and network issues',
                'is_active' => true,
            ],
            [
                'name' => 'Account & Billing',
                'description' => 'Account access, billing questions, and payment issues',
                'is_active' => true,
            ],
            [
                'name' => 'Feature Request',
                'description' => 'Suggestions for new features or improvements',
                'is_active' => true,
            ],
            [
                'name' => 'Bug Report',
                'description' => 'Report software bugs and errors',
                'is_active' => true,
            ],
            [
                'name' => 'General Inquiry',
                'description' => 'General questions and information requests',
                'is_active' => true,
            ],
            [
                'name' => 'Password Reset',
                'description' => 'Help with password and login issues',
                'is_active' => true,
            ],
            [
                'name' => 'Installation Help',
                'description' => 'Assistance with software installation',
                'is_active' => true,
            ],
            [
                'name' => 'Data Migration',
                'description' => 'Help with data import/export and migration',
                'is_active' => true,
            ],
            [
                'name' => 'Training & Documentation',
                'description' => 'Requests for training materials and documentation',
                'is_active' => true,
            ],
            [
                'name' => 'Other',
                'description' => 'Other requests not covered by existing categories',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        $this->command->info('Categories seeded successfully!');
    }
}