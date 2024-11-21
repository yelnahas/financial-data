<?php

namespace App\Jobs;

use App\Imports\ImportDatiFinanziari;
use App\Interfaces\LoggerInterface;
use App\Services\FileLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class JobDatiFinanziari implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue;

    protected $file;
    protected $logger;

    /**
     * Create a new job instance.
     */
    public function __construct($file, LoggerInterface $logger = null)
    {
        $this->file = $file;
        $this->logger = $logger;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $processedPath = 'processed-files';
        $idFiliale = $this->estraiIdFiliale();

        // Inizializza il logger se non è stato passato nel costruttore
        if (!$this->logger) {
            $this->logger = new FileLogger($idFiliale);
        }

        try {
            Excel::import(new ImportDatiFinanziari($this->logger), $this->file/* , null, \Maatwebsite\Excel\Excel::CSV */);
            $this->logger->info("Importazione completata con successo per il file {$this->file}.");
            /* 
            Sposta il file processato nell'apposita cartella dopo una corretta importazione
            PS: E' stata aggiunta la data e ora qualora si reputasse necessario distinguire i file con lo stesso nome importati in momenti differenti - prevenendo così la sovrascrittura
            */
            Storage::move($this->file, "{$processedPath}/" . date('YmdHi') . '_' . basename($this->file));
        } catch (\Exception $e) {
            $this->logger->error("Errore durante l'importazione del file {$this->file}: {$e->getMessage()}");
        }
    }


    protected function estraiIdFiliale()
    {
        $data = Excel::toArray(new ImportDatiFinanziari($this->logger), $this->file);

        // Controlla se il file contiene almeno una riga
        if (!empty($data[0])){
            // Estrai la prima colonna della seconda riga
            $idFiliale = $data[0][0]['id_filiale']; // $data[0] è il primo foglio, [0] è la prima riga, [id_filiale] è la prima colonna

            // Controllo con regex
            if (!preg_match('/^[A-Z]{3}[0-9]{4}$/', $idFiliale)) {
                // Se l'id_filiale non è valido, puoi loggare o gestire l'errore
                throw new \Exception("ID Filiale non valido: $idFiliale per il file {$this->file}.");
                return null;
            }

            return $idFiliale;
        }
    }
}
