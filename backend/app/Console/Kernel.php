<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /** Define o horário das tarefas automáticas da aplicação. */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('expenses:dispatch-reminders')->everyMinute()->withoutOverlapping();
    }

    /** Carrega os comandos personalizados do sistema. */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
