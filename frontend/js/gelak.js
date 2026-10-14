import { api } from './api.js';
import { guard } from './guard.js';

await guard();

const form = document.getElementById('form');
const idInput = document.getElementById('id');
const izenaInput = document.getElementById('izena');
const taldeaInput = document.getElementById('taldea');
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
  const lista = await api('gelak.php');
  taula.replaceChildren();
  for (const g of lista) {
    const tr = document.createElement('tr');

    const tdAkz = document.createElement('td');
    const edit = document.createElement('button');
    edit.textContent = 'Editatu';
    edit.className = 'bigarrena';
    edit.addEventListener('click', () => editatu(g));
    const del = document.createElement('button');
    del.textContent = 'Ezabatu';
    del.className = 'arriskua';
    del.addEventListener('click', () => ezabatu(g));
    tdAkz.append(edit, del);

    tr.append(gelaxka(g.id), gelaxka(g.izena), gelaxka(g.taldea), tdAkz);
    taula.append(tr);
  }
}

function editatu(g) {
  idInput.value = g.id;
  izenaInput.value = g.izena;
  taldeaInput.value = g.taldea ?? '';
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

async function ezabatu(g) {
  if (!confirm(`"${g.izena}" ezabatu nahi duzu?`)) return;
  try {
    await api(`gelak.php?id=${g.id}`, { method: 'DELETE' });
    kargatu();
  } catch (err) {
    errorBox.textContent = err.message;
  }
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  errorBox.textContent = '';

  const izena = izenaInput.value.trim();
  const taldea = taldeaInput.value.trim();
  if (!izena) { errorBox.textContent = 'Izena beharrezkoa da'; return; }
  if (izena.length > 100 || taldea.length > 100) { errorBox.textContent = 'Gehienez 100 karaktere'; return; }

  try {
    if (idInput.value) {
      await api(`gelak.php?id=${idInput.value}`, { method: 'PUT', body: { izena, taldea } });
    } else {
      await api('gelak.php', { method: 'POST', body: { izena, taldea } });
    }
    garbitu();
    kargatu();
  } catch (err) {
    errorBox.textContent = err.message;
  }
});

utziBtn.addEventListener('click', garbitu);
kargatu();
