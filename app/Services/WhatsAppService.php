<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->apiKey = config('services.whatsapp.api_key');
    }

    /**
     * Enviar mensaje de recordatorio de revisión anual
     */
    public function enviarRecordatorioRevision($telefono, $placa, $fecha, $diasAntes)
    {
        $mensaje = "🔧 *Chankas Car - Recordatorio*\n\n";
        $mensaje .= "Estimado cliente, le recordamos que su vehículo con placa *{$placa}* ";
        $mensaje .= "tiene su *revisión anual de habilitación GNV* programada para el *{$fecha}*.\n\n";
        
        if ($diasAntes == 5) {
            $mensaje .= "⏰ Faltan 5 días para su cita.\n\n";
        } else {
            $mensaje .= "⏰ Faltan 3 días para su cita.\n\n";
        }
        
        $mensaje .= "📞 Para reprogramar o consultas, contáctenos.\n";
        $mensaje .= "📍 Chankas Car - Especialistas en GNV";

        return $this->enviarMensaje($telefono, $mensaje);
    }

    /**
     * Enviar mensaje de recordatorio de recalificación de cilindro
     */
    public function enviarRecordatorioRecalificacion($telefono, $placa, $fecha, $diasAntes)
    {
        $mensaje = "🔧 *Chankas Car - Recordatorio*\n\n";
        $mensaje .= "Estimado cliente, le recordamos que su vehículo con placa *{$placa}* ";
        $mensaje .= "tiene su *recalificación de cilindro GNV* programada para el *{$fecha}*.\n\n";
        
        if ($diasAntes == 5) {
            $mensaje .= "⏰ Faltan 5 días para la recalificación.\n\n";
        } else {
            $mensaje .= "⏰ Faltan 3 días para la recalificación.\n\n";
        }
        
        $mensaje .= "⚠️ Es importante realizar este proceso en la fecha indicada.\n";
        $mensaje .= "📞 Para reprogramar o consultas, contáctenos.\n";
        $mensaje .= "📍 Chankas Car - Especialistas en GNV";

        return $this->enviarMensaje($telefono, $mensaje);
    }

    /**
     * Enviar mensaje vía n8n webhook
     */
    protected function enviarMensaje($telefono, $mensaje)
    {
        try {
            // Validar número de teléfono
            $telefono = $this->formatearTelefono($telefono);
            
            if (!$telefono) {
                Log::warning("Teléfono inválido para envío de WhatsApp");
                return false;
            }

            // Enviar a n8n webhook
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'telefono' => $telefono,
                'mensaje' => $mensaje,
                'timestamp' => now()->toDateTimeString(),
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp enviado exitosamente", [
                    'telefono' => $telefono,
                    'mensaje_preview' => substr($mensaje, 0, 50) . '...'
                ]);
                return true;
            }

            Log::error("Error al enviar WhatsApp", [
                'telefono' => $telefono,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return false;

        } catch (\Exception $e) {
            Log::error("Excepción al enviar WhatsApp: " . $e->getMessage(), [
                'telefono' => $telefono,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Formatear número de teléfono a formato internacional
     * Bolivia: +591 + número
     */
    protected function formatearTelefono($telefono)
    {
        // Limpiar el número
        $telefono = preg_replace('/[^0-9]/', '', $telefono);
        
        if (empty($telefono)) {
            return null;
        }

        // Si ya tiene código de país (591), agregamos solo el +
        if (strlen($telefono) == 11 && substr($telefono, 0, 3) == '591') {
            return '+' . $telefono;
        }

        // Si tiene 8 dígitos (número boliviano normal), agregamos +591
        if (strlen($telefono) == 8) {
            return '+591' . $telefono;
        }

        // Si tiene 9 dígitos y empieza con 5 (común en Bolivia)
        if (strlen($telefono) == 9 && substr($telefono, 0, 1) == '5') {
            return '+59' . $telefono;
        }

        // Retornar el número tal cual si no coincide con patrones conocidos
        return '+' . $telefono;
    }

    /**
     * Validar si el servicio está disponible
     */
    public function verificarConexion()
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->get($this->apiUrl . '/health');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("No se pudo conectar con WhatsApp API: " . $e->getMessage());
            return false;
        }
    }
}
