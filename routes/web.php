<?php

use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JiraController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    #Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    #Integration Routes
    Route::get('/integrations', [IntegrationController::class, 'index'])->name('integrations.index');
    Route::post('/integrations/jira/disconnect', [JiraController::class, 'disconnect'])->name('jira.disconnect');

    // Jira Routes
    Route::get('/jira/connect', [JiraController::class, 'redirectToJira'])->name('jira.connect');
    Route::get('/jira/callback', [JiraController::class, 'handleJiraCallback'])->name('jira.callback');

    Route::get('/integrations/jira/projects', [JiraController::class, 'showProjectSelector'])->name('jira.projects');
    Route::post('/integrations/jira/projects', [JiraController::class, 'saveSelectedProjects'])->name('jira.projects.save');

});


require __DIR__.'/auth.php';

/**
 * Socialite
 */
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

Route::get('/login/github', function () {
    return Socialite::driver('github')->redirect();
});

Route::get('/login/github/callback', function () {
    $githubUser = Socialite::driver('github')->user();

    $user = User::updateOrCreate(
        ['email' => $githubUser->getEmail()],
        [
            'name' => $githubUser->getName() ?? $githubUser->getNickname(),
            'password' => bcrypt(Str::random(24)), // random password since they're using social
        ]
    );

    Auth::login($user, true);

    return redirect()->intended('/dashboard');
});
