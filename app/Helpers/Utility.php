<?php

namespace App\Helpers;

use Carbon\Carbon;

class Utility
{
    /**
     * Normalizza un numero da diversi formati al formato anglosassone (es. 1234.56)
     * Accetta input con separatori migliaia e decimali
     *
     * @param string|float $value Numero in formato stringa o float
     * @return sting|float Numero normalizzato come valore float o stringa se non normalizzato
     */
    public static function normalizeNumber($value)
    {

        // Rimuovi eventuali spazi
        $value = str_replace(' ', '', $value);

        // Controlla se contiene una virgola
        if (strpos($value, ',') !== false) {
            // Se contiene un punto e una virgola, trattalo come formato europeo
            if (strpos($value, '.') !== false) {
                $value = str_replace(['.', ','], ['', '.'], $value); // Rimuovi il punto e sostituisci la virgola con un punto
            } else {
                // Se contiene solo una virgola, sostituiscila con un punto
                $value = str_replace(',', '.', $value);
            }
        }

        // Verifica se il valore è numerico
        if (!is_numeric($value)) {
            return $value;
        }

        // Converte la stringa in float
        return floatval($value);
    }


    /**
     * Converte un timestamp Excel (OLE) in un oggetto DateTime
     *
     * @param int $timestampExcel timestamp Excel da convertire
     * @return \DateTime
     */
    public static function convertOleToDateIta($timestampExcel)
    {
        $objDateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($timestampExcel);
        return Carbon::instance($objDateTime)->format('d/m/Y');
    }

    
    /**
     * validationDateIta
     * 
     * verifica la formattazione della stringa (dd/mm/yyyy)
     *
     * @param  string $date
     * @return bool
     */
    public static function validationDateIta($date) : bool
    {
        $result = false;
        $pattern = '/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/(19|20)\d{2}$/';

        if(preg_match($pattern, $date)) {
            $result = true;
        }

        return $result;
    }
}
