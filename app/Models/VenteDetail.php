<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenteDetail extends Model
{
    use HasFactory;

    protected $table = 'ventes_details';

    protected $fillable = [
        'vente_id',
        'produit_id',
        'type_vente',
        'quantite',
        'prix_unitaire',
        'sous_total',
        'prix_achat_snapshot',
        'notes',
    ];

    protected $casts = [
        'quantite'           => 'decimal:3',
        'prix_unitaire'      => 'decimal:2',
        'sous_total'         => 'decimal:2',
        'prix_achat_snapshot'=> 'decimal:2',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    // Marge bénéficiaire sur ce détail
    public function getMargeAttribute(): float
    {
        return $this->sous_total - ($this->quantite * $this->prix_achat_snapshot);
    }
}
