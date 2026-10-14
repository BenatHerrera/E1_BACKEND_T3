import { api } from './api.js';

export async function guard() {
  try {
    return await api('me.php');
  } catch {
    location.href = 'index.html';
  }
}