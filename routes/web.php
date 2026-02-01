<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserActivityController;
use App\Http\Controllers\TeamMember\DashboardController as TeamMemberDashboardController;
use App\Http\Controllers\TeamMember\SalaryController as TeamMemberSalaryController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// Public routes - Single page website
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Redirect after login based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isTeamMember()) {
        return redirect()->route('team-member.dashboard');
    } elseif ($user->isClient()) {
        return redirect()->route('client.dashboard');
    }
    
    abort(403, 'No dashboard access.');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Resource routes for all modules
    Route::resource('clients', ClientController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    
    // Task AJAX routes
    Route::post('/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::get('/tasks/{task}/details', [TaskController::class, 'details'])->name('tasks.details');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('tasks.comments.store');
    
    Route::resource('teams', TeamController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('salaries', SalaryController::class);
    Route::resource('users', UserController::class);
    
    // Send invitation emails
    Route::post('/users/{user}/send-invitation', [UserController::class, 'sendInvitation'])->name('users.send-invitation');
    Route::post('/clients/{client}/send-invitation', [ClientController::class, 'sendInvitation'])->name('clients.send-invitation');
    
    // User activity logs
    Route::get('/user-activities', [UserActivityController::class, 'index'])->name('user-activities.index');
    Route::get('/user-activities/{activity}', [UserActivityController::class, 'show'])->name('user-activities.show');
    
    // Public website management
    Route::resource('services', AdminServiceController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'update', 'destroy']);
});

// Team Member routes
Route::middleware(['auth', 'role:team_member'])->prefix('team')->name('team-member.')->group(function () {
    Route::get('/dashboard', [TeamMemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/salaries', [TeamMemberSalaryController::class, 'index'])->name('salaries.index');
    Route::get('/salaries/{salary}', [TeamMemberSalaryController::class, 'show'])->name('salaries.show');
    Route::get('/salaries/{salary}/share', [TeamMemberSalaryController::class, 'share'])->name('salaries.share');
    Route::post('/salaries/{salary}/confirm-received', [TeamMemberSalaryController::class, 'confirmReceived'])->name('salaries.confirm-received');
});

// Client routes
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
