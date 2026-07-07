import './bootstrap';

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.Livewire = Livewire;

Livewire.start();

createIcons({ icons });
