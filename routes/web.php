<?php

use Illuminate\Support\Facades\Route;

Route::get('/generate-key', [App\Http\Controllers\VoteController::class, 'index'])->name('key.generate');
Route::get('/publish-key', [App\Http\Controllers\VoteController::class, 'showPublishKey'])->name('key.publish');
Route::post('/vote', [App\Http\Controllers\VoteController::class, 'vote'])->name('vote.post');
Route::post('/generate-key', [App\Http\Controllers\VoteController::class, 'generateKey'])->name('key.generate.post');
Route::post('/publish-key', [App\Http\Controllers\VoteController::class, 'publishKey'])->name('key.publish.post');
Route::post('/count-published-keys', [App\Http\Controllers\VoteController::class, 'countPublishedKeys'])->name('key.publish.count');
Route::post('/sign-vote', [App\Http\Controllers\VoteController::class, 'signVote'])->name('key.sign.vote');
Route::get('/voting-page', [App\Http\Controllers\VoteController::class, 'votingPage'])->name('voting.page');
Route::get('/success', [App\Http\Controllers\VoteController::class, 'success'])->name('success.page');
Route::get('/bulletin', [App\Http\Controllers\VoteController::class, 'showBulletin'])->name('bulletin.page');

Auth::routes();

Route::middleware('auth')->group(function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/new-agenda', [App\Http\Controllers\AgendaController::class, 'index'])->name('agenda');
    Route::post('/new-agenda/store', [App\Http\Controllers\AgendaController::class, 'store'])->name('agenda.post');
    Route::post('/agenda/share/{id}', [App\Http\Controllers\AgendaController::class, 'generateVotingLink'])->name('agenda.share');
    Route::delete('/agenda/delete/{id}', [App\Http\Controllers\AgendaController::class, 'delete'])->name('agenda.delete');
});

