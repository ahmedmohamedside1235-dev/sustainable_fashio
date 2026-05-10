<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Swap extends Model
{
    protected $table      = 'swap__requests';
    protected $primaryKey = 'swap_id';
    protected $fillable   = ['requester_id', 'receiver_id', 'requested_item_id', 'cash_topup_amount', 'status'];
    public function requester()
    {return $this->belongsTo(UserData::class, 'requester_id', 'user_id');}
    public function receiver()
    {return $this->belongsTo(UserData::class, 'receiver_id', 'user_id');}
}
