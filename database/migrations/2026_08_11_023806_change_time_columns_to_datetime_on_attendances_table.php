<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * login_time/logout_time were declared as TIME columns while the app
     * writes full Carbon datetimes into them, silently dropping the date
     * portion and corrupting cross-midnight working_minutes math for the
     * overnight (7 PM - 4 AM) shift. Widen them to DATETIME.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE attendances MODIFY login_time DATETIME NULL');
        DB::statement('ALTER TABLE attendances MODIFY logout_time DATETIME NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE attendances MODIFY login_time TIME NULL');
        DB::statement('ALTER TABLE attendances MODIFY logout_time TIME NULL');
    }
};
