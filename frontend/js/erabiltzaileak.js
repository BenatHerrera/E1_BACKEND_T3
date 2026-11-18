import { api } from './api.js';
import { guard } from './guard.js';

await guard();

const form = document.getElementById('form');
const idInput = document.getElementById('id');
const izenaInput = document.getElementById('izena');
const erabiltzaileaInput = document.getElementById('erabiltzailea');
const pasahitzaInput = document.getElementById('pasahitza');
const rolaInput = document.getElementById('rola');
const errorBox = document.getElementById('errorea');
const gordeBtn = document.getElementById('gorde');
const utziBtn = document.getElementById('utzi');
const taula = document.getElementById('taula');

function gelaxka(testua) {
  const td = document.createElement('td');
  td.textContent = testua ?? '';
  return td;
}

async function kargatu() {
  const lista = await api('erabiltzaileak.php');
  taula.replaceChildren();
  for (const u of lista) {
    const tr = document.createElement('tr');

    const tdAkz = document.createElement('td');
    const edit = document.createElement('button');
    edit.textContent = 'Editatu';
    edit.className = 'bigarrena';
    edit.addEventListener('click', () => editatu(u));
    const del = document.createElement('button');
    del.textContent = 'Ezabatu';
    del.className = 'arriskua';
    del.addEventListener('click', () => ezabatu(u));
    tdAkz.append(edit, del);

    tr.append(gelaxka(u.id), gelaxka(u.izena), gelaxka(u.erabiltzailea), gelaxka(u.rola), tdAkz);
    taula.append(tr);
  }
}

function editatu(u) {
  idInput.value = u.id;
  izenaInput.value = u.izena;
  erabiltzaileaInput.value = u.erabiltzailea;
  pasahitzaInput.value = '';
  rolaInput.value = u.rola;
  gordeBtn.textContent = 'Gorde';
  utziBtn.hidden = false;
  izenaInput.focus();
}

function garbitu() {
  form.reset();
  idInput.value = '';
  gordeBtn.textContent = 'Gehitu';
  utziBtn.hidden = true;
  errorBox.textContent = '';
}

async function ezabatu(u) {
  if (!confirm(`"${u.izena}" ezabatu nahi duzu?`)) return;
  try {
    await api(`erabiltzaileak.php?id=${u.id}`, { method: 'DELETE' });
    kargatu();
  } catch (err) {
    errorBox.textContent = err.message;
  }
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  errorBox.textContent = '';

  const izena = izenaInput.value.trim();
  const erabiltzailea = erabiltzaileaInput.value.trim();
  const pasahitza = pasahitzaInput.value;
  const rola = rolaInput.value;
  const editatzen = Boolean(idInput.value);

  if (!izena) { errorBox.textContent = 'Izena beharrezkoa da'; return; }
  if (!erabiltzailea) { errorBox.textContent = 'Erabiltzailea beharrezkoa da'; return; }
  if (!editatzen && pasahitza.length < 6) {
    errorBox.textContent = 'Pasahitzak gutxienez 6 karaktere izan behar ditu'; return;
  }
  if (pasahitza !== '' && pasahitza.length < 6) {
    errorBox.textContent = 'Pasahitzak gutxienez 6 karaktere izan behar ditu'; return;
  }
  if (!['admin', 'user'].includes(rola)) { errorBox.textContent = 'Rola baliogabea'; return; }

  const body = { izena, erabiltzailea, rola };
  if (pasahitza !== '') body.pasahitza = pasahitza;

  try {
    if (editatzen) {
      await api(`erabiltzaileak.php?id=${idInput.value}`, { method: 'PUT', body });
    } else {
      await api('erabiltzaileak.php', { method: 'POST', body: { ...body, pasahitza } });
    }
    garbitu();
    kargatu();
  } catch (err) {
    errorBox.textContent = err.message;
  }
});

utziBtn.addEventListener('click', garbitu);
kargatu();