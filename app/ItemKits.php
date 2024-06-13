<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemKits extends Model
{
    //
    protected $table = "ospos_item_kits";

    public $timestamps = false; 
    protected $fillable = [
     

        'item_kit_id',
        'name',
        'description',
        'branch_code',
        'deleted'

    ];
    public function items()
    {
        // Assuming there is a foreign key 'item_kit_id' in ItemKitItemsOnline
        return $this->hasMany(ItemKitItems::class, 'item_kit_id','item_kit_id')->where('deleted', 0);
    }
}
