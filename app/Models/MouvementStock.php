<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'reference',
        'produit_id',
        'boutique_id',
        'user_id',
        'type_mouvement',
        'quantite',
        'quantite_avant',
        'quantite_apres',
        'prix_unitaire',
        'valeur_totale',
        'boutique_destination_id',
        'mouvement_lie_id',
        'vente_id',
        'commentaire',
        'date_mouvement',
    ];

    protected $casts = [
        'quantite'        => 'decimal:3',
        'quantite_avant'  => 'decimal:3',
        'quantite_apres'  => 'decimal:3',
        'prix_unitaire'   => 'decimal:2',
        'valeur_totale'   => 'decimal:2',
        'date_mouvement'  => 'datetime',
    ];

    // Relations
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function boutiqueDestination()
    {
        return $this->belongsTo(Boutique::class, 'boutique_destination_id');
    }

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function mouvementLie()
    {
        return $this->belongsTo(MouvementStock::class, 'mouvement_lie_id');
    }

    // Génération de référence
    public static function genererReference(): string
    {
        $annee = now()->format('Y');
        $dernier = static::where('reference', 'like', "MVT-{$annee}-%")
            ->orderByDesc('id')
            ->first();
        $numero = $dernier ? intval(substr($dernier->reference, -6)) + 1 : 1;
        return "MVT-{$annee}-" . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function scopeEntrees($query)
    {
        return $query->where('type_mouvement', 'entree');
    }

    public function scopeVentes($query)
    {
        return $query->where('type_mouvement', 'vente');
    }

    public function scopePertes($query)
    {
        return $query->where('type_mouvement', 'perte');
    }

    public function scopeParPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_mouvement', [$debut, $fin]);
    }
}
