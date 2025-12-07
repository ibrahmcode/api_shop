<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language') ?? $request->get('lang') ?? 'ku';
        
        // Validate locale
        $supportedLocales = ['ku', 'ar', 'en'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'ku'; // Default to Kurdish
        }
        
        app()->setLocale($locale);
        
        return $next($request);
    }
}
