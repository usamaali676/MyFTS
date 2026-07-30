<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Trainee;
use App\Models\TraineeAttendance;
use App\Models\TraineeComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraineeModelTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        Role::firstOrCreate(['id' => 1], ['name' => 'Reserved Admin Placeholder']);
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        return User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    public function test_trainee_has_attendances_and_comments_relations(): void
    {
        $user = $this->makeUser();

        $trainee = Trainee::create([
            'name' => 'Alex Trainee',
            'sudo_options' => true,
            'additional_info' => 'Started this week',
            'created_by_user_id' => $user->id,
        ]);

        $attendance = TraineeAttendance::create([
            'trainee_id' => $trainee->id,
            'date' => '2026-07-30',
            'present' => true,
            'late' => false,
            'created_by_user_id' => $user->id,
        ]);

        $comment = TraineeComment::create([
            'trainee_id' => $trainee->id,
            'user_id' => $user->id,
            'comment' => 'Doing well so far.',
            'comment_date' => '2026-07-30',
        ]);

        $this->assertTrue($trainee->sudo_options);
        $this->assertCount(1, $trainee->fresh()->attendances);
        $this->assertCount(1, $trainee->fresh()->comments);
        $this->assertSame($attendance->trainee->id, $trainee->id);
        $this->assertSame($comment->user->id, $user->id);
    }
}
