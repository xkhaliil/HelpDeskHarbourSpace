<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The pivot table of the many-to-many relationship between tickets and users:
     * which members of the IT staff (admins) handle which ticket.
     * A ticket can be handled by several admins, and an admin handles many tickets.
     * Laravel finds this table by itself, because its name is the two model names
     * in alphabetical order: "ticket" + "user".
     */
    public function up(): void
    {
        Schema::create('ticket_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained();
            $table->foreignId('user_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_user');
    }
};
