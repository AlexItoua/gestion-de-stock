<?php
// ============================================================
// app/Console/Kernel.php
// Tâches planifiées (cron jobs)
// ============================================================
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\StockService;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Vérification des expirations chaque matin à 7h (heure Brazzaville)
        $schedule->call(function () {
            app(StockService::class)->verifierExpirations();
        })->dailyAt('07:00')->timezone('Africa/Brazzaville');

        // Nettoyage des tokens expirés
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
