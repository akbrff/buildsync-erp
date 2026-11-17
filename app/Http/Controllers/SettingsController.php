<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index()
    {
        $currentLocale = Auth::user()->locale ?? session('locale', config('app.locale', 'en'));
        return view('settings.index', compact('currentLocale'));
    }

    /**
     * Update the language setting for the authenticated user.
     */
    public function updateLanguage(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:en,id'],
        ]);

        $locale = $validated['locale'];

        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        $request->session()->put('locale', $locale);

        return redirect()->route('settings.index')->with('success', __('settings.saved_successfully'));
    }

    /**
     * Quick switcher for topbar and guest login interface.
     */
    public function switchLocale(Request $request, string $locale)
    {
        if (!in_array($locale, ['en', 'id'], true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        $request->session()->put('locale', $locale);

        return back();
    }
}
