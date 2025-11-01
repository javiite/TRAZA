// src/features/inventory/Inventory.tsx

import { keepPreviousData, useQuery } from "@tanstack/react-query";

import { setToken, api } from "../lib/api";
import { useEffect, useState } from "react";

<button
  className="border px-3 py-1 rounded"
  onClick={async ()=>{
    try { await api.post("/logout"); } catch {}
    setToken(undefined);
    location.href = "/login";
  }}
>
  Cerrar sesión
</button>

type Equipment = {
  id: number;
  code?: string;
  name: string;
  category?: string;
  status?: string;
  location_type?: string;
  site_address?: string;
  daily_rate?: number;
  notes?: string;
  created_at?: string;
  updated_at?: string;
};

type Paginated<T> = {
  data: T[];
  meta: { total: number; per_page: number; current_page: number; last_page: number };
};

function useDebounced<T>(value: T, delay = 500) {
  const [debounced, setDebounced] = useState(value);
  useEffect(() => {
    const id = setTimeout(() => setDebounced(value), delay);
    return () => clearTimeout(id);
  }, [value, delay]);
  return debounced;
}


export default function Inventory() {
const [page, setPage] = useState(1);
  const [perPage] = useState(20); // si quieres, cámbialo a 10/50/etc.
  const [search, setSearch] = useState("");   // 1) aquí

    const { data, isLoading, isError, error, isFetching } = useQuery<Paginated<Equipment>>({
    queryKey: ["equipment", page, perPage, search],
    queryFn: async () => {
      const res = await api.get<Paginated<Equipment>>("/equipment", {
        params: {
          page,
          per_page: perPage,
          sort: "-created_at",
          ...(search ? { search } : {}), // solo envía 'search' si hay texto
        },
      });
      return res.data;
    },
    placeholderData: keepPreviousData, // mantiene los datos previos mientras llega la nueva página
    staleTime: 10_000,
  });

  if (isLoading) return <div className="p-6">Cargando inventario…</div>;
  if (isError) {
    const msg = (error as any)?.response?.data?.message || (error as Error).message;
    return (
      <div className="p-6">
        <div>Error al cargar: {msg}</div>
        <p className="text-sm text-gray-500 mt-2">¿Tienes el token activo?</p>
      </div>
    );
  }

  const total = data?.meta.total ?? 0;
  const current = data?.meta.current_page ?? 1;
  const last = data?.meta.last_page ?? 1;

  return (
 <div className="p-6 space-y-4">{/* <-- único nodo raíz */}
      <div className="flex items-center justify-between gap-4">
        <h1 className="text-2xl font-bold">Inventario</h1>

        <div className="flex-1 max-w-sm">
          <input
            placeholder="Buscar por código, nombre o categoría…"
            className="border p-2 w-full rounded"
            value={search}
            onChange={(e) => {
              setSearch(e.target.value);
              setPage(1);
            }}
          />
        </div>

        <div className="text-sm text-gray-600 whitespace-nowrap">
          {isFetching ? "Actualizando…" : `Total: ${total} · Página ${current}/${last}`}
        </div>
      </div>

      <table className="w-full text-sm border-collapse">
        <thead>
          <tr className="bg-gray-100 text-left">
            <th className="p-2 border">Código</th>
            <th className="p-2 border">Nombre</th>
            <th className="p-2 border">Estado</th>
            <th className="p-2 border">Ubicación</th>
            <th className="p-2 border">Tarifa/Día</th>
          </tr>
        </thead>
        <tbody>
          {data?.data?.map((e) => (
            <tr key={e.id} className="hover:bg-gray-50">
              <td className="p-2 border">{e.code ?? "—"}</td>
              <td className="p-2 border">{e.name}</td>
              <td className="p-2 border">{e.status ?? "—"}</td>
              <td className="p-2 border">{e.location_type ?? "—"}</td>
              <td className="p-2 border">
                {e.daily_rate != null ? `$${e.daily_rate}` : "—"}
              </td>
    </tr>
        ))}
        </tbody>
      </table>

      <div className="flex items-center gap-2">
        <button
          className="border px-3 py-1 rounded disabled:opacity-50"
          onClick={() => setPage((p) => Math.max(1, p - 1))}
          disabled={current <= 1}
        >
          ← Anterior
        </button>

        <span className="text-sm text-gray-600">
          Página {current} de {last}
        </span>

        <button
          className="border px-3 py-1 rounded disabled:opacity-50"
          onClick={() => setPage((p) => Math.min(last, p + 1))}
          disabled={current >= last}
        >
          Siguiente →
        </button>
      </div>
    </div>
  );
}

