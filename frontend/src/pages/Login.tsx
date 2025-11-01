import { useState } from "react";
import { api, setToken } from "../lib/api";
import { useNavigate } from "react-router-dom";

export default function Login() {
  const navigate = useNavigate();
  const [email, setEmail] = useState("admin@traza.local");
  const [password, setPassword] = useState("password");
  const [loading, setLoading] = useState(false);
  const [err, setErr] = useState<string | null>(null);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setErr(null);
    try {
      const { data } = await api.post("/login", { email, password });
      setToken(data.token); // guarda en localStorage + header Authorization
      navigate("/");        // manda al inventario
    } catch (error: any) {
      const msg = error?.response?.data?.message || "Error al iniciar sesión";
      setErr(msg);
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center p-6">
      <form onSubmit={onSubmit} className="w-full max-w-sm border rounded-xl p-6 space-y-4">
        <h1 className="text-2xl font-bold">Entrar a TRAZA</h1>
        {err && <div className="text-red-600 text-sm">{err}</div>}
        <div className="space-y-1">
          <label className="text-sm">Email</label>
          <input className="border p-2 w-full rounded" value={email} onChange={e=>setEmail(e.target.value)} />
        </div>
        <div className="space-y-1">
          <label className="text-sm">Contraseña</label>
          <input className="border p-2 w-full rounded" type="password" value={password} onChange={e=>setPassword(e.target.value)} />
        </div>
        <button
          disabled={loading}
          className="w-full py-2 rounded bg-black text-white disabled:opacity-60"
        >
          {loading ? "Entrando..." : "Entrar"}
        </button>
      </form>
    </div>
  );
}
