<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ya controlaremos permisos por middleware; aquí permitimos validar.
        return true;
    }

    public function rules(): array
    {
        return [
            'code'          => ['required','string','max:50','unique:equipment,code'],
            'name'          => ['required','string','max:150'],
            'category'      => ['nullable','string','max:100'],

            // Solo valores permitidos (enums de la BD)
            'status'        => ['required','in:available,rented,workshop'],
            'location_type' => ['required','in:warehouse,site'],

            'site_address'  => ['nullable','string','max:255'],

            // MXN con dos decimales, no negativo
            'daily_rate'    => ['required','numeric','min:0'],

            'notes'         => ['nullable','string'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'   => 'El código es obligatorio.',
            'code.unique'     => 'Ya existe un equipo con ese código.',
            'status.in'       => 'Estado inválido (usa available, rented o workshop).',
            'location_type.in'=> 'Ubicación inválida (usa warehouse o site).',
            'daily_rate.min'  => 'La tarifa diaria no puede ser negativa.',
        ];
    }
}
