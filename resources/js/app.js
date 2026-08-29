import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { createIcons, icons } from 'lucide';

import $ from 'jquery';
window.$ = window.jQuery = $;

import select2 from 'select2';
select2($);
import 'select2/dist/css/select2.min.css';

window.Alpine = Alpine;
window.Livewire = Livewire;

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

Livewire.start();