<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment; 

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
