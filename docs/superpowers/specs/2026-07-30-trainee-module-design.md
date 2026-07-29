# Trainee Management Module — Design

Date: 2026-07-30
Status: Approved by user, pending spec review

## Goal

Three new, fully independent CRM modules — **Trainees**, **Trainee
Attendance**, **Trainee Comments** — for tracking trainees who are not
system Users. Zero modifications to existing User, Attendance, Comments,
or Permission logic; existing modules only gain new *rows* (permissions)
and *route groups*, never edited application logic.

## Constraints discovered during exploration

- Stack is Laravel + Blade + vanilla JS + Bootstrap 5 (Sneat admin theme),
  same as the rest of the app.
- Route naming convention: `Route::controller(X::class)->prefix('x')->as('x.')
  ->middleware(PermissionMiddelware::class)->group(...)` with route names
  `index/create/store/edit/update/conf-delete/delete`, matching every
  existing module (`lead.`, `user.`, `attendance.`, `calltranscription.`).
- **Permission system is module-level, not granular strings.** One
  `Permission` row per `(role_id, name)` with `create/view/edit/delete`
  booleans. `GlobalHelper::Permissions()` derives the list of permission
  "modules" dynamically by scanning all registered route names and
  stripping the trailing operation — registering `trainee.*`,
  `traineeattendance.*`, `traineecomment.*` route groups makes those three
  modules auto-appear on the Role edit screen. **No manual seeding, no
  changes to `GlobalHelper` or `PermissionMiddelware` needed.**
- `PermissionMiddelware::matchRouteWithPermissionName()` only maps these
  literal route-name suffixes to a permission check: `index` → `view`,
  `create`/`store` → `create`, `edit`/`update` → `edit`,
  `conf-delete`/`delete` → `delete`. A route literally named `.view` would
  match the regex but hit the `default: false` case in the switch and
  always deny — so the listing route must be named `index`, not `view`,
  despite the underlying permission column being called `view`. This
  means the originally-requested permission strings (`trainee.view`,
  `trainee.create`, …) don't map 1:1 onto how this app's middleware
  actually works; module-level permissions (confirmed with user) are used
  instead.
- `show`/`detail` routes are **not** enforced by the middleware in any
  existing module (`detail`/`show` aren't in its recognized operation
  list) — this is pre-existing, consistent behavior across the app, not
  something this feature needs to fix.
- Every core entity (`User`, `Lead`, `Role`, `Sale`) uses `SoftDeletes`
  plus `created_by`/`updated_by`/`deleted_by` audit columns. Trainee
  follows the same convention for consistency, even though the
  originally-requested field list didn't include them.
- Role's create/edit is a **modal** (few fields); User's is **dedicated
  pages** (more fields, password handling). User chose dedicated pages
  for Trainee create/edit.
- `RealRashid\SweetAlert` is the existing flash/alert mechanism
  (`Alert::success(...)`, `Alert::error(...)`) — used for all
  create/update/delete feedback across the app; reused here, not
  replaced with a new alert library.
- Sidebar nav gating pattern (`sidebar.blade.php`):
  ```php
  $x_perm = App\Models\Permission::where('role_id', $user->role_id)->where('name', "x")->first();
  ...
  @if(isset($x_perm) && $x_perm->view == 1)
      <li class="menu-item">...</li>
  @endif
  ```
  Reused verbatim for the new "Trainees" entry (Trainee Attendance and
  Trainee Comments are reached *from* the Trainees page, not separate
  top-level nav items).

## Decisions (confirmed with user)

1. **`sudo_options` field** = trainee active/inactive status (boolean).
   Column name kept as `sudo_options` per the original field list, with
   clear doc-comments in the model/migration explaining it's a status
   flag, not literal sudo/privilege access.
2. **Permissions**: 3 module-level permissions — stored/matched as
   lowercase `trainee`, `traineeattendance`, `traineecomment` (same as
   existing `attendance`/`calltranscription` rows — `GlobalHelper::Permissions()`
   lowercases route-name prefixes, and that's what gets saved as
   `Permission.name` from the Role edit form) — each with the standard
   `view/create/edit/delete` toggles, exactly like every other module.
3. **Trainee create/edit UI**: dedicated pages
   (`pages/trainee/create.blade.php`, `pages/trainee/edit.blade.php`),
   mirroring the User module's structure.
4. **Delete**: soft delete (`SoftDeletes`), consistent with User/Lead/Role.
   A JS confirm (SweetAlert2 `Swal.fire` confirm dialog, already used
   elsewhere in the app, e.g. lead/sale delete buttons) gates the
   destructive action client-side before hitting the `delete` route —
   same UX as existing modules; no separate server-rendered confirm page
   (the `conf-delete` route exists in older modules but several, e.g.
   `UserController`, don't even implement the corresponding controller
   method, meaning it's effectively unused legacy — not replicated here).
5. **Trainee Attendance**: one row per `(trainee_id, date)`, enforced by a
   unique constraint. Marking attendance again for an already-marked date
   updates the existing row (upsert) rather than erroring or duplicating.
   Only "today" is markable from the Mark Attendance modal (no backdating
   UI in this iteration — keeps the button/modal simple, matches the
   spec's described 3-button flow).
6. **Trainee Comments**: dedicated page per trainee
   (`GET trainee-comment/{trainee}`), not a modal listing — per the
   original spec. Add Comment is a modal on that page.
7. **Cascade**: deleting a trainee (soft delete) does not delete its
   attendance/comment history — they remain queryable/auditable, consistent
   with how the app treats soft-deleted parents elsewhere (e.g. a deleted
   Lead's comments/sales history isn't purged). Hard-deleting the
   `trainees` row (not exposed in UI, but for schema correctness) cascades
   via FK `onDelete('cascade')` on the two child tables.

## Data model

Three new tables, no existing tables touched:

```
trainees
  id                    bigint PK
  name                  string, required
  sudo_options          boolean, required        -- active/inactive status
  additional_info       text, nullable
  created_by_user_id    FK -> users.id
  updated_by_user_id    FK -> users.id, nullable
  deleted_by_user_id    FK -> users.id, nullable
  timestamps
  soft deletes

trainee_attendances
  id                    bigint PK
  trainee_id            FK -> trainees.id, cascade delete
  date                  date
  present               boolean
  late                  boolean
  created_by_user_id    FK -> users.id
  created_at
  unique (trainee_id, date)

trainee_comments
  id                    bigint PK
  trainee_id            FK -> trainees.id, cascade delete
  user_id               FK -> users.id           -- comment author
  comment               text
  comment_date          date
  created_at
```

Attendance status is derived, not stored separately:

| Status  | present | late |
|---------|---------|------|
| On-Time | true    | false |
| Late    | true    | true  |
| Absent  | false   | false |

## Backend

New files only:

- `database/migrations/xxxx_create_trainees_table.php`
- `database/migrations/xxxx_create_trainee_attendances_table.php`
- `database/migrations/xxxx_create_trainee_comments_table.php`
- `app/Models/Trainee.php` — `SoftDeletes`, relations to `createdBy`,
  `attendances()`, `comments()`.
- `app/Models/TraineeAttendance.php` — `belongsTo(Trainee::class)`,
  `belongsTo(User::class, 'created_by_user_id')`.
- `app/Models/TraineeComment.php` — `belongsTo(Trainee::class)`,
  `belongsTo(User::class)`.
- `app/Http/Controllers/TraineeController.php` — `index, create, store,
  edit, update, destroy`, validation inline (matches `UserController`
  style: `$request->validate([...])` per method, no Form Request classes
  — consistent with the rest of the codebase).
- `app/Http/Controllers/TraineeAttendanceController.php` — `store` only
  (AJAX endpoint used by the Mark Attendance modal); upserts by
  `(trainee_id, date)`.
- `app/Http/Controllers/TraineeCommentController.php` — `index` (comments
  page for one trainee), `store` (AJAX from Add Comment modal).

New route groups in `routes/web.php` (additive block only, same file
every other module's routes already live in):

```php
Route::controller(TraineeController::class)
    ->prefix('trainee')->as('trainee.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('delete/{id}', 'destroy')->name('delete');
    });

Route::controller(TraineeAttendanceController::class)
    ->prefix('trainee-attendance')->as('traineeattendance.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
    });

Route::controller(TraineeCommentController::class)
    ->prefix('trainee-comment')->as('traineecomment.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('{trainee}', 'index')->name('index');
        Route::post('store', 'store')->name('store');
    });
```

## Frontend

- `resources/views/pages/trainee/index.blade.php` — table listing
  (Name, Status badge, Created By, Created At, Actions), same table/card
  markup conventions as `pages/user/index.blade.php`. Per-row action
  buttons: Edit, Delete (gated on respective Trainee permissions), **Mark
  Attendance**, **Comments** (gated on the respective Trainee Attendance /
  Trainee Comment permissions).
- `resources/views/pages/trainee/create.blade.php`,
  `resources/views/pages/trainee/edit.blade.php` — mirror
  `pages/user/create.blade.php` structure/styling.
- Mark Attendance: Bootstrap modal (same `.modal` markup pattern used
  elsewhere in the app) with three buttons — On-Time / Late / Absent —
  posting via `fetch`/AJAX to `traineeattendance.store` with the trainee
  id and today's date; SweetAlert success toast on completion, modal
  closes.
- `resources/views/pages/trainee-comment/index.blade.php` — comments
  list for one trainee (date, author name, comment text, newest first)
  + "Add Comment" button opening a modal with a textarea, posting to
  `traineecomment.store`.
- New gated `<li>` in `sidebar.blade.php` for "Trainees" only (Attendance
  and Comments are reached from within the Trainees page, not separate
  nav items), same `$trainee_perm` pattern as `$attendance_perm`.

All styling reuses existing Bootstrap/Sneat classes and components — no
new UI library, no new JS dependency.

## Security & privacy

- All three route groups behind `auth` + `PermissionMiddelware`, same as
  every other module.
- Server-side validation on every mutating endpoint (`name` required,
  `sudo_options` required boolean, `additional_info` nullable string,
  attendance `date` required + one of the three statuses, comment
  `comment` required non-empty).
- Trainees are explicitly not `User` records — no auth guard, no login,
  no password, no role assignment. Nothing in this feature touches the
  `users` table or `Auth` facade beyond reading `Auth::user()->id` for
  audit columns, same as every other module already does.

## Isolation guarantee

Only two touches to existing files: one new set of route group blocks
appended in `routes/web.php`, and one new gated `<li>` block appended in
`sidebar.blade.php`. No existing controller, model, migration, view, or
route is modified. `AttendanceController`, `CommentsController`,
`UserController`, `PermissionMiddelware`, `GlobalHelper`, and their
respective models/migrations/views are untouched.

## Testing / verification

- Feature tests: Trainee CRUD (create/edit/list/delete) with permission
  gating (403/redirect when role lacks the relevant toggle).
- Feature test: marking attendance twice for the same trainee/date
  updates rather than duplicates; verify derived status combinations
  (on-time/late/absent) persist correct `present`/`late` values.
- Feature test: adding a comment persists `user_id`/`comment_date`
  correctly and appears on the trainee's comment listing.
- Regression pass: confirm `attendance.index`, `comment.store` (existing
  Lead-comment flow), `user.index/create/edit`, and the Role permission
  matrix all behave unchanged — existing Attendance/Comments/User feature
  tests still pass unmodified.
