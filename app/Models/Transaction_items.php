<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction_items extends Model
{
    protected $table      = 'transaction__items';
    protected $primaryKey = 'transaction_item_id';
    protected $fillable   = ['transaction_id', 'item_id', 'price_at_purchase'];
    public function item()
    {return $this->belongsTo(Item::class, 'item_id', 'item_id');}
    public function transaction()
    {return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');}
}
