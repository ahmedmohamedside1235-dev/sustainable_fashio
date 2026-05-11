<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table      = 'materials';
    protected $primaryKey = 'material_id';
    protected $fillable = ['material_name', 'category'];
    public function items(){return $this->hasMany(Item::class, 'material_id', 'material_id');}
}
