import { guard } from './guard.js';
import { nabigazioa } from './nav.js';

const user = await guard();
nabigazioa(user);

document.getElementById('izena').textContent = user.username;
document.getElementById('rola').textContent = 'Rola: ' + user.rol;
