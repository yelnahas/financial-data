<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Jobs\JobDatiFinanziari;

class ImportDatiFinanziari extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando per l\'importazione di dati finanziari tramite jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $importPath = 'import-files';
        
        // Recupera i file csv/excel da processare
        $files = Storage::files($importPath);

        foreach ($files as $file) {
            // Dispatcha un job per ogni file
            JobDatiFinanziari::dispatch($file);
            $this->info("Job creato per il file: {$file}");
        }

        $this->info("_________________________________________\n");
        $this->info("Tutti i file sono stati messi in coda per l'elaborazione.");
    }
}
