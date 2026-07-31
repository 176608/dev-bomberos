<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\PubVisitante;
use App\Models\SIGEM\PubVisita;
use App\Traits\HashIp;

abstract class Controller extends \Illuminate\Routing\Controller
{
    use HashIp;

    protected function tieneCredenciales(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('Desarrollador') ||
            auth()->user()->hasRole('Estadistico')
        );
    }

    protected function esDesarrollador(): bool
    {
        return $this->tieneCredenciales();
    }

    protected function verificarAccesoCuadro(Cuadro $cuadro): ?array
    {
        if ($cuadro->publicado) return null;
        if ($this->tieneCredenciales()) return null;
        return ['error' => 'No tienes permiso para acceder a este cuadro.'];
    }

    protected function registrarMetrica(Cuadro $cuadro, string $accion): void
    {
        $this->registrarEvento($accion, null, $cuadro);
    }

    protected function registrarEvento(string $accion, ?string $detalle = null, ?Cuadro $cuadro = null): void
    {
        $vuid = request()->attributes->get('_vuid');
        if (!$vuid) return;

        $referer = request()->header('referer');
        $origen = 'directo';

        if ($referer) {
            $path = parse_url($referer, PHP_URL_PATH);
            if (str_contains($path, '/sigem-v2/catalogo')) {
                $origen = 'catalogo';
            } elseif (str_contains($path, '/sigem-v2/estadistica')) {
                $origen = 'estadistica';
            }
        }

        $esBot = $this->detectarBot();

        $visitante = PubVisitante::firstOrCreate(
            ['vuid' => $vuid],
            [
                'es_bot' => $esBot,
                'user_id' => auth()->id(),
                'ip_hash' => $this->hashIp(request()->ip()),
                'ip_bruta' => $esBot ? request()->ip() : null,
                'primer_visita' => now(),
                'ultima_visita' => now(),
                'total_visitas' => 0,
            ]
        );

        $visitante->increment('total_visitas');
        $visitante->forceFill([
            'ultima_visita' => now(),
            'user_id' => auth()->id(),
            'ip_hash' => $this->hashIp(request()->ip()),
            'ip_bruta' => $esBot ? request()->ip() : null,
        ])->save();

        PubVisita::create([
            'visitante_id' => $visitante->visitante_id,
            'cuadro_id'    => $cuadro?->cuadro_id,
            'accion'       => $accion,
            'detalle'      => $detalle,
            'origen'       => $origen,
            'created_at'   => now(),
        ]);
    }

    protected function detectarBot(): bool
    {
        $ua = request()->userAgent();
        if (!$ua) return false;

        $bots = ['googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
                 'yandexbot', 'facebot', 'twitterbot', 'whatsapp', 'linkedin',
                 'telegrambot', 'chatgpt', 'gptbot', 'claude', 'anthropic',
                 'perplexity', 'bytespider', 'semrush', 'ahrefsbot',
                 'mj12bot', 'dotbot', 'applebot', 'ccbot'];

        $ua = mb_strtolower($ua);
        foreach ($bots as $bot) {
            if (str_contains($ua, $bot)) return true;
        }
        return false;
    }
}
