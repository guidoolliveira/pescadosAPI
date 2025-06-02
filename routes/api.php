<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CultivoController;
use App\Http\Controllers\UsoDiarioController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\BiometriaController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\ViveiroController;
use App\Http\Controllers\ProfileController;



// Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('viveiros', ViveiroController::class);
    Route::get('/index', [SiteController::class, 'index'])->name('api.index');
    Route::apiResource('produtos', ProductController::class);
    Route::apiResource('cultivos', CultivoController::class);
    Route::get('cultivos/{cultivo}/relatorio', [CultivoController::class, 'relatorio'])->name('cultivos.relatorio');
    Route::get('cultivos/{cultivo}/finalizar', [CultivoController::class, 'finalizar'])->name('cultivos.finalizar');
    Route::apiResource('uso_diario', UsoDiarioController::class);
    Route::apiResource('estoque', EstoqueController::class);
    Route::apiResource('biometrias', BiometriaController::class);
    Route::apiResource('funcionarios', FuncionarioController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

