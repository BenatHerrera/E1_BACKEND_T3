import { api } from './api.js';
import { guard } from './guard.js';

await guard();

const form = document.getElementById('form');
const idInput = document.getElementById('id');
const izenaInput = document.getElementById('izena');
const errorBox = document.getElementById('errorea');
const gordeBtn = document.getElementById('gorde');
const utziBtn = document.getElementById('utzi');
const taula = document.getElementById('taula');

async function kargatu() {
  const lista = await api('kategoriak.php');
  taula.replaceChildren();
  for (const k of lista) {
    const tr = document.createElement('tr');

    const tdId = document.createElement('td');
    tdId.textContent = k.id;
    const tdIzena = document.createElement('td');
    tdIzena.textContent = k.izena;          // textContent evita inyección de HTML

    const tdAkz = document.createElement('td');
    const edit = document.createElement('button');
    edit.textContent = 'Editatu';
    edit.className = 'bigarrena';
    edit.addEventListener('click', () => editatu(k));
    const del = document.createElement('button');
    del.textContent = 'Ezabatu';
    del.className = 'arriskua';
    del.addEventListener('click', () => ezabatu(k));
    tdAkz.append(edit, del);

    tr.append(tdId, tdIzena, tdAkz);
    taula.append(tr);
  }
}

function editatu(k) {
  idInput.value = k.id;
  izenaInput.value = k.izena;
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

async function ezabatu(k) {
  if (!confirm(`"${k.izena}" ezabatu nahi duzu?`)) return;
  try {
    await api(`kategoriak.php?id=${k.id}`, { method: 'DELETE' });
    kargatu();
  } catch (err) {
    errorBox.textContent = err.message;
  }
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  errorBox.textContent = '';

  // Validación del cliente
  const izena = izenaInput.value.trim();
  if (!izena) { errorBox.textContent = 'Izena beharrezkoa da'; return; }
  if (izena.length > 100) { errorBox.textContent = 'Gehienez 100 karaktere'; return; }

  try {
    if (idInput.value) {
      await api(`kategoriak.php?id=${idInput.value}`, { method: 'PUT', body: { izena } });
    } else {
      await api('kategoriak.php', { method: 'POST', body: { izena } });
    }
    garbitu();
    kargatu();
  } catch (err) {
    errorBox.textContent = err.message;
  }
});

utziBtn.addEventListener('click', garbitu);
kargatu();