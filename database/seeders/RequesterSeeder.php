<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Requester;

class RequesterSeeder extends Seeder
{
    public function run(): void
    {
        $requesters = [
            [
                'user_id'    => null,
                'first_name' => 'David',
                'last_name'  => 'Brown',
                'email'      => 'david.brown@global.net',
                'phone'      => '+1-555-0105',
                'company'    => 'Global Networks',
            ],
            [
                'user_id'    => null,
                'first_name' => 'Sarah',
                'last_name'  => 'Davis',
                'email'      => 'sarah.davis@innovate.com',
                'phone'      => '+1-555-0106',
                'company'    => 'Innovate Inc',
            ],
            [
                'user_id'    => null,
                'first_name' => 'Robert',
                'last_name'  => 'Miller',
                'email'      => 'robert.m@solutions.org',
                'phone'      => '+1-555-0107',
                'company'    => 'Solutions Org',
            ],
        ];

        foreach ($requesters as $requesterData) {

            Requester::firstOrCreate(
                ['email' => $requesterData['email']],
                $requesterData
            );
        }

        $this->command->info('Requesters seeded successfully!');
    }
}