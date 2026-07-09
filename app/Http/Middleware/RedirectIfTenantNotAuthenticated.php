<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfTenantNotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        // যদি ইউজার লগইন না থাকে অথবা সেশন এক্সপায়ার হয়ে যায়
        if (!Auth::guard($guard)->check()) {
            
            // কারেন্ট রিকোয়েস্টের হোস্ট (যেমন: house57.serpn-saas.test)
            $host = $request->getHost();

            // ইন্টেলিজেন্ট রিডাইরেক্ট: কারেন্ট সাবডোমেন ঠিক রেখে /login পেজে পাঠানো
            return redirect()->away('http://' . $host . '/login')
                ->with('error', 'Your session has expired. Please login again.');
        }
        return $next($request);
    }
}
