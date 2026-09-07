<?php
header('Content-Type: application/json');
$SHEET_ID = "1EQS1zekqt0Ecq0JxOAfD6vjMrHtZqjCkGrpjatVzRnU";
$action = $_GET['action']?? '';
$turno = $_GET['turno']?? date('H');
if ($action === 'fila_disparo') {
    $csv = @file_get_contents("https://docs.google.com/spreadsheets/d/$SHEET_ID/export?format=csv");
    $linhas = array_map('str_getcsv', explode("\n", $csv));
    $clientes = [];
    for ($i=1; $i<count($linhas); $i++) {
        if (count($linhas[$i]) < 3) continue;
        $nome = trim($linhas[$i][0]); $tel = trim($linhas[$i][1]); $t = trim($linhas[$i][2]);
        if ($t == $turno && $nome && $tel) $clientes[] = ["nome"=>$nome, "telefone"=>$tel, "turno"=>$t];
    }
    echo json_encode(["sucesso"=>true, "turno"=>$turno."h00", "clientes"=>$clientes]);
    exit;
}
echo json_encode(["sucesso"=>false]);
