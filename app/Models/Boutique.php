<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boutique extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'code',
        'adresse',
        'ville',
        'telephone',
        'responsable',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function scopeActif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Valeur totale du stock de cette boutique
     */
    public function valeurTotaleStock(): float
    {
        return $this->stocks()->sum('valeur_stock');
    }
}
