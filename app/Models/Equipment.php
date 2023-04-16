<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;

    protected $table = 'equipments';

    protected $fillable = [
        'equipment_type_id',
        'serial_number',
        'desc'
    ];

    public function equipmentType(): HasOne
    {
        return $this->hasOne(EquipmentType::class, 'id', 'equipment_type_id');
    }
}
