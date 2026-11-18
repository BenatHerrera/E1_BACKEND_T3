import { api } from './api.js';
import { montatu } from './layout.js';

const IZENBURUAK = { inbentarioa: 'Inbentarioa', ekipamenduak: 'Ekipamenduak', erosketak: 'Erosketak' };
const mota = new URLSearchParams(location.search).get('mota');

if (!IZENBURUAK[mota]) {
  location.href = 'dashboard.html';
} else if (await montatu(IZENBURUAK[mota])) {
  document.title = IZENBURUAK[mota];
  const tbody = document.getElementById('errenkadak');
  try {
    const { zutabeak, errenkadak } = await api(`zerrendak.php?mota=${mota}`);
    const goiburua = document.getElementById('zutabeak');
    for (const z of zutabeak) goiburua.append(Object.assign(document.createElement('th'), { textContent: z }));

    if (errenkadak.length === 0) {
      const td = Object.assign(document.createElement('td'), { colSpan: zutabeak.length, className: 'hutsik', textContent: 'Ez dago daturik' });
      tbody.append(document.createElement('tr')).append(td);
    }
    for (const errenkada of errenkadak) {
      const tr = document.createElement('tr');
      for (const balioa of errenkada) {
        tr.append(Object.assign(document.createElement('td'), { textContent: balioa ?? '–' }));
      }
      tbody.append(tr);
    }
  } catch (err) {
    const td = Object.assign(document.createElement('td'), { colSpan: 5, className: 'errorea', textContent: err.message });
    tbody.append(document.createElement('tr')).append(td);
  }
}