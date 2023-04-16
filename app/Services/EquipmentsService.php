<?php

namespace App\Services;

use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EquipmentsService
{
    /*
        N – цифра от 0 до 9;
        A – прописная буква латинского алфавита;
        a – строчная буква латинского алфавита;
        X – прописная буква латинского алфавита либо цифра от 0 до 9;
        Z –символ из списка: “-“, “_”, “@”.
     */
    public const MASK_RULES = [
        'N' => '[0-9]',
        'A' => '[A-Z]',
        'a' => '[a-z]',
        'X' => '[A-Z0-9]',
        'Z' => '[\-_@]',
    ];

    /**
     * @param array $data
     * @return Equipment
     */
    public function createEquipment(array $data): Equipment
    {
        $equipment = new Equipment();
        $equipment->fill($data);
        $equipment->save();

        return $equipment;
    }

    /**
     * @param array $items
     * @return array
     * @throws ValidationException
     */
    public function createEquipmentsFromArray(array $items): array
    {
        $errors = collect([]);
        $equipments = collect([]);

        $validator = Validator::make([], [
            'equipment_type_id' => 'required|integer',
            'serial_number' => 'required|string',
            'desc' => 'nullable|string',
        ]);

        foreach($items as $k => $item) {
            $validator->setData($item);

            if ($validator->fails()) {
                $errors->put($k, $validator->errors()->all());
            } else {
                $data = $validator->validated();
                try {
                    if ($this->isValidSerialNumber($data['equipment_type_id'], $data['serial_number'])) {
                        $equipment = $this->createEquipment($data);
                        $equipments->put($k, new EquipmentResource($equipment));
                    } else {
                        $errors->put($k, ['The serial number is not valid']);
                    }
                } catch (\Exception $ex) {
                    if ($ex instanceof ModelNotFoundException) {
                        $errors->put($k, ['The equipment type is not found']);
                    } elseif ($ex instanceof QueryException && $ex->errorInfo[1] === 1062) {
                        $errors->put($k, ['Duplicate entry']);
                    }
                }
            }
        }

        return [
            'errors' => $errors,
            'success' => $equipments,
        ];
    }

    /**
     * @param int $id
     * @param array $data
     * @return Equipment
     * @throws ValidationException
     */
    public function updateEquipment(int $id, array $data): Equipment
    {
        if ($this->isValidSerialNumber($data['equipment_type_id'], $data['serial_number'])) {
            try {
                $equipment = Equipment::findOrFail($id);
                $equipment->fill($data);
                $equipment->save();
            } catch (QueryException $ex) {
                if ($ex->errorInfo[1] === 1062) {
                    throw ValidationException::withMessages(['serial_number' => 'Duplicate entry']);
                } else {
                    throw ValidationException::withMessages(['unknown' => $ex->errorInfo[2]]);
                }
            }
        } else {
            throw ValidationException::withMessages(['serial_number' => 'This value is incorrect']);
        }

        return $equipment;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteEquipment(int $id): bool
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        return true;
    }

    /**
     * @param int $equipmentTypeId
     * @param string $serialNumber
     * @return bool
     */
    public function isValidSerialNumber(int $equipmentTypeId, string $serialNumber): bool
    {
        $equipmentType = EquipmentType::find($equipmentTypeId);

        if ($equipmentType && strlen($equipmentType->mask) === strlen($serialNumber)) {
            $mask = $equipmentType->mask;

            $reg = '';
            for ($i = 0; $i < strlen($mask); $i++) {
                $reg .= self::MASK_RULES[$mask[$i]] . '{1}';
            }
            $reg = '/^' . $reg . '$/';

            return preg_match($reg, $serialNumber) !== 0;
        }

        return false;
    }

    /**
     * @param string $mask
     * @return bool
     */
    public function isValidMask(string $mask): bool
    {
        for ($i = 0; $i < strlen($mask); $i++) {
            if (!isset(self::MASK_RULES[$mask[$i]])) {
                return false;
            }
        }

        return true;
    }
}
