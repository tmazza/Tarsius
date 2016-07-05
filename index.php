<meta http-equiv="refresh" content="2">
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$db = new PDO('sqlite:tarsius.db');
$getTrabalho = $db->prepare('SELECT * FROM trabalho WHERE id = :trabId');
$getProcessos = $db->prepare('SELECT * FROM processo WHERE trabalho_id = :trabId');
$getQtdProcessada = $db->prepare('SELECT p.id,d.status,count(*) as qtd FROM processo p JOIN distribuido d ON d.tempDir = workDir AND d.status != 2 WHERE p.trabalho_id = :trabId GROUP BY p.id');

$trabId = 29;
$getTrabalho->execute([':trabId'=>$trabId]);
$data = $getTrabalho->fetch(PDO::FETCH_ASSOC);
$getTrabalho->closeCursor();

$getProcessos->execute([':trabId'=>$trabId]);
$processos = $getProcessos->fetchAll(PDO::FETCH_ASSOC);
$getProcessos->closeCursor();

$getQtdProcessada->execute([':trabId'=>$trabId]);
$qtdProcessada = $getQtdProcessada->fetchAll(PDO::FETCH_ASSOC);
$getQtdProcessada->closeCursor();

$faltaProcessar = [];
foreach ($qtdProcessada as $q) {
  $faltaProcessar[$q['id']] = $q['qtd'];
}



echo $data['id'] . ' | ';
echo $data['nome'] . ' | ';
echo '<code>' . $data['sourceDir'] . '</code>';

if($data['status'] == 0){
  echo '<a href="iniciarTrabalho.php?id='.$trabId.'">Iniciar distribuição</a>';
  echo " | <a href='cancelarTrabalho.php?id=".$trabId."' onclick='return confirm(\'Certeza ?\')'>Cancelar trabalho</a>";
} else if($data['status'] == 1) {
  echo '<a href="pararTrabalho.php?id='.$trabId.'">Parar distribuição</a>';
} else if($data['status'] == 2) {
  echo "Parando...";
} else {
  echo '???';
}
?>
<hr>
<table style='width:100%;font-family:monospace;'>
  <?php foreach ($processos as $p): ?>
    <?php $cor = $p['status'] == 1 ? '#7a7' : 'transparent'; ?>
    <tr style="background:<?=$cor?>"><td><?=implode('</td><td>',$p)?></td>
      <?php if(isset($faltaProcessar[$p['id']])) echo '<td>' . $faltaProcessar[$p['id']] . '</td>'; ?>
      </tr>
  <?php endforeach; ?>
</table>
