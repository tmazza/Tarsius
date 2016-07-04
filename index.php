<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$db = new PDO('sqlite:tarsius.db');
$getTrabalho = $db->prepare('SELECT * FROM trabalho WHERE id = :trabId');
$getProcessos = $db->prepare('SELECT * FROM processo WHERE trabalho_id = :trabId');

$trabId = 29;
$getTrabalho->execute([':trabId'=>$trabId]);
$data = $getTrabalho->fetch(PDO::FETCH_ASSOC);
$getTrabalho->closeCursor();

$getProcessos->execute([':trabId'=>$trabId]);
$processos = $getProcessos->fetchAll(PDO::FETCH_ASSOC);
$getProcessos->closeCursor();

echo $data['id'] . ' | ';
echo $data['nome'] . ' | ';
echo '<code>' . $data['sourceDir'] . '</code>';

if($data['status'] == 0){
  echo '<a href="iniciarTrabalho.php?id='.$trabId.'">Iniciar</a>';
} else if($data['status'] == 1) {
  echo '<a href="pararTrabalho.php?id='.$trabId.'">Parar</a>';
} else if($data['status'] == 2) {
  echo "Parando...";
} else {
  echo '???';
}

echo '<hr>';

foreach ($processos as $p) {
  echo '<div' . ( $p['status'] == 1 ? ' style="background:#7a7" ' : '' ). '>';
  echo $p['id'] . ' - ' . $p['pid'] . ' - ' . $p['trabalho_id'] . ' - ' . $p['workDir'] . '| ' . $p['status'] .  '<br>';
  echo '</div>';
}
