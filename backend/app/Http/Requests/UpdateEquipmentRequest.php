<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // permisos los controla el middleware de rol
    }

    public function rules(): array
    {
        // Si tu ruta es /equipment/{equipment} con binding,
        // esto obtiene el ID del modelo enlazado.
        $equipmentId = $this->route('equipment')?->id ?? $this->route('id');

        return [
            // 'sometimes' = solo valida si viene en el payload (permite updates parciales)
            'code' => [
                'sometimes','string','max:50',
                Rule::unique('equipment','code')->ignore($equipmentId)
            ],
            'name'          => ['sometimes','string','max:150'],
            'category'      => ['sometimes','nullable','string','max:100'],

            'status'        => ['sometimes','in:available,rented,workshop'],
            'location_type' => ['sometimes','in:warehouse,site'],

            // Si envías site_address, que sea string; si quieres forzar cuando sea site:
            // puedes usar required_if:location_type,site
            'site_address'  => ['sometimes','nullable','string','max:255'],

            'daily_rate'    => ['sometimes','numeric','min:0'],
            'notes'         => ['sometimes','nullable','string'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique'        => 'Ya existe un equipo con ese código.',
            'status.in'          => 'Estado inválido (usa available, rented o workshop).',
            'location_type.in'   => 'Ubicación inválida (usa warehouse o site).',
            'daily_rate.min'     => 'La tarifa diaria no puede ser negativa.',
        ];
    }
}
