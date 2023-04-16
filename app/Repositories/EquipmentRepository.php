<?php

namespace App\Repositories;

use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Support\Collection;

class EquipmentRepository
{
    /**
     * @param int $id
     * @return Equipment
     */
    public function getEquipmentById(int $id): Equipment
    {
        return Equipment::findOrFail($id);
    }

    /**
     * @param array $filter
     * @param int $pageNumber
     * @param int $perPage
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEquipmentsByFilter(array $filter, int $pageNumber = 1, int $perPage = 10): \Illuminate\Database\Eloquent\Collection
    {
        $equipments = Equipment::query()
            ->orderBy('id')
            ->skip(($pageNumber - 1) * $pageNumber)
            ->take($perPage);

        foreach ($filter as $f => $v) {
            if (in_array($f, ['serial_number', 'equipment_type_id'])) {
                $equipments->where($f, '=', $v);
            }
        }

        if (!empty($filter['q'])) {
            $equipments->orWhere('serial_number', 'like', '%' . $filter['q'] . '%');
            $equipments->orWhere('desc', 'like', '%' . $filter['q'] . '%');
        }

        return $equipments->get();
    }

    /**
     * @param array $filter
     * @param int $pageNumber
     * @param int $perPage
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEquipmentTypesByFilter(array $filter, int $pageNumber = 1, int $perPage = 10): Collection
    {
        $equipmentTypes = EquipmentType::query()
            ->orderBy('id')
            ->skip(($pageNumber - 1) * $pageNumber)
            ->take($perPage);

        foreach ($filter as $f => $v) {
            if (in_array($f, ['name', 'mask'])) {
                $equipmentTypes->where($f, '=', $v);
            }
        }

        if (!empty($filter['q'])) {
            $equipmentTypes->orWhere('name', 'like', '%' . $filter['q'] . '%');
            $equipmentTypes->orWhere('mask', 'like', '%' . $filter['q'] . '%');
        }

        return $equipmentTypes->get();
    }
}
