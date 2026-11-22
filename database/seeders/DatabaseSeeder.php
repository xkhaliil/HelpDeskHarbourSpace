<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Reply;
use App\Models\Ticket;
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
        // The three accounts for the demo. The password of every account is "password".
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ]);
        User::factory()->teacher()->create([
            'name' => 'Marta Vidal',
            'email' => 'teacher@harbour.space',
        ]);
        User::factory()->student()->create([
            'name' => 'Lucas Moreau',
            'email' => 'student@harbour.space',
        ]);

        // About 10 extra students and teachers.
        User::factory(7)->student()->create();
        User::factory(3)->teacher()->create();

        // The categories of the HelpDesk.
        $categoryNames = [
            'Wi-Fi & Network',
            'Accounts & Portal',
            'Classroom Equipment',
            'Laptop & Software',
            'Building & Facilities',
        ];
        foreach ($categoryNames as $name) {
            Category::factory()->create(['name' => $name]);
        }

        // 40 tickets reported by the students and teachers, spread over all statuses.
        // The TicketFactory picks a realistic problem and the category that fits it.
        $reporters = User::where('role', '!=', Role::Admin)->get();

        $tickets = Ticket::factory(40)
            ->recycle($reporters)
            ->sequence(
                ['status' => TicketStatus::Open],
                ['status' => TicketStatus::InProgress],
                ['status' => TicketStatus::Solved],
                ['status' => TicketStatus::Closed],
            )
            ->create();

        // 0 to 5 replies per ticket. The admin answers first, then the owner and the admin take turns.
        foreach ($tickets as $ticket) {
            $replyTimes = collect(range(1, fake()->numberBetween(0, 5)))
                ->map(fn () => fake()->dateTimeBetween($ticket->created_at, 'now'))
                ->sort()
                ->values();

            foreach ($replyTimes as $index => $writtenAt) {
                $writtenByAdmin = $index % 2 === 0;

                $factory = $writtenByAdmin ? Reply::factory()->fromStaff() : Reply::factory();

                $factory->for($ticket)->create([
                    'user_id' => $writtenByAdmin ? $admin->id : $ticket->user_id,
                    'created_at' => $writtenAt,
                    'updated_at' => $writtenAt,
                ]);
            }
        }
    }
}
