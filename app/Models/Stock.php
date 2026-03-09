<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id',
        'boutique_id',
        'quantite',
        'quantite_detail',
        'valeur_stock',
        'derniere_mise_a_jour',
    ];

    protected $casts = [
        'quantite' => 'decimal:3',
        'quantite_detail' => 'decimal:3',
        'valeur_stock' => 'decimal:2',
        'derniere_mise_a_jour' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Logique métier
    |--------------------------------------------------------------------------
    */

    /**
     * Recalcule la valeur du stock
     */
    public function recalculerValeur(): void
    {
        if (!$this->relationLoaded('produit')) {
            $this->load('produit:id,prix_achat');
        }

        $this->valeur_stock = $this->quantite * $this->produit->prix_achat;
        $this->derniere_mise_a_jour = now();

        $this->save();
    }

    /**
     * Ajouter du stock
     */
    public function ajouter(float $quantite): void
    {
        $this->quantite += $quantite;

        $this->recalculerValeur();
    }

    /**
     * Retirer du stock
     */
    public function retirer(float $quantite): void
    {
        if ($this->quantite < $quantite) {
            throw new \Exception(
                "Stock insuffisant. Disponible: {$this->quantite}, demandé: {$quantite}"
            );
        }

        $this->quantite -= $quantite;

        $this->recalculerValeur();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePourBoutique($query, int $boutiqueId)
    {
        return $query->where('boutique_id', $boutiqueId);
    }

    public function scopeProduit($query, int $produitId)
    {
        return $query->where('produit_id', $produitId);
    }
}
