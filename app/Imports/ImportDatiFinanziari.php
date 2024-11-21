<?php

namespace App\Imports;

use App\Models\FinancialData;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Log;

class ImportDatiFinanziari implements ToModel, WithHeadingRow, WithValidation
{
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $errors = [];

        // Validazione dei campi after rules
        $giorno = $this->checkFormattedDateIta($row['giorno'], $errors);
        $bilancio = $this->validateBilancio($row['bilancio_giornaliero'], $errors);

        // Log degli errori
        $this->logErrors($errors);

        if (!empty($errors)) {
            return null; // Salta la riga
        } 

        return new FinancialData([
            'branch_id' => $row['id_filiale'],
            'date' => Carbon::createFromFormat('d/m/Y', $giorno)->format('Y-m-d'),
            'bilance' => $bilancio,
        ]);
    }
    
    /**
     * rules
     * 
     * Regole campi file importato
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id_filiale' => 'required|string|regex:/^[A-Za-z]{3}\d{4}$/',
            'giorno' => 'required',
            'bilancio_giornaliero' => 'required',
        ];
    }
    
    /**
     * customValidationMessages
     * 
     * Messaggi custom per eventuali errori
     * (se non viene definito riporterà i messaggi in lingua inglese)
     *
     * @return void
     */
    public function customValidationMessages()
    {
        return [
            'id_filiale.required' => 'ID filiale è una colonna obbligatoria.',
            'id_filiale.string' => 'ID filiale deve essere una stringa.',
            'id_filiale.regex' => 'ID filiale deve essere composto da 3 lettere e 4 numeri.',
            'giorno.required' => 'Il giorno è una colonna obbligatoria.',
            'bilancio_giornaliero.required' => 'Il bilancio giornaliero è una colonna obbligatoria.',
        ];
    }

    /**
     * Valida il giorno e lo converte in formato Sql
     */
    protected function checkFormattedDateIta($giorno, &$errors)
    {
        if (is_numeric($giorno)) {
            $giorno = Utility::convertOleToDateIta($giorno);
        }

        //Se entra in condizione la data non è in formato ita
        if(!Utility::validationDateIta($giorno)){
            $errors[] = "Data non formattata correttamente dd/mm/yyyy: {$giorno}";
            return null;
        }
        
        return $giorno;
    }

    /**
     * Valida Bilancio
     */
    protected function validateBilancio($bilancio, &$errors)
    {
        $bilancio = Utility::normalizeNumber($bilancio);
        if (!is_numeric($bilancio)) {
            $errors[] = "Bilancio non valido: {$bilancio}";
            return null;
        }
        return $bilancio;
    }

    /**
     * Log degli errori
     */
    protected function logErrors(array $errors)
    {
        foreach ($errors as $error) {
            if (str_contains($error, 'ID Filiale')) {
                Log::error($error); // laravel.log
            } else {
                $this->logger->error($error); // Monolog dinamico
            }
        }
    }

    public function heading(): array
    {
        return [
            'id_filiale',
            'giorno',
            'bilancio_giornaliero',
        ];
    }
}

