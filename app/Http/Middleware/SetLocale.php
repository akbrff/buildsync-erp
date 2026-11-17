<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales in BuildSync ERP.
     */
    protected array $supportedLocales = ['en', 'id'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. Authenticated user's saved language preference (takes absolute precedence)
        if (Auth::check() && Auth::user()->locale) {
            $userLocale = Auth::user()->locale;
            if (in_array($userLocale, $this->supportedLocales, true)) {
                $locale = $userLocale;
                if ($request->hasSession() && $request->session()->get('locale') !== $userLocale) {
                    $request->session()->put('locale', $userLocale);
                }
            }
        }

        // 2. Guest/session language preference
        if (!$locale && $request->hasSession() && $request->session()->has('locale')) {
            $sessionLocale = $request->session()->get('locale');
            if (in_array($sessionLocale, $this->supportedLocales, true)) {
                $locale = $sessionLocale;
            }
        }

        // 3. Fallback to English ('en')
        if (!$locale || !in_array($locale, $this->supportedLocales, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
