import './bootstrap';

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

window.Alpine = Alpine;
window.Livewire = Livewire;

Livewire.start();

console.log('Livewire:', window.Livewire);
console.log('Alpine:', window.Alpine);