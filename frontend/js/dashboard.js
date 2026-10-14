import { api } from './api.js';

try {
  const user = await api('me.php');
  document.getElementById('izena').textContent = user.username;
  document.getElementById('rola').textContent = 'Rola: ' + user.rol;
} catch (err) {
  location.href = 'index.html';
}

document.getElementById('logout').addEventListener('click', async () => {
  await api('logout.php', { method: 'POST' });
  location.href = 'index.html';
});
