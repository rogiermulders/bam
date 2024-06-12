<?php

use Illuminate\Support\Facades\Route;
use Rogiermulders\Bam\Http\Controllers\BamController;

Route::get('/rogiermulders/bam', [BamController::class, 'bam']);
