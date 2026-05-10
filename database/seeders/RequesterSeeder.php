<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Requester;

class RequesterSeeder extends Seeder
{
    public function run(): void
    {
        // These are contact-only requesters (no login account)
        // Staff can create tickets on their behalf
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
            [
                'user_id'    => null,
                'first_name' => 'Lisa',
                'last_name'  => 'Wilson',
                'email'      => 'lisa.wilson@digital.com',
                'phone'      => '+1-555-0108',
                'company'    => 'Digital Company',
            ],
            [
                'user_id'    => null,
                'first_name' => 'James',
                'last_name'  => 'Moore',
                'email'      => 'james.moore@systems.net',
                'phone'      => '+1-555-0109',
                'company'    => 'Systems Network',
            ],
            [
                'user_id'    => null,
                'first_name' => 'Jennifer',
                'last_name'  => 'Taylor',
                'email'      => 'jennifer.t@software.io',
                'phone'      => '+1-555-0110',
                'company'    => 'Software IO',
            ],
            [
                'user_id'    => null,
                'first_name' => 'Christopher',
                'last_name'  => 'Anderson',
                'email'      => 'chris.anderson@cloud.com',
                'phone'      => '+1-555-0111',
                'company'    => 'Cloud Services',
            ],
            [
                'user_id'    => null,
                'first_name' => 'Amanda',
                'last_name'  => 'Thomas',
                'email'      => 'amanda.t@mobile.net',
                'phone'      => '+1-555-0112',
                'company'    => 'Mobile Networks',
            ],
            [
                'user_id'    => null,
                'first_name' => 'Matthew',
                'last_name'  => 'Jackson',
                'email'      => 'matt.jackson@data.io',
                'phone'      => '+1-555-0113',
                'company'    => 'Data Analytics',
            ],
            [
                'user_id'    => null,
                'first_name' => 'Ashley',
                'last_name'  => 'White',
                'email'      => 'ashley.white@security.com',
                'phone'      => '+1-555-0114',
                'company'    => 'Security First',
            ],
            [
                'user_id'    => null,
                'first_name' => 'Daniel',
                'last_name'  => 'Harris',
                'email'      => 'daniel.h@consulting.org',
                'phone'      => '+1-555-0115',
                'company'    => 'Consulting Group',
            ],
        ];

        foreach ($requesters as $requesterData) {
            Requester::create($requesterData);
        }

        $this->command->info('Requesters seeded successfully!');
    }
}