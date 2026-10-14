import { api } from './api.js';

const form = document.getElementById('loginForm');
const errorBox = document.getElementById('errorea');

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  errorBox.textContent = '';

  const user = form.user.value.trim();
  const password = form.password.value;

  if (!user || !password) {
    errorBox.textContent = 'Bete eremu guztiak';
    return;
  }

  try {
    await api('login.php', { method: 'POST', body: { user, password } });
    location.href = 'dashboard.html';
  } catch (err) {
    errorBox.textContent = err.message;
  }
});