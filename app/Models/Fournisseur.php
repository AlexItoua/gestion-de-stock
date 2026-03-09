<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'contact_nom',
        'telephone',
        'email',
        'adresse',
        'ville',
        'pays',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    public function scopeActif($query)
    {
        return $query->where('is_active', true);
    }
}
