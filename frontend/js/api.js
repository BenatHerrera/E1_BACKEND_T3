import { API_URL } from './config.js';

export async function api(path, { method = 'GET', body } = {}) {
  const res = await fetch(`${API_URL}/${path}`, {
    method,
    credentials: 'include',
    headers: body ? { 'Content-Type': 'application/json' } : {},
    body: body ? JSON.stringify(body) : undefined,
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw Object.assign(new Error(data.error || 'Errorea'), { status: res.status });
  return data;
}