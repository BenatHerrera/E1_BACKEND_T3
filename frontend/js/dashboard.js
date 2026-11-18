import { api } from './api.js';
import { montatu } from './layout.js';

if (await montatu('Hasiera')) {
  const tbody = document.getElementById('erosketak');
  try {
    const { stats, azkenErosketak } = await api('dashboard.php');
    document.getElementById('statMotak').textContent = stats.ekipamenduMotak;
    document.getElementById('statUnitateak').textContent = stats.unitateak;
    document.getElementById('statGelak').textContent = stats.gelak;
    document.getElementById('statInbentariatuta').textContent = stats.inbentariatuta;

    if (azkenErosketak.length === 0) {
      const td = Object.assign(document.createElement('td'), { colSpan: 4, className: 'hutsik', textContent: 'Ez dago erosketarik' });
      tbody.append(document.createElement('tr')).append(td);
    }
    for (const e of azkenErosketak) {
      const tr = document.createElement('tr');
      for (const balioa of [e.data, e.ekipamendua, e.kopurua, e.stockBerria]) {
        const td = document.createElement('td');
        td.textContent = balioa;
        tr.append(td);
      }
      tbody.append(tr);
    }
  } catch (err) {
    const td = Object.assign(document.createElement('td'), { colSpan: 4, className: 'errorea', textContent: err.message });
    tbody.append(document.createElement('tr')).append(td);
  }
}