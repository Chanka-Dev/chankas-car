<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trabajo;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EnviarRecordatoriosRevisiones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordatorios:revisiones {--test : Modo de prueba (no envía mensajes reales)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar recordatorios de revisiones anuales y recalificaciones de cilindros por WhatsApp';

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando revisiones y recalificaciones próximas...');
        
        $modoTest = $this->option('test');
        
        if ($modoTest) {
            $this->warn('⚠️  MODO PRUEBA - No se enviarán mensajes reales');
        }

        $hoy = Carbon::today();
        $en5Dias = $hoy->copy()->addDays(5);
        $en3Dias = $hoy->copy()->addDays(3);

        $recordatoriosEnviados = 0;
        $errores = 0;

        // Obtener todos los trabajos con cliente y teléfono
        $trabajos = Trabajo::with('cliente:id_cliente,placas,telefono')
            ->whereHas('cliente', function($query) {
                $query->whereNotNull('telefono')
                      ->where('telefono', '!=', '');
            })
            ->get();

        $this->info("📋 Trabajos encontrados: {$trabajos->count()}");

        foreach ($trabajos as $trabajo) {
            // Determinar qué fecha usar según la lógica
            if ($trabajo->fecha_recalificacion) {
                // Caso: Tiene recalificación - SOLO enviar para esa fecha
                $fechaObjetivo = Carbon::parse($trabajo->fecha_recalificacion);
                $tipoRecordatorio = 'recalificacion';
            } else {
                // Caso: NO tiene recalificación - Enviar para revisión anual (fecha_trabajo + 1 año)
                $fechaRevisionAnual = Carbon::parse($trabajo->fecha_trabajo)->addYear();
                $fechaObjetivo = $fechaRevisionAnual;
                $tipoRecordatorio = 'revision';
            }

            // Verificar si hay que enviar recordatorio a 5 días
            if ($fechaObjetivo->isSameDay($en5Dias)) {
                $enviado = $this->enviarRecordatorio(
                    $trabajo,
                    $fechaObjetivo,
                    5,
                    $tipoRecordatorio,
                    $modoTest
                );
                
                if ($enviado) {
                    $recordatoriosEnviados++;
                } else {
                    $errores++;
                }
            }

            // Verificar si hay que enviar recordatorio a 3 días
            if ($fechaObjetivo->isSameDay($en3Dias)) {
                $enviado = $this->enviarRecordatorio(
                    $trabajo,
                    $fechaObjetivo,
                    3,
                    $tipoRecordatorio,
                    $modoTest
                );
                
                if ($enviado) {
                    $recordatoriosEnviados++;
                } else {
                    $errores++;
                }
            }
        }

        // Resumen
        $this->newLine();
        $this->info("✅ Recordatorios enviados: {$recordatoriosEnviados}");
        
        if ($errores > 0) {
            $this->error("❌ Errores: {$errores}");
        }

        if ($recordatoriosEnviados === 0 && $errores === 0) {
            $this->comment('ℹ️  No hay recordatorios para enviar hoy');
        }

        Log::info('Comando recordatorios:revisiones ejecutado', [
            'enviados' => $recordatoriosEnviados,
            'errores' => $errores,
            'modo_test' => $modoTest
        ]);

        return Command::SUCCESS;
    }

    /**
     * Enviar recordatorio individual
     */
    protected function enviarRecordatorio($trabajo, $fecha, $diasAntes, $tipo, $modoTest = false)
    {
        $cliente = $trabajo->cliente;
        
        if (!$cliente || !$cliente->telefono) {
            $this->warn("⚠️  Trabajo #{$trabajo->id_trabajo}: Cliente sin teléfono");
            return false;
        }

        $fechaFormateada = $fecha->format('d/m/Y');
        $placa = $cliente->placas ?? 'Sin placa';

        if ($modoTest) {
            $this->line("📱 [TEST] Enviaría a {$cliente->telefono}:");
            $this->line("   Placa: {$placa}");
            $this->line("   Tipo: " . ($tipo === 'recalificacion' ? 'Recalificación' : 'Revisión Anual'));
            $this->line("   Fecha: {$fechaFormateada} (en {$diasAntes} días)");
            return true;
        }

        try {
            if ($tipo === 'recalificacion') {
                $resultado = $this->whatsappService->enviarRecordatorioRecalificacion(
                    $cliente->telefono,
                    $placa,
                    $fechaFormateada,
                    $diasAntes
                );
            } else {
                $resultado = $this->whatsappService->enviarRecordatorioRevision(
                    $cliente->telefono,
                    $placa,
                    $fechaFormateada,
                    $diasAntes
                );
            }

            if ($resultado) {
                $this->info("✅ Enviado a {$cliente->telefono} - Placa: {$placa} ({$tipo})");
                return true;
            } else {
                $this->error("❌ Error al enviar a {$cliente->telefono}");
                return false;
            }

        } catch (\Exception $e) {
            $this->error("❌ Excepción: {$e->getMessage()}");
            Log::error("Error enviando recordatorio", [
                'trabajo_id' => $trabajo->id_trabajo,
                'telefono' => $cliente->telefono,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
