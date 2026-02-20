<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\TeamContentController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserActivityController;
use App\Http\Controllers\Admin\RecycleBinController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TeamMember\DashboardController as TeamMemberDashboardController;
use App\Http\Controllers\TeamMember\SalaryController as TeamMemberSalaryController;
use App\Http\Controllers\TeamMember\TaskController as TeamMemberTaskController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ContactController;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailTestController;

// Public routes - Single page website
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Redirect after login based on role
Route::get('/dashboard', function () {
    $user = Auth::user();
    $user->ensureActiveRoleContext();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isProjectManager()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isTeamMember()) {
        return redirect()->route('team-member.dashboard');
    } elseif ($user->isClient()) {
        return redirect()->route('client.dashboard');
    }

    if ($user->hasAssignedRole(Role::ADMIN) || $user->hasAssignedRole(Role::PROJECT_MANAGER)) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasAssignedRole(Role::TEAM_MEMBER)) {
        return redirect()->route('team-member.dashboard');
    }

    if ($user->hasAssignedRole(Role::CLIENT)) {
        return redirect()->route('client.dashboard');
    }

    abort(403, 'No dashboard access.');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/switch-role', [ProfileController::class, 'switchRole'])->name('profile.switch-role');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-feed', [NotificationController::class, 'unreadFeed'])->name('notifications.unread-feed');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::patch('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/impersonation/leave', [UserController::class, 'stopImpersonation'])
        ->name('admin.impersonation.leave');
});

// Admin routes
Route::middleware(['auth', 'role:admin,project_manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    // Resource routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('clients', ClientController::class)->middleware('permission:clients.manage');
    });

    Route::resource('projects', ProjectController::class)->middleware('permission:projects.manage');
    Route::resource('tasks', TaskController::class)->middleware('permission:tasks.manage');

    // Task AJAX routes
    Route::post('/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])
        ->middleware('permission:tasks.manage')
        ->name('tasks.update-status');
    Route::get('/tasks/{task}/details', [TaskController::class, 'details'])
        ->middleware('permission:tasks.manage')
        ->name('tasks.details');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment'])
        ->middleware('permission:tasks.manage')
        ->name('tasks.comments.store');

    Route::middleware('role:admin')->group(function () {
        Route::resource('teams', TeamController::class)->middleware('permission:teams.manage');
        Route::resource('invoices', InvoiceController::class)->middleware('permission:invoices.manage');
        Route::resource('payments', PaymentController::class)->middleware('permission:payments.manage');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('salaries', SalaryController::class)->middleware('permission:salaries.manage');
        Route::resource('users', UserController::class)->middleware('permission:users.manage');
        Route::get('/settings', [SystemSettingController::class, 'edit'])->middleware('permission:settings.manage')->name('settings.edit');
        Route::put('/settings', [SystemSettingController::class, 'update'])->middleware('permission:settings.manage')->name('settings.update');
        Route::post('/settings/test-email', [SystemSettingController::class, 'testEmail'])->middleware('permission:settings.manage')->name('settings.test-email');

        Route::prefix('permissions')->name('permissions.')->middleware('permission:permissions.manage')->group(function () {
            Route::get('/roles', [PermissionController::class, 'roleIndex'])->name('roles.index');
            Route::get('/roles/{role}/edit', [PermissionController::class, 'roleEdit'])->name('roles.edit');
            Route::put('/roles/{role}', [PermissionController::class, 'roleUpdate'])->name('roles.update');

            Route::get('/users', [PermissionController::class, 'userIndex'])->name('users.index');
            Route::get('/users/{user}/edit', [PermissionController::class, 'userEdit'])->name('users.edit');
            Route::put('/users/{user}', [PermissionController::class, 'userUpdate'])->name('users.update');
        });
    });

    // Send invitation emails
    Route::post('/users/{user}/send-invitation', [UserController::class, 'sendInvitation'])
        ->middleware(['role:admin', 'permission:users.manage'])
        ->name('users.send-invitation');
    Route::post('/users/{user}/impersonate', [UserController::class, 'impersonate'])
        ->middleware(['role:admin', 'permission:users.manage'])
        ->name('users.impersonate');
    Route::post('/clients/{client}/send-invitation', [ClientController::class, 'sendInvitation'])
        ->middleware(['role:admin', 'permission:clients.manage'])
        ->name('clients.send-invitation');

    // User activity logs
    Route::middleware('role:admin')->group(function () {
        Route::get('/user-activities', [UserActivityController::class, 'index'])->middleware('permission:user-activities.view')->name('user-activities.index');
        Route::get('/user-activities/{activity}', [UserActivityController::class, 'show'])->middleware('permission:user-activities.view')->name('user-activities.show');
        Route::get('/user-activities/{activity}/edit', [UserActivityController::class, 'edit'])->middleware('permission:user-activities.edit')->name('user-activities.edit');
        Route::put('/user-activities/{activity}', [UserActivityController::class, 'update'])->middleware('permission:user-activities.edit')->name('user-activities.update');
        Route::delete('/user-activities/{activity}', [UserActivityController::class, 'destroy'])->middleware('permission:user-activities.delete')->name('user-activities.destroy');
        Route::post('/user-activities/{activity}/restore', [UserActivityController::class, 'restore'])->middleware('permission:user-activities.restore')->name('user-activities.restore');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/recycle-bin', [RecycleBinController::class, 'index'])
            ->middleware('permission:recycle-bin.view')
            ->name('recycle-bin.index');
        Route::post('/recycle-bin/{type}/{id}/restore', [RecycleBinController::class, 'restore'])
            ->middleware('permission:recycle-bin.restore')
            ->name('recycle-bin.restore');
    });

    // Public website management
    Route::middleware('role:admin')->group(function () {
        Route::resource('services', AdminServiceController::class)->middleware('permission:services.manage');
        Route::resource('team-contents', TeamContentController::class)->middleware('permission:team-content.manage');
        Route::resource('testimonials', TestimonialController::class)->middleware('permission:testimonials.manage');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('contact-messages', ContactMessageController::class)
            ->middleware('permission:contact-messages.manage')
            ->only(['index', 'show', 'update', 'destroy']);
    });
});

// Team Member routes
Route::middleware(['auth', 'role:team_member'])->prefix('team')->name('team-member.')->group(function () {
    Route::get('/dashboard', [TeamMemberDashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::middleware('permission:tasks.manage_own')->group(function () {
        Route::get('/tasks/create', [TeamMemberTaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TeamMemberTaskController::class, 'store'])->name('tasks.store');
        Route::post('/tasks/{task}/update-status', [TeamMemberTaskController::class, 'updateStatus'])->name('tasks.update-status');
        Route::get('/tasks/{task}/details', [TeamMemberTaskController::class, 'details'])->name('tasks.details');
        Route::post('/tasks/{task}/comments', [TeamMemberTaskController::class, 'storeComment'])->name('tasks.comments.store');
    });

    Route::middleware('permission:salaries.view_own')->group(function () {
        Route::get('/salaries', [TeamMemberSalaryController::class, 'index'])->name('salaries.index');
        Route::get('/salaries/{salary}', [TeamMemberSalaryController::class, 'show'])->name('salaries.show');
        Route::get('/salaries/{salary}/share', [TeamMemberSalaryController::class, 'share'])->name('salaries.share');
        Route::post('/salaries/{salary}/confirm-received', [TeamMemberSalaryController::class, 'confirmReceived'])->name('salaries.confirm-received');
    });
});

// Client routes
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])
        ->middleware('permission:dashboard.view_own')
        ->name('dashboard');
});

// Email testing route
Route::get('/test-email', [EmailTestController::class, 'send'])->middleware('auth');

require __DIR__ . '/auth.php';
