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
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // The three accounts for the demo. They are already approved, so they can log in
        // right away. The password of every account is "password".
        // The project requirements ask for an admin with the email admin@example.com.
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
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

        // Two people who registered with another address and still wait for an admin.
        User::factory()->unapproved()->create([
            'name' => 'Jordi Puig',
            'email' => 'jordi.puig@gmail.com',
        ]);
        User::factory()->unapproved()->create([
            'name' => 'Sofia Martín',
            'email' => 'sofia.martin@harbour.space',
        ]);

        // The categories of the HelpDesk. The CategoryFactory adds the matching description.
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

        // The welcome page may still have the categories of the old database in its cache.
        Cache::forget(Category::HOME_PAGE_CACHE_KEY);

        // 40 tickets reported by the approved students and teachers, spread over all statuses.
        // The TicketFactory picks a realistic problem and the category that fits it.
        $reporters = User::where('role', '!=', Role::Admin)->whereNotNull('approved_at')->get();

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
            $replyCount = fake()->numberBetween(0, 5);
            $writtenAt = $ticket->created_at;

            for ($number = 1; $number <= $replyCount; $number++) {
                // Each reply is written after the one before it, and never in the future.
                $writtenAt = fake()->dateTimeBetween($writtenAt);

                if ($number % 2 === 1) {
                    Reply::factory()->fromStaff()->for($ticket)->create([
                        'user_id' => $admin->id,
                        'created_at' => $writtenAt,
                        'updated_at' => $writtenAt,
                    ]);
                } else {
                    Reply::factory()->for($ticket)->create([
                        'user_id' => $ticket->user_id,
                        'created_at' => $writtenAt,
                        'updated_at' => $writtenAt,
                    ]);
                }
            }
        }
    }
}
