# Trainee Management Module Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add three independent modules — Trainees, Trainee Attendance, Trainee Comments — with zero changes to existing User/Attendance/Comments/Permission logic.

**Architecture:** Standard Laravel MVC matching every existing module in this codebase: `Route::controller(...)->prefix(...)->as('x.')->middleware(PermissionMiddelware::class)->group(...)`, thin controllers with inline `$request->validate()`, Eloquent models, Blade views extending `layouts.dashboard`. Permissions are module-level (`view/create/edit/delete` booleans per role), auto-detected from route names by the existing `GlobalHelper::Permissions()` — no permission seeding code needed.

**Tech Stack:** Laravel, Blade, Bootstrap 5 (Sneat theme), jQuery (for the shared delete-modal handler), `RealRashid\SweetAlert` for flash messages. No new dependencies.

## Global Constraints

- Do not modify `AttendanceController`, `CommentsController`, `UserController`, `PermissionMiddelware`, `GlobalHelper`, or any of their models/migrations/views.
- Only two existing files get additive edits: `routes/web.php` (new route groups appended) and `resources/views/layouts/partials/sidebar.blade.php` (one new gated `<li>` appended).
- Route names must be `{entity}.{operation}` with `{entity}` a single lowercase word (no underscores/hyphens) and `{operation}` one of `index|create|store|edit|update|delete` — anything else silently bypasses the permission check (see `PermissionMiddelware::matchRouteWithPermissionName`).
- Table id `recodetable` and link classes `dropdown-item delete-record` with `data-route="{entity}"` `data-id="{id}"` on any listing page reuse the already-global delete-confirmation modal (`#basicModal` in `layouts/dashboard.blade.php`) — do not write new delete-confirmation JS.
- Every mutating controller action ends with `Alert::success(...)` / `Alert::error(...)` (from `RealRashid\SweetAlert\Facades\Alert`) + `redirect()`, matching every existing controller.
- Spec: `docs/superpowers/specs/2026-07-30-trainee-module-design.md`.

---

### Task 1: Migrations and Models

**Files:**
- Create: `database/migrations/2026_07_30_090000_create_trainees_table.php`
- Create: `database/migrations/2026_07_30_090100_create_trainee_attendances_table.php`
- Create: `database/migrations/2026_07_30_090200_create_trainee_comments_table.php`
- Create: `app/Models/Trainee.php`
- Create: `app/Models/TraineeAttendance.php`
- Create: `app/Models/TraineeComment.php`
- Test: `tests/Feature/TraineeModelTest.php`

**Interfaces:**
- Produces: `Trainee` (fillable `name, sudo_options, additional_info, created_by_user_id, updated_by_user_id, deleted_by_user_id`; relations `createdBy(): BelongsTo`, `attendances(): HasMany`, `comments(): HasMany`; uses `SoftDeletes`), `TraineeAttendance` (fillable `trainee_id, date, present, late, created_by_user_id`; relations `trainee(): BelongsTo`, `createdBy(): BelongsTo`), `TraineeComment` (fillable `trainee_id, user_id, comment, comment_date`; relations `trainee(): BelongsTo`, `user(): BelongsTo`).

- [ ] **Step 1: Write the migrations**

`database/migrations/2026_07_30_090000_create_trainees_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Active/Inactive status flag (not a literal privilege/access flag).
            $table->boolean('sudo_options');
            $table->text('additional_info')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainees');
    }
};
```

`database/migrations/2026_07_30_090100_create_trainee_attendances_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainee_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('trainees')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('present');
            $table->boolean('late');
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['trainee_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainee_attendances');
    }
};
```

`database/migrations/2026_07_30_090200_create_trainee_comments_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainee_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('trainees')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('comment');
            $table->date('comment_date');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainee_comments');
    }
};
```

- [ ] **Step 2: Run migrations**

Run: `php artisan migrate`
Expected: `2026_07_30_090000_create_trainees_table ... DONE`, `..._090100_create_trainee_attendances_table ... DONE`, `..._090200_create_trainee_comments_table ... DONE`

- [ ] **Step 3: Write the models**

`app/Models/Trainee.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sudo_options',
        'additional_info',
        'created_by_user_id',
        'updated_by_user_id',
        'deleted_by_user_id',
    ];

    protected $casts = [
        'sudo_options' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(TraineeAttendance::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TraineeComment::class);
    }
}
```

`app/Models/TraineeAttendance.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraineeAttendance extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'trainee_id',
        'date',
        'present',
        'late',
        'created_by_user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'present' => 'boolean',
        'late' => 'boolean',
    ];

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
```

`app/Models/TraineeComment.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraineeComment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'trainee_id',
        'user_id',
        'comment',
        'comment_date',
    ];

    protected $casts = [
        'comment_date' => 'date',
    ];

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 4: Write the failing/passing relationship test**

`tests/Feature/TraineeModelTest.php`:

```php
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
```

- [ ] **Step 5: Run test**

Run: `php artisan test --filter=TraineeModelTest`
Expected: `Tests: 1 passed`

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_07_30_090000_create_trainees_table.php database/migrations/2026_07_30_090100_create_trainee_attendances_table.php database/migrations/2026_07_30_090200_create_trainee_comments_table.php app/Models/Trainee.php app/Models/TraineeAttendance.php app/Models/TraineeComment.php tests/Feature/TraineeModelTest.php
git commit -m "feat: add Trainee, TraineeAttendance, TraineeComment migrations and models"
```

---

### Task 2: Trainee create flow (controller create/store, routes, create page, sidebar entry)

**Files:**
- Create: `app/Http/Controllers/TraineeController.php`
- Modify: `routes/web.php` (append new route group)
- Modify: `resources/views/layouts/partials/sidebar.blade.php` (append gated nav item)
- Create: `resources/views/pages/trainee/create.blade.php`
- Create: `resources/views/pages/trainee/index.blade.php` (minimal listing so `create` has somewhere to redirect to and link back from; attendance/comment buttons added in later tasks)
- Test: `tests/Feature/TraineeControllerTest.php`

**Interfaces:**
- Consumes: `Trainee` model from Task 1 (`Trainee::create([...])`).
- Produces: routes `trainee.index`, `trainee.create`, `trainee.store` usable by later tasks; `TraineeController` class that Task 3 adds `edit/update/destroy` methods to.

- [ ] **Step 1: Write the failing test**

`tests/Feature/TraineeControllerTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Trainee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraineeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeUserWithPermission(bool $canView, bool $canCreate, bool $canEdit = false, bool $canDelete = false): User
    {
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        Permission::create([
            'role_id' => $role->id,
            'name' => 'trainee',
            'create' => $canCreate ? 1 : 0,
            'view' => $canView ? 1 : 0,
            'edit' => $canEdit ? 1 : 0,
            'delete' => $canDelete ? 1 : 0,
        ]);

        return User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('trainee.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_denied_without_view_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: false, canCreate: false);

        $response = $this->actingAs($user)->get(route('trainee.index'));

        $response->assertRedirect(route('home'));
    }

    public function test_index_allowed_with_view_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: false);

        $response = $this->actingAs($user)->get(route('trainee.index'));

        $response->assertOk();
    }

    public function test_store_creates_trainee_for_authorized_user(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);

        $response = $this->actingAs($user)->post(route('trainee.store'), [
            'name' => 'Alex Trainee',
            'sudo_options' => '1',
            'additional_info' => 'Started this week',
        ]);

        $response->assertRedirect(route('trainee.index'));
        $this->assertDatabaseHas('trainees', [
            'name' => 'Alex Trainee',
            'sudo_options' => 1,
            'created_by_user_id' => $user->id,
        ]);
    }

    public function test_store_denied_without_create_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: false);

        $response = $this->actingAs($user)->post(route('trainee.store'), [
            'name' => 'Alex Trainee',
            'sudo_options' => '1',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseCount('trainees', 0);
    }

    public function test_store_requires_name(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);

        $response = $this->actingAs($user)->post(route('trainee.store'), [
            'sudo_options' => '1',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=TraineeControllerTest`
Expected: FAIL — `route('trainee.index')` / `route('trainee.store')` not defined (route `[trainee.index] not defined`).

- [ ] **Step 3: Write the controller**

`app/Http/Controllers/TraineeController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TraineeController extends Controller
{
    public function index()
    {
        $trainees = Trainee::with('createdBy')->orderBy('id', 'desc')->get();
        $srno = 1;

        return view('pages.trainee.index', compact('trainees', 'srno'));
    }

    public function create()
    {
        return view('pages.trainee.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sudo_options' => 'required|boolean',
            'additional_info' => 'nullable|string',
        ]);

        Trainee::create([
            'name' => $request->name,
            'sudo_options' => $request->boolean('sudo_options'),
            'additional_info' => $request->additional_info,
            'created_by_user_id' => Auth::id(),
        ]);

        Alert::success('Success', 'Trainee Added Successfully');

        return redirect()->route('trainee.index');
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
```

- [ ] **Step 4: Add the route group**

In `routes/web.php`, add `use App\Http\Controllers\TraineeController;` to the `use` block at the top (alongside the other controller imports), then append this group after the `calltranscription` group at the end of the file:

```php
    Route::controller(TraineeController::class)
    ->prefix('trainee')
    ->as('trainee.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('delete/{id}', 'destroy')->name('delete');
    });
```

- [ ] **Step 5: Write the minimal index view**

`resources/views/pages/trainee/index.blade.php`:

```blade
@extends('layouts.dashboard')
@section('content')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="d-flex justify-content-between align-items-center">
                <h4 class="py-3 mb-4"><span class="text-muted fw-light">Trainee/</span> List</h4>
                <a href="{{ route('trainee.create') }}" class="btn btn-primary">Create Trainee</a>
              </div>

              <div class="card">
                <h5 class="card-header">Trainees</h5>
                <div class="card-datatable table-responsive">
                  <table id="recodetable" class="table table-bordered">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($trainees as $item)
                          <tr>
                            <td>{{ $srno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>
                                @if($item->sudo_options)
                                    <span class="badge rounded-pill bg-success">Active</span>
                                @else
                                    <span class="badge rounded-pill bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $item->createdBy->name ?? '-' }}</td>
                            <td>
                                <div class="d-inline-block text-nowrap">
                                    <button
                                    class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                    <div class="dropdown-menu dropdown-menu-end m-0">
                                        <a href="{{ route('trainee.edit', $item->id) }}" class="dropdown-item"><i class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                        <a href="javascript:;"
                                        data-id="{{ $item->id }}"
                                        data-route="trainee"
                                        data-bs-toggle="modal"
                                        data-bs-target="#basicModal"
                                        class="dropdown-item delete-record"><i class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                    </div>
                                </div>
                            </td>
                          </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="content-backdrop fade"></div>
          </div>
@endsection
```

- [ ] **Step 6: Write the create view**

`resources/views/pages/trainee/create.blade.php`:

```blade
@extends('layouts.dashboard')
@section('content')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="py-3 mb-4"><span class="text-muted fw-light">Trainee/</span> Create</h4>

              <div class="card mb-4">
                <form class="card-body" action="{{ route('trainee.store') }}" method="POST">
                  @csrf
                  @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                  @endif
                  <div class="row g-4">
                    <div class="col-md-6">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" placeholder="Alex Trainee" />
                        <label for="name">Full Name</label>
                      </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="row">
                            <div class="col-md mb-md-0 mb-5">
                            <div class="form-check custom-option custom-option-basic checked">
                                <label class="form-check-label custom-option-content" for="sudo_options_1">
                                <input class="form-check-input" name="sudo_options" type="radio" value="1" id="sudo_options_1" checked>
                                <span class="custom-option-header">
                                    <span class="h6 mb-0">Active</span>
                                </span>
                                </label>
                            </div>
                            </div>
                            <div class="col-md">
                            <div class="form-check custom-option custom-option-basic">
                                <label class="form-check-label custom-option-content" for="sudo_options_0">
                                <input class="form-check-input" name="sudo_options" type="radio" value="0" id="sudo_options_0">
                                <span class="custom-option-header">
                                    <span class="h6 mb-0">Inactive</span>
                                </span>
                                </label>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                      <div class="form-floating form-floating-outline">
                        <textarea name="additional_info" id="additional_info" class="form-control" style="height: 100px" placeholder="Notes">{{ old('additional_info') }}</textarea>
                        <label for="additional_info">Additional Info</label>
                      </div>
                    </div>
                  </div>
                  <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <a href="{{ route('trainee.index') }}" class="btn btn-outline-secondary">Cancel</a>
                  </div>
                </form>
              </div>
            </div>
            <div class="content-backdrop fade"></div>
          </div>
@endsection
```

- [ ] **Step 7: Add the sidebar entry**

In `resources/views/layouts/partials/sidebar.blade.php`, add to the `@php` block at the top (alongside `$attendance_perm`, `$calltranscription_perm`):

```php
    $trainee_perm = App\Models\Permission::where('role_id', $user->role_id)->where('name', "trainee")->first();
```

Then append this block right after the `calltranscription` nav `@endif` (before the closing `</ul>`):

```blade
        @if(isset($trainee_perm) && $trainee_perm->view == 1)
        <li class="menu-item">
            <a href="{{ route('trainee.index') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-school-outline"></i>
                <div data-i18n="Trainees">Trainees</div>
            </a>
        </li>
        @endif
```

- [ ] **Step 8: Run tests to verify they pass**

Run: `php artisan test --filter=TraineeControllerTest`
Expected: `Tests: 6 passed`

- [ ] **Step 9: Run the full existing suite to confirm no regressions**

Run: `php artisan test`
Expected: all previously-passing tests still pass (no failures introduced).

- [ ] **Step 10: Commit**

```bash
git add app/Http/Controllers/TraineeController.php routes/web.php resources/views/layouts/partials/sidebar.blade.php resources/views/pages/trainee/create.blade.php resources/views/pages/trainee/index.blade.php tests/Feature/TraineeControllerTest.php
git commit -m "feat: add Trainee index/create/store with permission-gated sidebar entry"
```

---

### Task 3: Trainee edit/update/delete

**Files:**
- Modify: `app/Http/Controllers/TraineeController.php` (fill in `edit`, `update`, `destroy`)
- Create: `resources/views/pages/trainee/edit.blade.php`
- Modify: `tests/Feature/TraineeControllerTest.php` (append edit/update/delete tests)

**Interfaces:**
- Consumes: `Trainee` model, routes `trainee.edit`, `trainee.update`, `trainee.delete` (already registered in Task 2).
- Produces: fully working Trainee CRUD, ready for Tasks 4/5 to add row-level actions to `trainee/index.blade.php`.

- [ ] **Step 1: Append the failing tests**

Add to `tests/Feature/TraineeControllerTest.php` (inside the class, after `test_store_requires_name`):

```php
    public function test_update_edits_trainee_for_authorized_user(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true, canEdit: true);
        $trainee = Trainee::create([
            'name' => 'Old Name',
            'sudo_options' => true,
            'created_by_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('trainee.update', $trainee->id), [
            'name' => 'New Name',
            'sudo_options' => '0',
            'additional_info' => 'Updated notes',
        ]);

        $response->assertRedirect(route('trainee.index'));
        $this->assertDatabaseHas('trainees', [
            'id' => $trainee->id,
            'name' => 'New Name',
            'sudo_options' => 0,
            'updated_by_user_id' => $user->id,
        ]);
    }

    public function test_update_denied_without_edit_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true, canEdit: false);
        $trainee = Trainee::create([
            'name' => 'Old Name',
            'sudo_options' => true,
            'created_by_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('trainee.update', $trainee->id), [
            'name' => 'New Name',
            'sudo_options' => '0',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('trainees', ['id' => $trainee->id, 'name' => 'Old Name']);
    }

    public function test_delete_soft_deletes_trainee_for_authorized_user(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true, canDelete: true);
        $trainee = Trainee::create([
            'name' => 'To Delete',
            'sudo_options' => true,
            'created_by_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('trainee.delete', $trainee->id));

        $response->assertRedirect(route('trainee.index'));
        $this->assertSoftDeleted('trainees', ['id' => $trainee->id]);
    }

    public function test_delete_denied_without_delete_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true, canDelete: false);
        $trainee = Trainee::create([
            'name' => 'To Delete',
            'sudo_options' => true,
            'created_by_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('trainee.delete', $trainee->id));

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('trainees', ['id' => $trainee->id, 'deleted_at' => null]);
    }
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=TraineeControllerTest`
Expected: FAIL — `edit`/`update`/`destroy` are empty methods, so update won't persist changes and delete won't soft-delete (assertions fail).

- [ ] **Step 3: Implement edit/update/destroy**

In `app/Http/Controllers/TraineeController.php`, replace the three empty methods:

```php
    public function edit($id)
    {
        $trainee = Trainee::findOrFail($id);

        return view('pages.trainee.edit', compact('trainee'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sudo_options' => 'required|boolean',
            'additional_info' => 'nullable|string',
        ]);

        $trainee = Trainee::findOrFail($id);
        $trainee->name = $request->name;
        $trainee->sudo_options = $request->boolean('sudo_options');
        $trainee->additional_info = $request->additional_info;
        $trainee->updated_by_user_id = Auth::id();
        $trainee->save();

        Alert::success('Success', 'Trainee Updated Successfully');

        return redirect()->route('trainee.index');
    }

    public function destroy($id)
    {
        $trainee = Trainee::findOrFail($id);
        $trainee->deleted_by_user_id = Auth::id();
        $trainee->save();
        $trainee->delete();

        Alert::success('Success', 'Trainee Deleted Successfully');

        return redirect()->route('trainee.index');
    }
```

- [ ] **Step 4: Write the edit view**

`resources/views/pages/trainee/edit.blade.php` (copy of `create.blade.php` with pre-filled values and the update route/method):

```blade
@extends('layouts.dashboard')
@section('content')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="py-3 mb-4"><span class="text-muted fw-light">Trainee/</span> Edit</h4>

              <div class="card mb-4">
                <form class="card-body" action="{{ route('trainee.update', $trainee->id) }}" method="POST">
                  @csrf
                  @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                  @endif
                  <div class="row g-4">
                    <div class="col-md-6">
                      <div class="form-floating form-floating-outline">
                        <input type="text" name="name" id="name" value="{{ old('name', $trainee->name) }}" class="form-control" placeholder="Alex Trainee" />
                        <label for="name">Full Name</label>
                      </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="row">
                            <div class="col-md mb-md-0 mb-5">
                            <div class="form-check custom-option custom-option-basic checked">
                                <label class="form-check-label custom-option-content" for="sudo_options_1">
                                <input class="form-check-input" name="sudo_options" type="radio" value="1" id="sudo_options_1" @if($trainee->sudo_options) checked @endif>
                                <span class="custom-option-header">
                                    <span class="h6 mb-0">Active</span>
                                </span>
                                </label>
                            </div>
                            </div>
                            <div class="col-md">
                            <div class="form-check custom-option custom-option-basic">
                                <label class="form-check-label custom-option-content" for="sudo_options_0">
                                <input class="form-check-input" name="sudo_options" type="radio" value="0" id="sudo_options_0" @if(!$trainee->sudo_options) checked @endif>
                                <span class="custom-option-header">
                                    <span class="h6 mb-0">Inactive</span>
                                </span>
                                </label>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                      <div class="form-floating form-floating-outline">
                        <textarea name="additional_info" id="additional_info" class="form-control" style="height: 100px" placeholder="Notes">{{ old('additional_info', $trainee->additional_info) }}</textarea>
                        <label for="additional_info">Additional Info</label>
                      </div>
                    </div>
                  </div>
                  <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <a href="{{ route('trainee.index') }}" class="btn btn-outline-secondary">Cancel</a>
                  </div>
                </form>
              </div>
            </div>
            <div class="content-backdrop fade"></div>
          </div>
@endsection
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=TraineeControllerTest`
Expected: `Tests: 10 passed`

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/TraineeController.php resources/views/pages/trainee/edit.blade.php tests/Feature/TraineeControllerTest.php
git commit -m "feat: add Trainee edit/update/delete"
```

---

### Task 4: Trainee Attendance (Mark Attendance modal)

**Files:**
- Create: `app/Http/Controllers/TraineeAttendanceController.php`
- Modify: `routes/web.php` (append `traineeattendance` route group)
- Modify: `resources/views/pages/trainee/index.blade.php` (add Mark Attendance button + per-row modal)
- Test: `tests/Feature/TraineeAttendanceControllerTest.php`

**Interfaces:**
- Consumes: `Trainee`, `TraineeAttendance` models (Task 1); `trainee/index.blade.php` (Task 2).
- Produces: route `traineeattendance.store`.

- [ ] **Step 1: Write the failing test**

`tests/Feature/TraineeAttendanceControllerTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Trainee;
use App\Models\TraineeAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraineeAttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeUserWithPermission(bool $canCreate): User
    {
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        Permission::create([
            'role_id' => $role->id,
            'name' => 'traineeattendance',
            'create' => $canCreate ? 1 : 0,
            'view' => 1,
            'edit' => 0,
            'delete' => 0,
        ]);

        return User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    private function makeTrainee(User $user): Trainee
    {
        return Trainee::create([
            'name' => 'Alex Trainee',
            'sudo_options' => true,
            'created_by_user_id' => $user->id,
        ]);
    }

    public function test_marking_on_time_sets_present_true_late_false(): void
    {
        $user = $this->makeUserWithPermission(canCreate: true);
        $trainee = $this->makeTrainee($user);

        $response = $this->actingAs($user)->post(route('traineeattendance.store'), [
            'trainee_id' => $trainee->id,
            'date' => '2026-07-30',
            'status' => 'on_time',
        ]);

        $response->assertRedirect(route('trainee.index'));
        $this->assertDatabaseHas('trainee_attendances', [
            'trainee_id' => $trainee->id,
            'date' => '2026-07-30',
            'present' => 1,
            'late' => 0,
        ]);
    }

    public function test_marking_late_sets_present_true_late_true(): void
    {
        $user = $this->makeUserWithPermission(canCreate: true);
        $trainee = $this->makeTrainee($user);

        $this->actingAs($user)->post(route('traineeattendance.store'), [
            'trainee_id' => $trainee->id,
            'date' => '2026-07-30',
            'status' => 'late',
        ]);

        $this->assertDatabaseHas('trainee_attendances', [
            'trainee_id' => $trainee->id,
            'present' => 1,
            'late' => 1,
        ]);
    }

    public function test_marking_absent_sets_present_false_late_false(): void
    {
        $user = $this->makeUserWithPermission(canCreate: true);
        $trainee = $this->makeTrainee($user);

        $this->actingAs($user)->post(route('traineeattendance.store'), [
            'trainee_id' => $trainee->id,
            'date' => '2026-07-30',
            'status' => 'absent',
        ]);

        $this->assertDatabaseHas('trainee_attendances', [
            'trainee_id' => $trainee->id,
            'present' => 0,
            'late' => 0,
        ]);
    }

    public function test_marking_attendance_twice_for_same_date_updates_not_duplicates(): void
    {
        $user = $this->makeUserWithPermission(canCreate: true);
        $trainee = $this->makeTrainee($user);

        $this->actingAs($user)->post(route('traineeattendance.store'), [
            'trainee_id' => $trainee->id,
            'date' => '2026-07-30',
            'status' => 'on_time',
        ]);
        $this->actingAs($user)->post(route('traineeattendance.store'), [
            'trainee_id' => $trainee->id,
            'date' => '2026-07-30',
            'status' => 'absent',
        ]);

        $this->assertDatabaseCount('trainee_attendances', 1);
        $this->assertDatabaseHas('trainee_attendances', [
            'trainee_id' => $trainee->id,
            'present' => 0,
            'late' => 0,
        ]);
    }

    public function test_store_denied_without_create_permission(): void
    {
        $user = $this->makeUserWithPermission(canCreate: false);
        $trainee = $this->makeTrainee($user);

        $response = $this->actingAs($user)->post(route('traineeattendance.store'), [
            'trainee_id' => $trainee->id,
            'date' => '2026-07-30',
            'status' => 'on_time',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseCount('trainee_attendances', 0);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=TraineeAttendanceControllerTest`
Expected: FAIL — route `traineeattendance.store` not defined.

- [ ] **Step 3: Write the controller**

`app/Http/Controllers/TraineeAttendanceController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\TraineeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TraineeAttendanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'date' => 'required|date',
            'status' => 'required|in:on_time,late,absent',
        ]);

        [$present, $late] = match ($request->status) {
            'on_time' => [true, false],
            'late' => [true, true],
            'absent' => [false, false],
        };

        TraineeAttendance::updateOrCreate(
            ['trainee_id' => $request->trainee_id, 'date' => $request->date],
            ['present' => $present, 'late' => $late, 'created_by_user_id' => Auth::id()]
        );

        Alert::success('Success', 'Attendance Saved Successfully');

        return redirect()->route('trainee.index');
    }
}
```

- [ ] **Step 4: Add the route group**

In `routes/web.php`, add `use App\Http\Controllers\TraineeAttendanceController;` to the imports, then append after the `trainee` group:

```php
    Route::controller(TraineeAttendanceController::class)
    ->prefix('trainee-attendance')
    ->as('traineeattendance.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
    });
```

- [ ] **Step 5: Add the Mark Attendance button and modal to the listing page**

In `resources/views/pages/trainee/index.blade.php`, add the button inside the dropdown-menu (right after the Delete link, still inside `<div class="dropdown-menu ...">`):

```blade
                                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#markAttendance{{ $item->id }}" class="dropdown-item"><i class="mdi mdi-calendar-check-outline me-2"></i><span>Mark Attendance</span></a>
```

Then, right after the closing `</tr>` of the trainee's `@foreach` loop body (i.e. immediately after each row, still inside the loop, before `@endforeach`), add a per-trainee modal:

```blade
                          <div class="modal fade" id="markAttendance{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Mark Attendance — {{ $item->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('traineeattendance.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="trainee_id" value="{{ $item->id }}">
                                        <input type="hidden" name="date" value="{{ now()->toDateString() }}">
                                        <div class="modal-body">
                                            <p class="mb-3">Mark attendance for today ({{ now()->format('M d, Y') }}):</p>
                                            <div class="d-flex gap-2">
                                                <button type="submit" name="status" value="on_time" class="btn btn-success flex-fill">On-Time</button>
                                                <button type="submit" name="status" value="late" class="btn btn-warning flex-fill">Late</button>
                                                <button type="submit" name="status" value="absent" class="btn btn-danger flex-fill">Absent</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                          </div>
```

- [ ] **Step 6: Run tests to verify they pass**

Run: `php artisan test --filter=TraineeAttendanceControllerTest`
Expected: `Tests: 5 passed`

- [ ] **Step 7: Run the full suite**

Run: `php artisan test`
Expected: all previously-passing tests still pass.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/TraineeAttendanceController.php routes/web.php resources/views/pages/trainee/index.blade.php tests/Feature/TraineeAttendanceControllerTest.php
git commit -m "feat: add Trainee Attendance mark on-time/late/absent"
```

---

### Task 5: Trainee Comments (dedicated page + Add Comment modal)

**Files:**
- Create: `app/Http/Controllers/TraineeCommentController.php`
- Modify: `routes/web.php` (append `traineecomment` route group)
- Create: `resources/views/pages/trainee-comment/index.blade.php`
- Modify: `resources/views/pages/trainee/index.blade.php` (add Comments button)
- Test: `tests/Feature/TraineeCommentControllerTest.php`

**Interfaces:**
- Consumes: `Trainee`, `TraineeComment` models (Task 1).
- Produces: routes `traineecomment.index`, `traineecomment.store`.

- [ ] **Step 1: Write the failing test**

`tests/Feature/TraineeCommentControllerTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Trainee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraineeCommentControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeUserWithPermission(bool $canView, bool $canCreate): User
    {
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        Permission::create([
            'role_id' => $role->id,
            'name' => 'traineecomment',
            'create' => $canCreate ? 1 : 0,
            'view' => $canView ? 1 : 0,
            'edit' => 0,
            'delete' => 0,
        ]);

        return User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    private function makeTrainee(User $user): Trainee
    {
        return Trainee::create([
            'name' => 'Alex Trainee',
            'sudo_options' => true,
            'created_by_user_id' => $user->id,
        ]);
    }

    public function test_index_denied_without_view_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: false, canCreate: false);
        $trainee = $this->makeTrainee($user);

        $response = $this->actingAs($user)->get(route('traineecomment.index', $trainee->id));

        $response->assertRedirect(route('home'));
    }

    public function test_index_shows_comments_for_trainee(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);
        $trainee = $this->makeTrainee($user);
        $this->actingAs($user)->post(route('traineecomment.store'), [
            'trainee_id' => $trainee->id,
            'comment' => 'Great progress today.',
        ]);

        $response = $this->actingAs($user)->get(route('traineecomment.index', $trainee->id));

        $response->assertOk();
        $response->assertSee('Great progress today.');
        $response->assertSee($user->name);
    }

    public function test_store_creates_comment_with_author_and_date(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);
        $trainee = $this->makeTrainee($user);

        $response = $this->actingAs($user)->post(route('traineecomment.store'), [
            'trainee_id' => $trainee->id,
            'comment' => 'Great progress today.',
        ]);

        $response->assertRedirect(route('traineecomment.index', $trainee->id));
        $this->assertDatabaseHas('trainee_comments', [
            'trainee_id' => $trainee->id,
            'user_id' => $user->id,
            'comment' => 'Great progress today.',
            'comment_date' => now()->toDateString(),
        ]);
    }

    public function test_store_denied_without_create_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: false);
        $trainee = $this->makeTrainee($user);

        $response = $this->actingAs($user)->post(route('traineecomment.store'), [
            'trainee_id' => $trainee->id,
            'comment' => 'Great progress today.',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseCount('trainee_comments', 0);
    }

    public function test_store_requires_non_empty_comment(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);
        $trainee = $this->makeTrainee($user);

        $response = $this->actingAs($user)->post(route('traineecomment.store'), [
            'trainee_id' => $trainee->id,
            'comment' => '',
        ]);

        $response->assertSessionHasErrors('comment');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=TraineeCommentControllerTest`
Expected: FAIL — routes `traineecomment.index` / `traineecomment.store` not defined.

- [ ] **Step 3: Write the controller**

`app/Http/Controllers/TraineeCommentController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use App\Models\TraineeComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TraineeCommentController extends Controller
{
    public function index($trainee)
    {
        $trainee = Trainee::findOrFail($trainee);
        $comments = $trainee->comments()->with('user')->orderBy('created_at', 'desc')->get();

        return view('pages.trainee-comment.index', compact('trainee', 'comments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'comment' => 'required|string',
        ]);

        TraineeComment::create([
            'trainee_id' => $request->trainee_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'comment_date' => now()->toDateString(),
        ]);

        Alert::success('Success', 'Comment Added Successfully');

        return redirect()->route('traineecomment.index', $request->trainee_id);
    }
}
```

- [ ] **Step 4: Add the route group**

In `routes/web.php`, add `use App\Http\Controllers\TraineeCommentController;` to the imports, then append after the `traineeattendance` group:

```php
    Route::controller(TraineeCommentController::class)
    ->prefix('trainee-comment')
    ->as('traineecomment.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('{trainee}', 'index')->name('index');
        Route::post('store', 'store')->name('store');
    });
```

- [ ] **Step 5: Write the comments page**

`resources/views/pages/trainee-comment/index.blade.php`:

```blade
@extends('layouts.dashboard')
@section('content')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="d-flex justify-content-between align-items-center">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Trainee/{{ $trainee->name }}/</span> Comments
                </h4>
                <div>
                    <a href="{{ route('trainee.index') }}" class="btn btn-outline-secondary me-2">Back to Trainees</a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addComment">Add Comment</button>
                </div>
              </div>

              <div class="card">
                <div class="card-body">
                    @forelse ($comments as $comment)
                        <div class="d-flex align-items-start mb-4 pb-4 border-bottom">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">{{ $comment->user->name ?? 'Unknown' }}</h6>
                                    <small class="text-muted">{{ $comment->comment_date->format('M d, Y') }}</small>
                                </div>
                                <p class="mb-0">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No comments yet.</p>
                    @endforelse
                </div>
              </div>
            </div>
            <div class="content-backdrop fade"></div>
          </div>

          <div class="modal fade" id="addComment" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('traineecomment.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="trainee_id" value="{{ $trainee->id }}">
                        <div class="modal-body">
                            <div class="form-floating form-floating-outline">
                                <textarea name="comment" id="comment" class="form-control" style="height: 120px" placeholder="Comment"></textarea>
                                <label for="comment">Comment</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Comment</button>
                        </div>
                    </form>
                </div>
            </div>
          </div>
@endsection
```

- [ ] **Step 6: Add the Comments button to the trainee listing**

In `resources/views/pages/trainee/index.blade.php`, add inside the dropdown-menu, right after the Mark Attendance link:

```blade
                                        <a href="{{ route('traineecomment.index', $item->id) }}" class="dropdown-item"><i class="mdi mdi-comment-text-outline me-2"></i><span>Comments</span></a>
```

- [ ] **Step 7: Run tests to verify they pass**

Run: `php artisan test --filter=TraineeCommentControllerTest`
Expected: `Tests: 5 passed`

- [ ] **Step 8: Run the full suite**

Run: `php artisan test`
Expected: all previously-passing tests still pass.

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/TraineeCommentController.php routes/web.php resources/views/pages/trainee-comment/index.blade.php resources/views/pages/trainee/index.blade.php tests/Feature/TraineeCommentControllerTest.php
git commit -m "feat: add Trainee Comments page and Add Comment modal"
```

---

### Task 6: Final regression and isolation verification

**Files:**
- None created/modified — verification only.

**Interfaces:**
- None.

- [ ] **Step 1: Run the entire test suite**

Run: `php artisan test`
Expected: 0 failures, including every pre-existing test file (`AttendanceController` has no test file to check, but `CallTranscription*Test`, `user`-related tests if any, `Role`-related tests if any — whatever exists must all still pass).

- [ ] **Step 2: Confirm only the intended existing files were touched**

Run: `git status --short`
Expected output shows only:
```
M routes/web.php
M resources/views/layouts/partials/sidebar.blade.php
?? app/Http/Controllers/TraineeController.php
?? app/Http/Controllers/TraineeAttendanceController.php
?? app/Http/Controllers/TraineeCommentController.php
?? app/Models/Trainee.php
?? app/Models/TraineeAttendance.php
?? app/Models/TraineeComment.php
?? database/migrations/2026_07_30_090000_create_trainees_table.php
?? database/migrations/2026_07_30_090100_create_trainee_attendances_table.php
?? database/migrations/2026_07_30_090200_create_trainee_comments_table.php
?? resources/views/pages/trainee/
?? resources/views/pages/trainee-comment/
?? tests/Feature/TraineeModelTest.php
?? tests/Feature/TraineeControllerTest.php
?? tests/Feature/TraineeAttendanceControllerTest.php
?? tests/Feature/TraineeCommentControllerTest.php
```
(plus the already-committed spec/plan docs from earlier). If any other existing file shows as modified, stop and investigate before proceeding — that would violate the "no existing module changes" constraint.

- [ ] **Step 3: Manual verification checklist**

- **Important operational step, do this first:** `PermissionMiddelware` denies access whenever no `Permission` row exists at all for a role+module (not just when it's `0`) — see `PermissionMiddelware::handle()`'s `else` branch. `Permission` rows for the 3 new modules only get created when `RoleController::update()` runs (it loops over `GlobalHelper::Permissions()`, which will now include `trainee`/`traineeattendance`/`traineecomment` since their routes exist). So: open **Role & Permissions → Edit** on the role you'll test with, toggle the desired checkboxes for Trainee/Traineeattendance/Traineecomment (they'll appear automatically in the matrix), and Save — once per role — before the checks below will work. This is not specific to this feature; it's how every existing module's permissions get activated for a role the first time.
- Log in as a role with all three new permissions enabled (or role_id 1, which the app treats as super-admin in some views — verify against `role_id == 1` checks in `sidebar.blade.php`).
- Confirm "Trainees" appears in the sidebar; click through to the list (empty state renders without errors).
- Create a trainee, confirm it appears in the list with correct Active/Inactive badge.
- Edit the trainee, toggle status, save, confirm the badge updates.
- Mark Attendance On-Time, then Late, then Absent for the same trainee/day — confirm only one `trainee_attendances` row exists each time (via `php artisan tinker` → `TraineeAttendance::count()`).
- Click Comments, add a comment, confirm it appears with the correct author name and today's date.
- Delete the trainee via the shared delete modal, confirm it disappears from the list (soft-deleted, not visible).
- Log in as a role with the permission toggles OFF for `trainee`/`traineeattendance`/`traineecomment`, confirm the sidebar entry is hidden and direct navigation to `trainee.index`/`traineecomment.index` redirects home with the existing "You can't perform this operation" alert.
- Separately, log in and confirm existing modules are unaffected: `attendance.index`, `user.index`, `lead.index`, Role edit screen (new `Trainee`/`Traineeattendance`/`Traineecomment` toggles appear there automatically) all still work as before.

- [ ] **Step 4: Final commit (if any stray fixups were needed)**

```bash
git status --short
git add -A
git commit -m "chore: trainee module regression verification"
```
(Skip this step entirely if there is nothing to commit — Steps 1–3 are verification only.)
