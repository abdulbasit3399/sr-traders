<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceReturnProduct extends Model
{
    use HasFactory;
    protected $table = 'invoice_return_products';
    protected $fillable = [
        'product_id',
        'invoice_id',
        'quantity',
        'tax',
        'discount',
        'total',
    ];

    public function product(){
        return $this->hasOne('App\Models\ProductService', 'id', 'product_id')->first();
    }
}
