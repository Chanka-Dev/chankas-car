<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Enviar recordatorios de revisiones y recalificaciones
        // Se ejecuta todos los días a las 9:00 AM
        $schedule->command('recordatorios:revisiones')
                 ->dailyAt('09:00')
                 ->timezone('America/La_Paz')
                 ->appendOutputTo(storage_path('logs/recordatorios.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
