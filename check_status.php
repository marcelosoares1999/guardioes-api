<?php
// Guardiões 24h - Check a cada 5 min
header('Content-Type: text/plain');

$apiUrl = getenv('EVOLUTION_API_URL'); // ex: https://sua-api.onrender.com
$apiKey = getenv('EVOLUTION_API_KEY');
$instance = getenv('EVOLUTION_INSTANCE'); // ex: guardioes

// 1. Lê planilha Google (CSV público)
$sheetCsv = getenv('SHEET_CSV_URL');
$clientes = [];
if ($sheetCsv) {
    $csv = @file_get_contents($sheetCsv);
    if ($csv) {
        $linhas = explode("\n", $csv);
        foreach ($linhas as $i => $l) {
            if ($i==0 || trim($l)=="") continue;
            $c = str_getcsv($l);
            // Esperado: nome,whatsapp,dvr_id,status
            $clientes[] = ['nome'=>@$c[0], 'whatsapp'=>@$c[1], 'dvr_id'=>@$c[2]];
        }
    }
}

// 2. Simula check P2P (aqui tu vai ligar o real depois)
foreach ($clientes as $cli) {
    $online = true; // TODO: trocar pela checagem P2P real
    if (!$online && $apiUrl) {
        $fone = preg_replace('/\D/','', $cli['whatsapp']);
        $msg = "🚨 Guardiões 24h: {$cli['nome']}, seu DVR {$cli['dvr_id']} caiu às ".date('H:i').". Verifique a energia/internet.";
        @file_get_contents($apiUrl."/message/sendText/".$instance, false, stream_context_create([
            'http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\napikey: $apiKey\r\n",'content'=>json_encode(['number'=>$fone,'text'=>$msg])]
        ]));
    }
}

echo "check ok - ".date('d/m H:i')." - ".count($clientes)." clientes";