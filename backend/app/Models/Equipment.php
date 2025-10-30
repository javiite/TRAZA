<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    // Si no pones $table, Eloquent infiere 'equipment' desde el nombre del modelo,
    // pero lo declaramos explícito por claridad:
    protected $table = 'equipment';

    // Campos que se pueden asignar masivamente (create/update desde arrays)
    protected $fillable = [
        'code',
        'name',
        'category',
        'status',
        'location_type',
        'site_address',
        'daily_rate',
        'notes',
    ];

    // Casts: cómo quieres leer/escribir ciertos tipos
    protected $casts = [
        'daily_rate' => 'decimal:2', // siempre con 2 decimales en PHP
    ];

    /* =======================
     *  Scopes de consulta
     * ======================= */

    // Búsqueda general por code/name/category
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;
        return $q->where(function ($w) use ($term) {
            $w->where('code', 'like', "%{$term}%")
              ->orWhere('name', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%");
        });
    }

    // Filtro por estado: available,rented,workshop (uno o varios separados por coma)
    public function scopeStatusIn(Builder $q, ?string $statuses): Builder
    {
        if (!$statuses) return $q;
        $list = array_filter(array_map('trim', explode(',', $statuses)));
        return $q->whereIn('status', $list);
    }

    // Filtro por ubicación: warehouse,site (uno o varios)
    public function scopeLocationIn(Builder $q, ?string $locations): Builder
    {
        if (!$locations) return $q;
        $list = array_filter(array_map('trim', explode(',', $locations)));
        return $q->whereIn('location_type', $list);
    }

    // Ordenamiento seguro: sort=name | sort=-daily_rate
    public function scopeSortBy(Builder $q, ?string $sort): Builder
    {
        if (!$sort) {
            return $q->orderByDesc('created_at'); // orden por defecto
        }

        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $field = ltrim($sort, '-');

        // Campos permitidos para ordenar
        $allowed = ['name', 'code', 'daily_rate', 'created_at'];
        if (!in_array($field, $allowed, true)) {
            return $q->orderByDesc('created_at'); // fallback seguro
        }

        return $q->orderBy($field, $direction);
    }
}
