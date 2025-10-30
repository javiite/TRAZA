/* import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import './App.css'

function App() {
  const [count, setCount] = useState(0)

  return (
    <>
      <div>
        <a href="https://vite.dev" target="_blank">
          <img src={viteLogo} className="logo" alt="Vite logo" />
        </a>
        <a href="https://react.dev" target="_blank">
          <img src={reactLogo} className="logo react" alt="React logo" />
        </a>
      </div>
      <h1>Vite + React</h1>
      <div className="card">
        <button onClick={() => setCount((count) => count + 1)}>
          count is {count}
        </button>
        <p>
          Edit <code>src/App.tsx</code> and save to test HMR
        </p>
      </div>
      <p className="read-the-docs">
        Click on the Vite and React logos to learn more
      </p>
    </>
  )
}

export default App
*/
// En tu App.tsx (o donde hiciste la prueba)
import { useState } from 'react';

export default function App() {
  const [result, setResult] = useState<any>(null);

  const testLogin = async () => {
    const res = await fetch(`${import.meta.env.VITE_API_URL}/api/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ email: 'admin@traza.local', password: 'password' }),
    });
    const data = await res.json();
    localStorage.setItem('token', data.token);
    setResult({ step: 'login', status: res.status, data });
  };

  const testMe = async () => {
    const token = localStorage.getItem('token') ?? '';
    const res = await fetch(`${import.meta.env.VITE_API_URL}/api/me`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
    });
    const data = await res.json();
    setResult({ step: 'me', status: res.status, data });
  };

  return (
    <div style={{ padding: 24 }}>
      <h1>Test API</h1>
      <button onClick={testLogin}>Probar /api/login</button>{' '}
      <button onClick={testMe}>Probar /api/me</button>

      {result && (
        <>
          <pre>STATUS: {result.status}</pre>
          <pre>{JSON.stringify(result.data, null, 2)}</pre>
        </>
      )}
    </div>
  );
}
