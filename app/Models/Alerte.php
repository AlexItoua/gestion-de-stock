<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id',
        'boutique_id',
        'type_alerte',
        'titre',
        'message',
        'niveau',
        'is_lue',
        'is_resolue',
        'valeur_actuelle',
        'valeur_seuil',
        'date_resolution',
    ];

    protected $casts = [
        'is_lue'           => 'boolean',
        'is_resolue'       => 'boolean',
        'date_resolution'  => 'datetime',
        'valeur_actuelle'  => 'decimal:3',
        'valeur_seuil'     => 'decimal:3',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    public function marquerLue(): void
    {
        $this->update(['is_lue' => true]);
    }

    public function resoudre(): void
    {
        $this->update([
            'is_resolue'       => true,
            'date_resolution'  => now(),
        ]);
    }

    public function scopeNonLues($query)
    {
        return $query->where('is_lue', false);
    }

    public function scopeNonResolues($query)
    {
        return $query->where('is_resolue', false);
    }

    public function scopeDanger($query)
    {
        return $query->where('niveau', 'danger');
    }
}
