<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'equipment_type' => [
                'id' => $this->equipmentType->id,
                'name' => $this->equipmentType->name,
                'mask' => $this->equipmentType->mask,
            ],
            'serial_number' => $this->serial_number,
            'desc' => $this->desc,
            'created_at' => $this->created_at->format('Y-m-d H:j:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:j:s')
        ];
    }
}
