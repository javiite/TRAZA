// src/lib/api.ts
import axios from "axios";

export const api = axios.create({
  baseURL: "http://localhost:9000/api", // tu backend local
});

// Lee token de localStorage si existe
const saved = localStorage.getItem("token");
if (saved) {
  api.defaults.headers.common["Authorization"] = `Bearer ${saved}`;
}

// Helper para establecer/cambiar el token (lo usaremos pronto)
export function setToken(token?: string) {
  if (token) {
    localStorage.setItem("token", token);
    api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  } else {
    localStorage.removeItem("token");
    delete api.defaults.headers.common["Authorization"];
  }
}
