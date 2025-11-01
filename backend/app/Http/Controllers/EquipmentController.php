<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment; 
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use Illuminate\Database\QueryException;



class EquipmentController extends Controller
{
 /**
     * GET /equipment
     * Parámetros opcionales:
     * - search: texto (busca en code/name/category)
     * - status: lista separada por comas (available,rented,workshop)
     * - location_type: lista separada por comas (warehouse,site)
     * - sort: campo asc/desc (name | -name | code | -daily_rate | created_at | -created_at)
     * - per_page: tamaño de página (default 20, máx 100)
     */
    /*
    public function index(Request $request)
    {
        $perPage = (int) ($request->integer('per_page') ?: 20);
        $perPage = max(1, min($perPage, 100)); // 1..100

        $query = Equipment::query()
            ->search($request->string('search'))
            ->statusIn($request->string('status'))
            ->locationIn($request->string('location_type'))
            ->sortBy($request->string('sort'));

        return $query->paginate($perPage);

     }   

*/public function index(Request $request)
{
    $query = Equipment::query();

    // filtros opcionales...
    if ($search = $request->query('search')) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    // ordenamiento robusto
    $sort = $request->query('sort', 'created_at');
    $dir  = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

    // si viene con guion delante, forzar desc y limpiar el guion
    if (str_starts_with($sort, '-')) {
        $dir  = 'desc';
        $sort = ltrim($sort, '-');
    }

    // lista blanca de columnas permitidas (por seguridad)
    $allowed = ['id','code','name','category','status','location_type','daily_rate','created_at','updated_at'];
    if (!in_array($sort, $allowed, true)) {
        $sort = 'created_at';
    }

    $query->orderBy($sort, $dir);

    $perPage = (int) $request->query('per_page', 20);
    $rows = $query->paginate($perPage);

    return response()->json([
        'data' => $rows->items(),
        'meta' => [
            'total'        => $rows->total(),
            'per_page'     => $rows->perPage(),
            'current_page' => $rows->currentPage(),
            'last_page'    => $rows->lastPage(),
        ],
    ]);
}



  // 🚮 LISTAR SOLO los de la papelera
    public function trash()
    {
        return response()->json(
            Equipment::onlyTrashed()->orderByDesc('deleted_at')->paginate(20)
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEquipmentRequest $request)
    {
        // Datos ya validados por StoreEquipmentRequest
        $data = $request->validated();

        $equipment = Equipment::create($data);

        return response()->json([
            'message' => 'Equipo creado correctamente.',
            'data'    => $equipment,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //    // $equipment llega resuelto por ID; si no existe, Laravel lanza 404
         // withTrashed() para que funcione incluso si está en la papelera
            $equipment = Equipment::withTrashed()->findOrFail($id);
    return response()->json(['data' => $equipment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentRequest $request, Equipment $equipment)
    {
        $equipment->update($request->validated());

        return response()->json([
            'message' => 'Equipo actualizado correctamente.',
            'data'    => $equipment,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $equipment = Equipment::findOrFail($id);

    // Bloquea si está rentado (ajusta los valores a tus estados reales)
    if (in_array($equipment->status, ['rented', 'rentado'])) {
        return response()->json(['message' => 'Equipment currently rented'], 409);
    }

    try {
        $equipment->delete(); // hard delete (o soft si usas SoftDeletes)
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'Cannot delete: item is referenced by other records'
        ], 409);
    }

    return response()->noContent(); // 204
}

  // ♻️ Restaurar desde papelera
    public function restore($id)
    {
        $equipment = Equipment::onlyTrashed()->findOrFail($id);
        $equipment->restore();
        return response()->json(['restored' => true, 'id' => $equipment->id], 200);
    }
 // ❌ Borrado definitivo
    public function forceDelete($id)
    {
        $equipment = Equipment::onlyTrashed()->findOrFail($id);

        try {
            $equipment->forceDelete();
        } catch (QueryException $e) {
            return response()->json(['message' => 'Cannot force delete (FK constraints)'], 409);
        }

        return response()->noContent(); // 204
    }

}
