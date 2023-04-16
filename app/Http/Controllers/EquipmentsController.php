<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEquipmentRequest;
use App\Http\Resources\EquipmentCollection;
use App\Http\Resources\EquipmentResource;
use App\Http\Resources\EquipmentTypeCollection;
use App\Http\Resources\NotFoundResource;
use App\Http\Resources\ValidationErrorsResource;
use App\Models\Equipment;
use App\Repositories\EquipmentRepository;
use App\Services\EquipmentsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EquipmentsController extends Controller
{
    /**
     * @var EquipmentsService
     */
    private EquipmentsService $equipmentsService;

    /**
     * @var EquipmentRepository
     */
    private EquipmentRepository $equipmentRepository;

    /**
     * @param EquipmentsService $equipmentsService
     * @param EquipmentRepository $equipmentRepository
     */
    public function __construct(EquipmentsService $equipmentsService, EquipmentRepository $equipmentRepository)
    {
        $this->equipmentsService = $equipmentsService;
        $this->equipmentRepository = $equipmentRepository;
    }

    /**
     * @param Request $request
     * @return EquipmentCollection
     */
    public function index(Request $request): EquipmentCollection
    {
        $filter = [];
        $pageNumber = $request->query('page_number', 1);
        $perPage = $request->query('per_page', 10);

        if ($request->has('q')) {
            $filter['q'] = $request->query('q');
        }
        if ($request->has('equipment_type_id')) {
            $filter['equipment_type_id'] = $request->query('equipment_type_id');
        }
        if ($request->has('serial_number')) {
            $filter['serial_number'] = $request->query('serial_number');
        }

        return new EquipmentCollection(
            $this->equipmentRepository->getEquipmentsByFilter($filter, $pageNumber, $perPage)
        );
    }

    /**
     * @param Request $request
     * @return EquipmentTypeCollection
     */
    public function getEquipmentTypes(Request $request): EquipmentTypeCollection
    {
        $filter = [];
        $pageNumber = $request->query('page_number', 1);
        $perPage = $request->query('per_page', 10);

        if ($request->has('q')) {
            $filter['q'] = $request->query('q');
        }
        if ($request->has('name')) {
            $filter['name'] = $request->query('name');
        }
        if ($request->has('mask')) {
            $filter['mask'] = $request->query('mask');
        }

        return new EquipmentTypeCollection(
            $this->equipmentRepository->getEquipmentTypesByFilter($filter, $pageNumber, $perPage)
        );
    }

    /**
     * @param int $id
     * @return EquipmentResource|NotFoundResource
     */
    public function show(int $id): EquipmentResource|NotFoundResource
    {
        $equipment =  Equipment::find($id);

        return $equipment !=null ? new EquipmentResource($equipment) : new NotFoundResource(null);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $items = $request->all();

        $data = $this->equipmentsService->createEquipmentsFromArray($items);

        return response()->json($data, options: JSON_FORCE_OBJECT);
    }

    /**
     * @param int $id
     * @param UpdateEquipmentRequest $request
     * @return EquipmentResource|NotFoundResource|ValidationErrorsResource
     */
    public function update(int $id, UpdateEquipmentRequest $request): EquipmentResource|NotFoundResource|ValidationErrorsResource
    {
        $data = $request->validated();

        try {
            $equipment = $this->equipmentsService->updateEquipment($id, $data);
        } catch (ValidationException $ex) {
            return new ValidationErrorsResource(['errors' => $ex->errors()]);
        } catch (ModelNotFoundException $ex) {
            return new NotFoundResource(null);
        }

        return new EquipmentResource($equipment);
    }

    /**
     * @param int $id
     * @return JsonResponse|NotFoundResource
     */
    public function remove(int $id): JsonResponse|NotFoundResource
    {
        try {
            $this->equipmentsService->deleteEquipment($id);
        } catch (ModelNotFoundException $ex) {
            return new NotFoundResource(null);
        }

        return response()->json(['success' => true]);
    }
}
