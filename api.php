<?php
$SPREADSHEET_ID = '1EQS1zekqt0Ecq0JxOAfD6vjMrHtZqiCkGrpjatVzRnU';
$action = $_GET['action'] ?? '';

if ($action === 'fila_disparo') {
    $url = "https://docs.google.com/spreadsheets/d/$SPREADSHEET_ID/gviz/tq?tqx=out:csv&sheet=disparo";
    $csv = @file_get_contents($url);
    if (!$csv) { echo json_encode([]); exit; }
    $linhas = array_map('str_getcsv', explode("\n", trim($csv)));
    $header = array_shift($linhas);
    $out = [];
    foreach ($linhas as $l) {
        if (count($l) < count($header)) continue;
        $out[] = array_combine($header, $l);
    }
    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
}

if ($action === 'guardioes') {
    $url = "https://docs.google.com/spreadsheets/d/$SPREADSHEET_ID/gviz/tq?tqx=out:csv&sheet=guardioes";
    $csv = @file_get_contents($url);
    if (!$csv) { echo json_encode([]); exit; }
    $linhas = array_map('str_getcsv', explode("\n", trim($csv)));
    $header = array_shift($linhas);
    $out = [];
    foreach ($linhas as $l) {
        if (count($l) < count($header)) continue;
        $out[] = array_combine($header, $l);
    }
    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
}

if ($action === 'marcar_enviado') {
    echo json_encode(['ok' => true]);
    exit;
}

echo json_encode(['erro' => 'acao invalida']);
