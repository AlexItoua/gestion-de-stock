<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorie extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'module_stock_id',
        'nom',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function moduleStock()
    {
        return $this->belongsTo(ModuleStock::class, 'module_stock_id');
    }

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    public function scopeActif($query)
    {
        return $query->where('is_active', true);
    }
}
