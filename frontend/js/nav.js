// Menú horizontal común. Para añadir una sección, añade una línea aquí.
import { api } from './api.js';

const ESTEKAK = [
  { href: 'dashboard.html',  testua: 'Hasiera' },
  { href: 'gelak.html',      testua: 'Gelak' },
  { href: 'kategoriak.html', testua: 'Kategoriak' },
  // { href: 'baliabideak.html', testua: 'Baliabideak' },
  // { href: 'gailuak.html',     testua: 'Gailuak' },
  // { href: 'mugimenduak.html', testua: 'Mugimenduak' },
  // { href: 'erabiltzaileak.html', testua: 'Erabiltzaileak', admin: true },
];

export function nabigazioa(user) {
  const goiburua = document.getElementById('nav');
  const uneko = location.pathname.split('/').pop() || 'dashboard.html';

  const nav = document.createElement('nav');
  const ul = document.createElement('ul');

  for (const e of ESTEKAK) {
    if (e.admin && user.rol !== 'admin') continue;
    const li = document.createElement('li');
    const a = document.createElement('a');
    a.href = e.href;
    a.textContent = e.testua;
    if (e.href === uneko) a.className = 'aktiboa';
    li.append(a);
    ul.append(li);
  }
  nav.append(ul);

  const erabiltzailea = document.createElement('div');
  erabiltzailea.className = 'nav-erabiltzailea';
  const izena = document.createElement('span');
  izena.textContent = `${user.username} (${user.rol})`;
  const logout = document.createElement('button');
  logout.type = 'button';
  logout.className = 'bigarrena';
  logout.textContent = 'Saioa itxi';
  logout.addEventListener('click', async () => {
    await api('logout.php', { method: 'POST' });
    location.href = 'index.html';
  });
  erabiltzailea.append(izena, logout);

  goiburua.replaceChildren(nav, erabiltzailea);
}
