<?php

class detectTelco {

    // UPDATED 2025 prefix lists

    private $globe = [
        '817','905','906','915','916','917','925','926','927','935','936','937','945',
        '955','956','965','966','967','975','976','977','978','979','994','995','996','997'
    ];

    private $smart = [
        '907','908','909','910','912','918','919','920','921','928','929','930','938',
        '939','940','946','947','948','949','950','951','961','962','963','964','968',
        '969','970','981','989','999'
    ];

    private $dito = [
        '895','896','897','898','991','992','993','994'
    ];

    public function detect($number) {

        // Remove non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);

        if (strpos($number, '09') === 0) {
            $number = '63' . substr($number, 1);
        }

        // Validate PH format: must start with 63 + 10 digits
        if (strpos($number, '63') !== 0 || strlen($number) < 12) {
            return 'unknown';
        }

        
        $prefix3 = substr($number, 2, 3); 
        $prefix4 = substr($number, 2, 4); 

        // Check Globe/TM
        if (in_array($prefix3, $this->globe) || in_array($prefix4, $this->globe)) {
            return 'globe';
        }

        // Check Smart/TNT/Sun
        if (in_array($prefix3, $this->smart) || in_array($prefix4, $this->smart)) {
            return 'smart';
        }

        // Check DITO
        if (in_array($prefix3, $this->dito) || in_array($prefix4, $this->dito)) {
            return 'dito';
        }

        return 'unknown';
    }
}
