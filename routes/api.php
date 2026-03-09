<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    DashboardController,
    ProduitController,
    StockController,
    VenteController,
    BoutiqueController,
    FournisseurController,
    CategorieController,
    AlerteController,
    UserController,
    ModuleStockController,
    RapportController,
};

/*
|--------------------------------------------------------------------------
| API Routes - Gestion de Stock
|--------------------------------------------------------------------------
*/

// ── Authentification (publique) ─────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// ── Routes protégées ─────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::put('password', [AuthController::class, 'changePassword']);
    });

    // Tableau de bord
    Route::get('dashboard', [DashboardController::class, 'index']);

    // ── Modules (admin seulement) ─────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('modules', [ModuleStockController::class, 'index']);
        Route::post('modules', [ModuleStockController::class, 'store']);
        Route::put('modules/{moduleStock}', [ModuleStockController::class, 'update']);

        // Gestion utilisateurs
        Route::apiResource('users', UserController::class);
        Route::put('users/{user}/reset-password', [UserController::class, 'resetPassword']);
    });

    // ── Catégories ────────────────────────────────────────────────────
    Route::middleware('role:admin|gestionnaire')->group(function () {
        Route::get('categories', [CategorieController::class, 'index']);
        Route::post('categories', [CategorieController::class, 'store']);
        Route::put('categories/{categorie}', [CategorieController::class, 'update']);
        Route::delete('categories/{categorie}', [CategorieController::class, 'destroy']);

        // Fournisseurs
        Route::apiResource('fournisseurs', FournisseurController::class);

        // Boutiques
        Route::apiResource('boutiques', BoutiqueController::class);
    });

    // ── Produits (admin + gestionnaire) ──────────────────────────────
    Route::middleware('role:admin|gestionnaire')->group(function () {
        Route::get('produits', [ProduitController::class, 'index']);
        Route::post('produits', [ProduitController::class, 'store']);
        Route::get('produits/{produit}', [ProduitController::class, 'show']);
        Route::put('produits/{produit}', [ProduitController::class, 'update']);
        Route::delete('produits/{produit}', [ProduitController::class, 'destroy']);
    });

    // ── Stocks ────────────────────────────────────────────────────────
    Route::prefix('stocks')->group(function () {
        // Liste stocks (tous les rôles)
        Route::get('/', [StockController::class, 'index']);
        Route::get('mouvements', [StockController::class, 'mouvements']);

        // Modifications stock (admin + gestionnaire)
        Route::middleware('role:admin|gestionnaire')->group(function () {
            Route::post('entree', [StockController::class, 'entree']);
            Route::post('perte', [StockController::class, 'perte']);
            Route::post('ajustement', [StockController::class, 'ajustement']);
            Route::post('transfert', [StockController::class, 'transfert']);
        });
    });

    // ── Ventes (tous les rôles authentifiés) ─────────────────────────
    Route::get('ventes', [VenteController::class, 'index']);
    Route::post('ventes', [VenteController::class, 'store']);
    Route::get('ventes/{vente}', [VenteController::class, 'show']);

    // Annulation vente (admin + gestionnaire)
    Route::middleware('role:admin|gestionnaire')->group(function () {
        Route::post('ventes/{vente}/annuler', [VenteController::class, 'annuler']);
    });

    // ── Alertes ───────────────────────────────────────────────────────
    Route::prefix('alertes')->group(function () {
        Route::get('/', [AlerteController::class, 'index']);
        Route::put('{alerte}/lire', [AlerteController::class, 'marquerLue']);
        Route::put('lire-toutes', [AlerteController::class, 'marquerToutesLues']);
        Route::middleware('role:admin|gestionnaire')->group(function () {
            Route::put('{alerte}/resoudre', [AlerteController::class, 'resoudre']);
            Route::post('verifier', [AlerteController::class, 'verifier']);
        });
    });

    // ── Rapports (admin + gestionnaire) ──────────────────────────────
    Route::middleware('role:admin|gestionnaire')->prefix('rapports')->group(function () {
        Route::get('ventes-jour', [RapportController::class, 'ventesJour']);
        Route::get('ventes-mois', [RapportController::class, 'ventesMois']);
        Route::get('etat-stock', [RapportController::class, 'etatStock']);
        Route::get('pertes', [RapportController::class, 'pertes']);
    });

    // Route modules accessible à tous (lecture)
    Route::get('modules', [ModuleStockController::class, 'index'])
        ->withoutMiddleware('role:admin');
});
