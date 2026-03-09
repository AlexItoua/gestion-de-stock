<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_vente',
        'boutique_id',
        'user_id',
        'montant_total',
        'montant_paye',
        'monnaie_rendue',
        'statut',
        'mode_paiement',
        'nom_client',
        'telephone_client',
        'notes',
        'date_vente',
    ];

    protected $casts = [
        'montant_total'   => 'decimal:2',
        'montant_paye'    => 'decimal:2',
        'monnaie_rendue'  => 'decimal:2',
        'date_vente'      => 'datetime',
    ];

    // Relations
    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(VenteDetail::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    // Statut
    public function isFinalisee(): bool
    {
        return $this->statut === 'finalisee';
    }

    // Génération numéro
    public static function genererNumero(): string
    {
        $annee = now()->format('Y');
        $dernier = static::where('numero_vente', 'like', "VTE-{$annee}-%")
            ->orderByDesc('id')
            ->first();
        $numero = $dernier ? intval(substr($dernier->numero_vente, -6)) + 1 : 1;
        return "VTE-{$annee}-" . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function scopeFinalisees($query)
    {
        return $query->where('statut', 'finalisee');
    }

    public function scopeParPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_vente', [$debut, $fin]);
    }

    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_vente', today());
    }

    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date_vente', [now()->startOfWeek(), now()->endOfWeek()]);
    }
}
