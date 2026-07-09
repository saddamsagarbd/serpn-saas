import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { createIcons, icons } from 'lucide';

// ১. উইন্ডো অবজেক্টে গ্লোবালি অ্যাসাইন করুন
window.Alpine = Alpine;
window.Livewire = Livewire;

// ২. ডম (DOM) কন্টেন্ট লোড হওয়ার পর Lucide আইকন এবং লাইভওয়্যার ইনিশিয়েট করুন
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// ৩. লাইভওয়্যার বুটস্ট্র্যাপ রান করুন
Livewire.start();