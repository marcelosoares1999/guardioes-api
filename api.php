<?php
define('PRECO_PLANO', 99.99);
define('TURNO_PADRAO', '21');
try {
 $pdo = new PDO("sqlite:".__DIR__."/guardioes24h.db");
 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 $pdo->exec("CREATE TABLE IF NOT EXISTS clientes (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT, dvr_host TEXT, dvr_porta TEXT, whatsapp TEXT, turno TEXT DEFAULT '21', status TEXT DEFAULT 'ativo', adimplente INTEGER DEFAULT 1, plano_valor REAL DEFAULT 99.99)");
 $pdo->exec("CREATE TABLE IF NOT EXISTS logs (id INTEGER PRIMARY KEY AUTOINCREMENT, cliente_id INTEGER, status_detectado TEXT, data_hora DATETIME DEFAULT CURRENT_TIMESTAMP)");
} catch(Exception $e){ die(json_encode(["erro"=>$e->getMessage()])); }

$action = $_GET['action'] ?? '';
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

if($action=='fila_disparo'){
 $turno = $_GET['turno'] ?? TURNO_PADRAO;
 $s=$pdo->prepare("SELECT id,nome,dvr_host,dvr_porta,whatsapp FROM clientes WHERE turno=:t AND status='ativo' AND adimplente=1");
 $s->execute([':t'=>$turno]);
 echo json_encode(["sucesso"=>true,"turno"=>$turno."h00","clientes"=>$s->fetchAll(PDO::FETCH_ASSOC)]);
}
if($action=='salvar_log'){
 $in=json_decode(file_get_contents("php://input"),true);
 if(($in['status_dvr']??'')==='online'){ echo json_encode(["sucesso"=>true,"msg"=>"ignorado"]); exit; }
 $s=$pdo->prepare("INSERT INTO logs (cliente_id,status_detectado) VALUES (:c,:s)");
 $s->execute([':c'=>$in['cliente_id'],':s'=>$in['status_dvr']]);
 echo json_encode(["sucesso"=>true,"msg"=>"falha registrada"]);
}
if($action=='adicionar_cliente'){
 $s=$pdo->prepare("INSERT INTO clientes (nome,dvr_host,dvr_porta,whatsapp,turno,plano_valor) VALUES (?,?,?,?,?,?)");
 $s->execute([$_GET['nome'],$_GET['dvr_host'],$_GET['dvr_porta'],$_GET['whatsapp'],TURNO_PADRAO,PRECO_PLANO]);
 echo json_encode(["sucesso"=>true]);
}
