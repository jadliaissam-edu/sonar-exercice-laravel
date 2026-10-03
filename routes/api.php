<?php

use App\Level1\Controllers\CategoryController;
use App\Level1\Controllers\TicketController;
use App\Level2\Controllers\CommentController;
use App\Level2\Controllers\StatsController;
use App\Level3\Controllers\AttachmentController;
use App\Level3\Controllers\AuthController;
use App\Level3\Controllers\SearchController;
use App\Level4\Controllers\AdminReportController;
use App\Level4\Controllers\ReportController;
use App\Level5\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

// Tickets and categories
Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::post('/tickets/{id}/close', [TicketController::class, 'close']);
Route::post('/tickets/{id}/reopen', [TicketController::class, 'reopen']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);

// Comments, assignment and statistics
Route::get('/tickets/{ticketId}/comments', [CommentController::class, 'index']);
Route::post('/tickets/{ticketId}/comments', [CommentController::class, 'store']);
Route::delete('/tickets/{ticketId}/comments/{commentId}', [CommentController::class, 'destroy']);
Route::post('/tickets/{ticketId}/assign', [StatsController::class, 'assign']);
Route::get('/stats/summary', [StatsController::class, 'summary']);

// Accounts, search and attachments
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/status', [SearchController::class, 'byStatus']);
Route::post('/tickets/{ticketId}/attachments', [AttachmentController::class, 'store']);
Route::post('/attachments', [AttachmentController::class, 'storeRaw']);
Route::get('/attachments/download', [AttachmentController::class, 'download']);

// Reports and SLA
Route::get('/reports/summary', [ReportController::class, 'summary']);
Route::get('/reports/open.csv', [ReportController::class, 'exportOpen']);
Route::get('/admin/reports/open.csv', [AdminReportController::class, 'exportOpen']);
Route::get('/admin/sla/{id}', [AdminReportController::class, 'sla']);

// Workflow
Route::post('/workflow/{id}/transition', [WorkflowController::class, 'transition']);
Route::post('/workflow/{id}/escalate', [WorkflowController::class, 'escalate']);
Route::get('/workflow/priority', [WorkflowController::class, 'priority']);
