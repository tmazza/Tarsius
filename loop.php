<?php
$db = new PDO('sqlite:tarsius.db');
$setProcFinalizado = $db->prepare('UPDATE processo SET status = 2 WHERE trabalho_id = :trabId AND pid = :pid');
$getProcStatus = $db->prepare('SELECT status FROM PROCESSO WHERE trabalho_id = :trabId AND pid = :pid');

$trabId = $argv[1];
$pid = getmypid();

$count = 0;
$h = fopen("aaa.txt","a+");
while(true){
  fwrite($h,$trab . ' | ' . $count . ' | ' . date('H:i:s') . "\n");
  $count++;

  if($count % 5 == 0){ // A cada 5seg verifica se deve continuar processando
    $getProcStatus->execute([
      ':trabId'=>$trabId,
      ':pid'=>$pid,
    ]);
    $data = $getProcStatus->fetch();

    fwrite($h,json_encode($data));

    if($data['status'] != 1){
      // TODO: executar função de fechamento
      break;
    }

  }

  sleep(1);


  if($count > 100){
    break;
  }

}
fclose($h);


$setProcFinalizado->execute([
  ':trabId'=>$trabId,
  ':pid'=>$pid,
]);
