<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // 🚀 টেন্যান্ট কনটেক্সট চেক করে ডাইনামিক রিডাইরেকশন
        if (tenant()) {
            // টেন্যান্ট ইউজার হলে তাকে তার নিজস্ব লাইভওয়্যার ড্যাশবোর্ডে রিডাইরেক্ট করুন
            $this->redirect(route('tenant.dashboard'), navigate: true);
        } else {
            // সেন্ট্রাল সুপার এডমিন হলে ডিফল্ট ড্যাশবোর্ডে যাবে
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- 🏢 ডাইনামিক টাইটেল (সেন্ট্রাল বনাম টেন্যান্ট) -->
    <div class="mb-6 text-center">
        @if(tenant())
            <!-- টেন্যান্ট সাবডোমেইনে থাকলে তার কোম্পানির নাম দেখাবে -->
            <h2 class="text-xl font-bold text-gray-800">Login to {{ tenant('company_name') ?? 'Your Store' }}</h2>
            <p class="text-xs text-gray-500 mt-1">Tenant Dashboard Portal</p>
        @else
            <!-- মেইন সেন্ট্রাল ডোমেইনে থাকলে সুপার অ্যাডমিন টাইটেল দেখাবে -->
            <h2 class="text-xl font-bold text-gray-800">Central Admin Portal</h2>
            <p class="text-xs text-gray-500 mt-1">Sign in to manage the SaaS</p>
        @endif
    </div>

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>
