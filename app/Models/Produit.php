<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Produit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code_produit',
        'nom',
        'module_stock_id',
        'categorie_id',
        'fournisseur_id',
        'prix_achat',
        'prix_vente_gros',
        'prix_vente_detail',
        'unite_stock',
        'unite_detail',
        'contenance_carton',
        'seuil_alerte',
        'stock_minimum',
        'date_expiration',
        'jours_alerte_expiration',
        'description',
        'image',
        'is_active',
        'vente_detail_possible',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'vente_detail_possible' => 'boolean',
        'prix_achat' => 'decimal:2',
        'prix_vente_gros' => 'decimal:2',
        'prix_vente_detail' => 'decimal:2',
        'contenance_carton' => 'decimal:3',
        'date_expiration' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function moduleStock()
    {
        return $this->belongsTo(ModuleStock::class, 'module_stock_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function ventesDetails()
    {
        return $this->hasMany(VenteDetail::class);
    }

    public function alertes()
    {
        return $this->hasMany(Alerte::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStockTotalAttribute(): float
    {
        if ($this->relationLoaded('stocks')) {
            return $this->stocks->sum('quantite');
        }

        return $this->stocks()->sum('quantite');
    }

    public function getJoursAvantExpirationAttribute(): ?int
    {
        if (!$this->date_expiration) {
            return null;
        }

        return max(0, now()->diffInDays($this->date_expiration, false));
    }

    /*
    |--------------------------------------------------------------------------
    | Logique métier
    |--------------------------------------------------------------------------
    */

    public function isStockFaible(): bool
    {
        return $this->stock_total <= $this->seuil_alerte;
    }

    public function isExpire(): bool
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }

    public function isExpirationProche(): bool
    {
        if (!$this->date_expiration) {
            return false;
        }

        return now()->diffInDays($this->date_expiration)
            <= $this->jours_alerte_expiration
            && !$this->isExpire();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParModule($query, int $moduleId)
    {
        return $query->where('module_stock_id', $moduleId);
    }

    public function scopeStockFaible($query)
    {
        return $query->whereHas('stocks', function ($q) {
            $q->whereColumn('quantite', '<=', 'produits.seuil_alerte');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Génération automatique du code produit
    |--------------------------------------------------------------------------
    */

    public static function genererCode(string $moduleSlug): string
    {
        $prefix = strtoupper(substr($moduleSlug, 0, 3));

        $dernier = static::where('code_produit', 'like', $prefix . '-%')
            ->orderByDesc('id')
            ->first();

        $numero = $dernier
            ? intval(substr($dernier->code_produit, -4)) + 1
            : 1;

        return $prefix . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
