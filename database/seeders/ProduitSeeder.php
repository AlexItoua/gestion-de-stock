<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produit;
use App\Models\ModuleStock;
use App\Models\Categorie;
use App\Models\Fournisseur;

class ProduitSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Récupération des modules ───
        $mRiz        = ModuleStock::where('slug', 'riz-cereales')->first();
        $mHuiles     = ModuleStock::where('slug', 'huiles')->first();
        $mSucre      = ModuleStock::where('slug', 'sucre-farine')->first();
        $mConserves  = ModuleStock::where('slug', 'conserves')->first();
        $mLegumes    = ModuleStock::where('slug', 'legumes')->first();
        $mBoissons   = ModuleStock::where('slug', 'boissons')->first();
        $mLaitiers   = ModuleStock::where('slug', 'produits-laitiers')->first();
        $mHygiene    = ModuleStock::where('slug', 'hygiene')->first();
        $mCondiments = ModuleStock::where('slug', 'condiments')->first();
        $mSnacks     = ModuleStock::where('slug', 'snacks')->first();

        // ─── Récupération des catégories ───
        $catRiz50    = Categorie::where('slug', 'riz-sac-50kg')->first();
        $catRiz25    = Categorie::where('slug', 'riz-sac-25kg')->first();
        $catRiz5     = Categorie::where('slug', 'riz-sachet-5kg')->first();
        $catMais     = Categorie::where('slug', 'mais-mil')->first();

        $catHuile5   = Categorie::where('slug', 'huile-bidon-5l')->first();
        $catHuile1   = Categorie::where('slug', 'huile-bidon-1l')->first();

        $catSucre50  = Categorie::where('slug', 'sucre-sac-50kg')->first();
        $catSucre1   = Categorie::where('slug', 'sucre-sachet-1kg')->first();
        $catFarine50 = Categorie::where('slug', 'farine-sac-50kg')->first();
        $catFarine1  = Categorie::where('slug', 'farine-sachet-1kg')->first();

        $catConsT    = Categorie::where('slug', 'conserves-tomate')->first();
        $catPates    = Categorie::where('slug', 'pates')->first();
        $catSardine  = Categorie::where('slug', 'sardines')->first();

        $catOignon   = Categorie::where('slug', 'oignons')->first();
        $catTomate   = Categorie::where('slug', 'tomates')->first();
        $catAil      = Categorie::where('slug', 'ail')->first();
        $catPdt      = Categorie::where('slug', 'pommes-terre')->first();

        $catEau      = Categorie::where('slug', 'eau-minerale')->first();
        $catSucrerie = Categorie::where('slug', 'sucreries')->first();
        $catJus      = Categorie::where('slug', 'jus')->first();
        $catGazeuse  = Categorie::where('slug', 'gazeuses')->first();

        $catLaitP    = Categorie::where('slug', 'lait-poudre')->first();
        $catLaitC    = Categorie::where('slug', 'lait-concentre')->first();

        $catSavon    = Categorie::where('slug', 'savon')->first();
        $catDeterg   = Categorie::where('slug', 'detergent')->first();

        $catSel      = Categorie::where('slug', 'sel')->first();
        $catCube     = Categorie::where('slug', 'cube-bouillon')->first();

        $catBiscuit  = Categorie::where('slug', 'biscuits')->first();
        $catChips    = Categorie::where('slug', 'chips')->first();

        // ─── Récupération des fournisseurs ───
        $fRiz        = Fournisseur::where('nom', 'Grossiste Riz Congo')->first();
        $fImport     = Fournisseur::where('nom', 'Import Afrique SARL')->first();
        $fHuiles     = Fournisseur::where('nom', 'Distrib Huiles & Savons')->first();
        $fMaraicher  = Fournisseur::where('nom', 'Maraîcher Central')->first();
        $fConserves  = Fournisseur::where('nom', 'Conserves & Pâtes Import')->first();
        $fBoissons   = Fournisseur::where('nom', 'Boissons & Rafraîchissements')->first();

        $produits = [
            // ═══════════ RIZ & CÉRÉALES ═══════════
            [
                'code_produit' => 'RIZ-0001', 'nom' => 'Riz Parfumé Sac 50kg',
                'module_stock_id' => $mRiz->id, 'categorie_id' => $catRiz50->id, 'fournisseur_id' => $fRiz->id,
                'prix_achat' => 25000, 'prix_vente_gros' => 28000, 'prix_vente_detail' => 550,
                'unite_stock' => 'sac', 'unite_detail' => 'kg', 'contenance_carton' => 50,
                'seuil_alerte' => 5, 'stock_minimum' => 3, 'vente_detail_possible' => true,
                'is_active' => true,
            ],
            [
                'code_produit' => 'RIZ-0002', 'nom' => 'Riz Ordinaire Sac 50kg',
                'module_stock_id' => $mRiz->id, 'categorie_id' => $catRiz50->id, 'fournisseur_id' => $fRiz->id,
                'prix_achat' => 18000, 'prix_vente_gros' => 21000, 'prix_vente_detail' => 450,
                'unite_stock' => 'sac', 'unite_detail' => 'kg', 'contenance_carton' => 50,
                'seuil_alerte' => 5, 'stock_minimum' => 3, 'vente_detail_possible' => true,
                'is_active' => true,
            ],
            [
                'code_produit' => 'RIZ-0003', 'nom' => 'Riz Parfumé Sac 25kg',
                'module_stock_id' => $mRiz->id, 'categorie_id' => $catRiz25->id, 'fournisseur_id' => $fRiz->id,
                'prix_achat' => 13000, 'prix_vente_gros' => 15000, 'prix_vente_detail' => 550,
                'unite_stock' => 'sac', 'unite_detail' => 'kg', 'contenance_carton' => 25,
                'seuil_alerte' => 5, 'stock_minimum' => 3, 'vente_detail_possible' => true,
                'is_active' => true,
            ],
            [
                'code_produit' => 'RIZ-0004', 'nom' => 'Riz Étuvé Sachet 5kg',
                'module_stock_id' => $mRiz->id, 'categorie_id' => $catRiz5->id, 'fournisseur_id' => $fRiz->id,
                'prix_achat' => 3000, 'prix_vente_gros' => 3500, 'prix_vente_detail' => 600,
                'unite_stock' => 'sachet', 'unite_detail' => 'kg', 'contenance_carton' => 5,
                'seuil_alerte' => 10, 'stock_minimum' => 5, 'vente_detail_possible' => true,
                'is_active' => true,
            ],
            [
                'code_produit' => 'MAI-0001', 'nom' => 'Maïs Sac 50kg',
                'module_stock_id' => $mRiz->id, 'categorie_id' => $catMais->id, 'fournisseur_id' => $fRiz->id,
                'prix_achat' => 20000, 'prix_vente_gros' => 23000, 'prix_vente_detail' => 500,
                'unite_stock' => 'sac', 'unite_detail' => 'kg', 'contenance_carton' => 50,
                'seuil_alerte' => 3, 'stock_minimum' => 2, 'vente_detail_possible' => true,
                'is_active' => true,
            ],

            // ═══════════ HUILES ═══════════
            [
                'code_produit' => 'HUI-0001', 'nom' => 'Huile Végétale Bidon 5L',
                'module_stock_id' => $mHuiles->id, 'categorie_id' => $catHuile5->id, 'fournisseur_id' => $fHuiles->id,
                'prix_achat' => 6500, 'prix_vente_gros' => 7500, 'prix_vente_detail' => 1700,
                'unite_stock' => 'bidon', 'unite_detail' => 'litre', 'contenance_carton' => 5,
                'seuil_alerte' => 10, 'stock_minimum' => 5, 'vente_detail_possible' => true,
                'is_active' => true,
            ],
            [
                'code_produit' => 'HUI-0002', 'nom' => 'Huile de Palme Bidon 5L',
                'module_stock_id' => $mHuiles->id, 'categorie_id' => $catHuile5->id, 'fournisseur_id' => $fHuiles->id,
                'prix_achat' => 7000, 'prix_vente_gros' => 8000, 'prix_vente_detail' => 1800,
                'unite_stock' => 'bidon', 'unite_detail' => 'litre', 'contenance_carton' => 5,
                'seuil_alerte' => 10, 'stock_minimum' => 5, 'vente_detail_possible' => true,
                'is_active' => true,
            ],
            [
                'code_produit' => 'HUI-0003', 'nom' => 'Huile Végétale Bidon 1L',
                'module_stock_id' => $mHuiles->id, 'categorie_id' => $catHuile1->id, 'fournisseur_id' => $fHuiles->id,
                'prix_achat' => 1400, 'prix_vente_gros' => 1700, 'prix_vente_detail' => 1800,
                'unite_stock' => 'bidon', 'unite_detail' => 'litre', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],

            // ═══════════ SUCRE & FARINE ═══════════
            [
                'code_produit' => 'SUC-0001', 'nom' => 'Sucre Blanc Sac 50kg',
                'module_stock_id' => $mSucre->id, 'categorie_id' => $catSucre50->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 30000, 'prix_vente_gros' => 34000, 'prix_vente_detail' => 750,
                'unite_stock' => 'sac', 'unite_detail' => 'kg', 'contenance_carton' => 50,
                'seuil_alerte' => 3, 'stock_minimum' => 2, 'vente_detail_possible' => true,
                'is_active' => true,
            ],
            [
                'code_produit' => 'SUC-0002', 'nom' => 'Sucre en Morceaux Sachet 1kg',
                'module_stock_id' => $mSucre->id, 'categorie_id' => $catSucre1->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 900, 'prix_vente_gros' => 1100, 'prix_vente_detail' => 1200,
                'unite_stock' => 'sachet', 'unite_detail' => 'kg', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'FAR-0001', 'nom' => 'Farine de Blé Supérieure 50kg',
                'module_stock_id' => $mSucre->id, 'categorie_id' => $catFarine50->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 28000, 'prix_vente_gros' => 32000, 'prix_vente_detail' => 700,
                'unite_stock' => 'sac', 'unite_detail' => 'kg', 'contenance_carton' => 50,
                'seuil_alerte' => 3, 'stock_minimum' => 2, 'vente_detail_possible' => true,
                'is_active' => true,
            ],
            [
                'code_produit' => 'FAR-0002', 'nom' => 'Farine de Maïs Sachet 1kg',
                'module_stock_id' => $mSucre->id, 'categorie_id' => $catFarine1->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 800, 'prix_vente_gros' => 1000, 'prix_vente_detail' => 1100,
                'unite_stock' => 'sachet', 'unite_detail' => 'kg', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],

            // ═══════════ CONSERVES & PÂTES ═══════════
            [
                'code_produit' => 'CON-0001', 'nom' => 'Sardines en Boîte',
                'module_stock_id' => $mConserves->id, 'categorie_id' => $catSardine->id, 'fournisseur_id' => $fConserves->id,
                'prix_achat' => 800, 'prix_vente_gros' => 1000, 'prix_vente_detail' => 1100,
                'unite_stock' => 'boite', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 30, 'stock_minimum' => 15, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'CON-0002', 'nom' => 'Concentré de Tomate Boîte',
                'module_stock_id' => $mConserves->id, 'categorie_id' => $catConsT->id, 'fournisseur_id' => $fConserves->id,
                'prix_achat' => 500, 'prix_vente_gros' => 700, 'prix_vente_detail' => 800,
                'unite_stock' => 'boite', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 30, 'stock_minimum' => 15, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'PAT-0001', 'nom' => 'Spaghetti 500g',
                'module_stock_id' => $mConserves->id, 'categorie_id' => $catPates->id, 'fournisseur_id' => $fConserves->id,
                'prix_achat' => 400, 'prix_vente_gros' => 600, 'prix_vente_detail' => 700,
                'unite_stock' => 'paquet', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'PAT-0002', 'nom' => 'Macaroni 500g',
                'module_stock_id' => $mConserves->id, 'categorie_id' => $catPates->id, 'fournisseur_id' => $fConserves->id,
                'prix_achat' => 400, 'prix_vente_gros' => 600, 'prix_vente_detail' => 700,
                'unite_stock' => 'paquet', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],

            // ═══════════ LÉGUMES FRAIS ═══════════
            [
                'code_produit' => 'LEG-0001', 'nom' => 'Oignons',
                'module_stock_id' => $mLegumes->id, 'categorie_id' => $catOignon->id, 'fournisseur_id' => $fMaraicher->id,
                'prix_achat' => 700, 'prix_vente_gros' => 900, 'prix_vente_detail' => 1000,
                'unite_stock' => 'kg', 'unite_detail' => 'kg', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'LEG-0002', 'nom' => 'Tomates',
                'module_stock_id' => $mLegumes->id, 'categorie_id' => $catTomate->id, 'fournisseur_id' => $fMaraicher->id,
                'prix_achat' => 600, 'prix_vente_gros' => 800, 'prix_vente_detail' => 900,
                'unite_stock' => 'kg', 'unite_detail' => 'kg', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'LEG-0003', 'nom' => 'Ail',
                'module_stock_id' => $mLegumes->id, 'categorie_id' => $catAil->id, 'fournisseur_id' => $fMaraicher->id,
                'prix_achat' => 2500, 'prix_vente_gros' => 3000, 'prix_vente_detail' => 3200,
                'unite_stock' => 'kg', 'unite_detail' => 'kg', 'contenance_carton' => 1,
                'seuil_alerte' => 10, 'stock_minimum' => 5, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'LEG-0004', 'nom' => 'Pommes de Terre',
                'module_stock_id' => $mLegumes->id, 'categorie_id' => $catPdt->id, 'fournisseur_id' => $fMaraicher->id,
                'prix_achat' => 500, 'prix_vente_gros' => 700, 'prix_vente_detail' => 800,
                'unite_stock' => 'kg', 'unite_detail' => 'kg', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],

            // ═══════════ BOISSONS ═══════════
            [
                'code_produit' => 'BOI-0001', 'nom' => 'Eau Minérale 1.5L',
                'module_stock_id' => $mBoissons->id, 'categorie_id' => $catEau->id, 'fournisseur_id' => $fBoissons->id,
                'prix_achat' => 300, 'prix_vente_gros' => 400, 'prix_vente_detail' => 500,
                'unite_stock' => 'bouteille', 'unite_detail' => 'litre', 'contenance_carton' => 1,
                'seuil_alerte' => 30, 'stock_minimum' => 15, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'BOI-0002', 'nom' => 'Coca-Cola 1.5L',
                'module_stock_id' => $mBoissons->id, 'categorie_id' => $catGazeuse->id, 'fournisseur_id' => $fBoissons->id,
                'prix_achat' => 800, 'prix_vente_gros' => 1000, 'prix_vente_detail' => 1200,
                'unite_stock' => 'bouteille', 'unite_detail' => 'litre', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'BOI-0003', 'nom' => "Jus d'Orange 1L",
                'module_stock_id' => $mBoissons->id, 'categorie_id' => $catJus->id, 'fournisseur_id' => $fBoissons->id,
                'prix_achat' => 1200, 'prix_vente_gros' => 1500, 'prix_vente_detail' => 1700,
                'unite_stock' => 'bouteille', 'unite_detail' => 'litre', 'contenance_carton' => 1,
                'seuil_alerte' => 15, 'stock_minimum' => 8, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'BOI-0004', 'nom' => 'Bonbons en Sachet',
                'module_stock_id' => $mBoissons->id, 'categorie_id' => $catSucrerie->id, 'fournisseur_id' => $fBoissons->id,
                'prix_achat' => 500, 'prix_vente_gros' => 700, 'prix_vente_detail' => 100,
                'unite_stock' => 'sachet', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => true,
                'is_active' => true,
            ],

            // ═══════════ PRODUITS LAITIERS ═══════════
            [
                'code_produit' => 'LAI-0001', 'nom' => 'Lait en Poudre 400g',
                'module_stock_id' => $mLaitiers->id, 'categorie_id' => $catLaitP->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 2500, 'prix_vente_gros' => 3000, 'prix_vente_detail' => 3300,
                'unite_stock' => 'boite', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 15, 'stock_minimum' => 8, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'LAI-0002', 'nom' => 'Lait Concentré Sucré',
                'module_stock_id' => $mLaitiers->id, 'categorie_id' => $catLaitC->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 800, 'prix_vente_gros' => 1000, 'prix_vente_detail' => 1200,
                'unite_stock' => 'boite', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],

            // ═══════════ HYGIÈNE ═══════════
            [
                'code_produit' => 'HYG-0001', 'nom' => 'Savon de Toilette',
                'module_stock_id' => $mHygiene->id, 'categorie_id' => $catSavon->id, 'fournisseur_id' => $fHuiles->id,
                'prix_achat' => 300, 'prix_vente_gros' => 400, 'prix_vente_detail' => 500,
                'unite_stock' => 'piece', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 30, 'stock_minimum' => 15, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'HYG-0002', 'nom' => 'Savon de Marseille',
                'module_stock_id' => $mHygiene->id, 'categorie_id' => $catSavon->id, 'fournisseur_id' => $fHuiles->id,
                'prix_achat' => 500, 'prix_vente_gros' => 700, 'prix_vente_detail' => 800,
                'unite_stock' => 'piece', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'HYG-0003', 'nom' => 'Détergent Lessive 1kg',
                'module_stock_id' => $mHygiene->id, 'categorie_id' => $catDeterg->id, 'fournisseur_id' => $fHuiles->id,
                'prix_achat' => 1500, 'prix_vente_gros' => 1800, 'prix_vente_detail' => 2000,
                'unite_stock' => 'paquet', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 15, 'stock_minimum' => 8, 'vente_detail_possible' => false,
                'is_active' => true,
            ],

            // ═══════════ CONDIMENTS ═══════════
            [
                'code_produit' => 'CDM-0001', 'nom' => 'Sel de Cuisine 1kg',
                'module_stock_id' => $mCondiments->id, 'categorie_id' => $catSel->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 300, 'prix_vente_gros' => 400, 'prix_vente_detail' => 500,
                'unite_stock' => 'paquet', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'CDM-0002', 'nom' => 'Cube Bouillon',
                'module_stock_id' => $mCondiments->id, 'categorie_id' => $catCube->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 500, 'prix_vente_gros' => 700, 'prix_vente_detail' => 800,
                'unite_stock' => 'sachet', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 30, 'stock_minimum' => 15, 'vente_detail_possible' => false,
                'is_active' => true,
            ],

            // ═══════════ SNACKS ═══════════
            [
                'code_produit' => 'SNK-0001', 'nom' => 'Biscuits Sucrés',
                'module_stock_id' => $mSnacks->id, 'categorie_id' => $catBiscuit->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 500, 'prix_vente_gros' => 700, 'prix_vente_detail' => 800,
                'unite_stock' => 'paquet', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 20, 'stock_minimum' => 10, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
            [
                'code_produit' => 'SNK-0002', 'nom' => 'Chips de Pomme de Terre',
                'module_stock_id' => $mSnacks->id, 'categorie_id' => $catChips->id, 'fournisseur_id' => $fImport->id,
                'prix_achat' => 800, 'prix_vente_gros' => 1000, 'prix_vente_detail' => 1200,
                'unite_stock' => 'paquet', 'unite_detail' => 'piece', 'contenance_carton' => 1,
                'seuil_alerte' => 15, 'stock_minimum' => 8, 'vente_detail_possible' => false,
                'is_active' => true,
            ],
        ];

        foreach ($produits as $data) {
            Produit::updateOrCreate(['code_produit' => $data['code_produit']], $data);
        }
    }
}