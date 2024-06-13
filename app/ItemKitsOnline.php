<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemKitsOnline extends Model
{
    //
    protected $connection = 'online';
    
    protected $table = "item_kits";

    public $timestamps = false; 
    protected $fillable = [
     

        'item_kit_id',
        'name',
        'description',
        'branch_code',

    ];
    public function items()
    {
        // Assuming there is a foreign key 'item_kit_id' in ItemKitItemsOnline
        return $this->hasMany(ItemKitItemsOnline::class, 'item_kit_id','item_kit_id');
    }
}
