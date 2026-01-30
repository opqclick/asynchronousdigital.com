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
use App\Http\Controllers\TeamMember\DashboardController as TeamMemberDashboardController;
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
    Route::resource('teams', TeamController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('salaries', SalaryController::class);
    Route::resource('users', UserController::class);
    
    // Public website management
    Route::resource('services', AdminServiceController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'update', 'destroy']);
});

// Team Member routes
Route::middleware(['auth', 'role:team_member'])->prefix('team')->name('team-member.')->group(function () {
    Route::get('/dashboard', [TeamMemberDashboardController::class, 'index'])->name('dashboard');
});

// Client routes
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
