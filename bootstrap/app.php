<?php

use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->redirectGuestsTo('/sekolahku');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'code' => $e->getStatusCode(),
                    'message' => $e->getMessage() ?: match ($e->getStatusCode()) {
                        401 => 'Autentikasi diperlukan.',
                        403 => 'Akses ditolak.',
                        404 => 'Sumber daya tidak ditemukan.',
                        419 => 'Sesi kedaluwarsa.',
                        429 => 'Terlalu banyak permintaan.',
                        503 => 'Layanan dalam pemeliharaan.',
                        default => 'Terjadi kesalahan pada sistem.',
                    },
                ], $e->getStatusCode());
            }
        });
    })->create();
