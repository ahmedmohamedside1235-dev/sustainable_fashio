<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table      = 'items';
    protected $primaryKey = 'item_id';
    public function seller(){return $this->belongsTo(UserData::class, 'seller_id', 'user_id');}
    public function material(){return $this->belongsTo(Material::class, 'material_id', 'material_id');}
    public function condition(){return $this->belongsTo(Item_condition::class, 'condition_id', 'condition_id');}
}
