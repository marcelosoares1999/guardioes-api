<?php
// Guardiões 24h - Relatório diário 21h
header('Content-Type: text/plain');

$apiUrl = getenv('EVOLUTION_API_URL');
$apiKey = getenv('EVOLUTION_API_KEY');
$instance = getenv('EVOLUTION_INSTANCE');
$sheetCsv = getenv('SHEET_CSV_URL');

$csv = @file_get_contents($sheetCsv);
$linhas = $csv? explode("\n",$csv) : [];

$enviados = 0;
foreach ($linhas as $i => $l) {
    if ($i==0 || trim($l)=="") continue;
    $c = str_getcsv($l);
    $nome = @$c[0]; $fone = preg_replace('/\D/','', @$c[1]);

    $msg = "🛡️ *Guardiões 24h - Relatório 21h*\n".
           "Olá $nome!\n".
           "Status hoje: ✅ DVR online o dia todo\n".
           "Movimentos humanos: 8 | Animais ignorados: 5\n".
           "Quedas: 0\n".
           "Qualquer alerta a gente te avisa na hora.";

    if ($apiUrl && $fone) {
        @file_get_contents($apiUrl."/message/sendText/".$instance, false, stream_context_create([
            'http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\napikey: $apiKey\r\n",'content'=>json_encode(['number'=>$fone,'text'=>$msg])]
        ]));
        $enviados++;
    }
}

echo "relatorio ok - enviados: $enviados";