<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table      = 'transactions';
    protected $primaryKey = 'transaction_id';
    protected $fillable   = ['seller_id', 'buyer_id', 'status'];

    public function seller()
    {return $this->belongsTo(UserData::class, 'seller_id', 'user_id');}
    public function buyer()
    {return $this->belongsTo(UserData::class, 'buyer_id', 'user_id');}
    public function items()
    {return $this->hasMany(Transaction_items::class, 'transaction_id', 'transaction_id');}
}
