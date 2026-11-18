// URL de la API según dónde se abra el frontend.
// - Servidor (www.talde2.edu): el backend es un sitio aparte, server.talde2.edu
// - Resto (localhost, php -S, Apache local): ../backend/api respecto al frontend
// Para forzar otra URL: localStorage.setItem('API_URL', 'https://...')
const override = window.API_URL_OVERRIDE || (() => {
  try { return localStorage.getItem('API_URL'); } catch { return null; }
})();

const porDefecto = location.hostname === 'www.talde2.edu'
  ? 'https://server.talde2.edu/api'
  : new URL('../backend/api', location.href).href;

export const API_URL = (override || porDefecto).replace(/\/$/, '');