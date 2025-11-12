<?php 




$barangayName = 'Unknown Barangay';
if ($lat && $lng) {
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lng&zoom=18&addressdetails=1";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "DisasterReady/1.0 (contact: jaredabrera@example.com)");
    $responseGeo = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode === 200 && $responseGeo) {
        $data = json_decode($responseGeo, true);
        $barangayName = $data['address']['quarter']
            ?? $data['address']['suburb']
            ?? $data['address']['village']
            ?? $data['address']['neighbourhood']
            ?? 'Unknown Barangay';

         $city = $data['address']['city']
            ?? $data['address']['town']
            ?? $data['address']['municipality']
            ?? $data['address']['county']
            ?? 'Unknown City';

    
    }
}
