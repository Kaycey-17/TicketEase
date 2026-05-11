<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Requester;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Staff users
        $staffUsers = [
            [
                'name'     => 'Admin User',
                'email'    => 'admin@helpdesk.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'John Supervisor',
                'email'    => 'supervisor@helpdesk.com',
                'password' => Hash::make('password'),
                'role'     => 'supervisor',
            ],
            [
                'name'     => 'Sarah Agent',
                'email'    => 'agent1@helpdesk.com',
                'password' => Hash::make('password'),
                'role'     => 'support_agent',
            ],
            [
                'name'     => 'Mike Agent',
                'email'    => 'agent2@helpdesk.com',
                'password' => Hash::make('password'),
                'role'     => 'support_agent',
            ],
            [
                'name'     => 'Lisa Agent',
                'email'    => 'agent3@helpdesk.com',
                'password' => Hash::make('password'),
                'role'     => 'support_agent',
            ],
        ];

        foreach ($staffUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Requester users
        $requesterUsers = [
            [
                'name'       => 'Bob Requester',
                'email'      => 'requester@helpdesk.com',
                'password'   => Hash::make('password'),
                'role'       => 'requester',
                'first_name' => 'Bob',
                'last_name'  => 'Requester',
                'phone'      => '+1-555-0200',
                'company'    => 'Helpdesk Client',
            ],
            [
                'name'       => 'John Doe',
                'email'      => 'john.doe@company.com',
                'password'   => Hash::make('password'),
                'role'       => 'requester',
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'phone'      => '+1-555-0101',
                'company'    => 'Acme Corporation',
            ],
            [
                'name'       => 'Jane Smith',
                'email'      => 'jane.smith@techcorp.com',
                'password'   => Hash::make('password'),
                'role'       => 'requester',
                'first_name' => 'Jane',
                'last_name'  => 'Smith',
                'phone'      => '+1-555-0102',
                'company'    => 'Tech Corp',
            ],
            [
                'name'       => 'Michael Johnson',
                'email'      => 'michael.j@enterprise.com',
                'password'   => Hash::make('password'),
                'role'       => 'requester',
                'first_name' => 'Michael',
                'last_name'  => 'Johnson',
                'phone'      => '+1-555-0103',
                'company'    => 'Enterprise Solutions',
            ],
            [
                'name'       => 'Emily Williams',
                'email'      => 'emily.w@startup.io',
                'password'   => Hash::make('password'),
                'role'       => 'requester',
                'first_name' => 'Emily',
                'last_name'  => 'Williams',
                'phone'      => '+1-555-0104',
                'company'    => 'Startup.io',
            ],
        ];

        foreach ($requesterUsers as $userData) {

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => $userData['password'],
                    'role'     => 'requester',
                ]
            );

            Requester::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'user_id'    => $user->id,
                    'first_name' => $userData['first_name'],
                    'last_name'  => $userData['last_name'],
                    'phone'      => $userData['phone'],
                    'company'    => $userData['company'],
                ]
            );
        }

        $this->command->info('Users seeded successfully!');
    }
}