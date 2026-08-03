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

    protected function registrarMetrica(Cuadro $cuadro, string $accion, ?string $origenForzado = null, ?string $detalle = null): void
    {
        $this->registrarEvento($accion, $detalle, $cuadro, $origenForzado);
    }

    protected function registrarEvento(string $accion, ?string $detalle = null, ?Cuadro $cuadro = null, ?string $origenForzado = null): void
    {
        if (auth()->check()) {
            return;
        }

        $vuid = request()->attributes->get('_vuid');
        if (!$vuid) return;

        $origen = $origenForzado ?? $this->detectarOrigen();

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

    protected function detectarOrigen(): string
    {
        $referer = request()->header('referer');
        if (!$referer) return 'directo';

        $path = parse_url($referer, PHP_URL_PATH) ?? '';

        if (str_contains($path, '/sigem-v2/catalogo')) return 'catalogo';
        if (str_contains($path, '/sigem-v2/estadistica')) return 'estadistica';
        if (preg_match('#/sigem-v2/cuadro/\d+/dataset#', $path)) return 'dataset';
        if (preg_match('#/sigem-v2/cuadro/\d+/grafica#', $path)) return 'grafica';
        if (preg_match('#/sigem-v2/cuadro/\d+/mapa#', $path)) return 'mapa';

        return 'directo';
    }

    protected function tieneRefererVisor(): bool
    {
        $referer = request()->header('referer');
        if (!$referer) return false;
        return str_contains(parse_url($referer, PHP_URL_PATH) ?? '', '/sigem-v2/');
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
