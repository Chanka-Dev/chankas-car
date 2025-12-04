<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use ReCaptcha\ReCaptcha;

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo validar en producción y si las claves están configuradas
        if (app()->environment('production') && config('services.recaptcha.secret_key')) {
            $recaptchaToken = $request->input('g-recaptcha-response');
            
            if (!$recaptchaToken) {
                return back()->withErrors(['recaptcha' => 'Por favor completa la verificación de seguridad.']);
            }

            $recaptcha = new ReCaptcha(config('services.recaptcha.secret_key'));
            $response = $recaptcha->verify($recaptchaToken, $request->ip());

            if (!$response->isSuccess()) {
                return back()->withErrors(['recaptcha' => 'Verificación de seguridad fallida. Intenta nuevamente.']);
            }

            // Verificar score (para reCAPTCHA v3)
            if ($response->getScore() < 0.5) {
                return back()->withErrors(['recaptcha' => 'Actividad sospechosa detectada.']);
            }
        }

        return $next($request);
    }
}
