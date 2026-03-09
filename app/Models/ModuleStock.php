<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modules_stock';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'icone',
        'couleur',
        'is_active',
        'ordre',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function categories()
    {
        return $this->hasMany(Categorie::class, 'module_stock_id');
    }

    public function produits()
    {
        return $this->hasMany(Produit::class, 'module_stock_id');
    }

    public function scopeActif($query)
    {
        return $query->where('is_active', true)->orderBy('ordre');
    }
}
