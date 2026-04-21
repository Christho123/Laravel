<?php

namespace App\Models\Inventory\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'date',
        'total',
        'tipo_comprobante',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }
}
