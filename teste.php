<?php
$db = new PDO('sqlite:tarsius.db');

$criarTrabalho = $db->prepare('INSERT INTO trabalho (nome) VALUES (:nome)');
$criarTrabalho->execute(array(':nome'=>'Trabalho ' . time()));

$buscaUltimoTrabalhoCriado = $db->prepare('SELECT max(id) as maior FROM trabalho');
$buscaUltimoTrabalhoCriado->execute();
$data = $buscaUltimoTrabalhoCriado->fetch();
$trabId = $data['maior'];

$criaProcesso = $db->prepare('INSERT INTO processo (pid,status,trabalho_id) VALUES (:pid,:status,:trabId)');


$buscaUltimoProcessoCriado = $db->prepare('SELECT max(id) as maior FROM processo WHERE trabalho_id = :id');

echo 'Trabalho: ' . $trabId . "\n";

for($i=0;$i<3;$i++){
  $criaProcesso->execute([
    ':pid'=>exec('php loop.php ' . $trabId . ' > /dev/null 2>&1 & echo $!; '),
    ':status'=>1,
    ':trabId'=>$trabId,
  ]);
}
