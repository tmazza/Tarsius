<?php
/**
 * Parar processos
 * Excluir distribuidos
 * Excluir processos
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if(isset($_GET['id'])){
	$trabId = $_GET['id'];
	$db = new PDO('sqlite:tarsius.db');


	$getProcessos = $db->prepare('SELECT * FROM processo WHERE trabalho_id = :trabId');
	$setStatusProc = $db->prepare("UPDATE processo SET status=:status WHERE id = :id AND status=1");
	$qtdProcAtivos = $db->prepare("SELECT count(*) as qtd FROM processo WHERE trabalho_id=:trabId and status!=:status");
	$deleteProc = $db->prepare("DELETE FROM processo 	WHERE trabalho_id=:trabId");
	$deleteDist = $db->prepare("DELETE FROM distribuido WHERE trabalho_id=:trabId");


	$getProcessos->execute([':trabId'=>$trabId]);
	$processos = $getProcessos->fetchAll(PDO::FETCH_ASSOC);
	$getProcessos->closeCursor();


	foreach ($processos as $p){
		$setStatusProc->execute([
			':id'=>$p['id'],
			':status'=>3,
		]);
		$setStatusProc->fetchAll(PDO::FETCH_ASSOC);
		$setStatusProc->closeCursor();
	}
	do { // Aguarda até que todos os arquivos seja movidos para de volta para sourceDir
		$qtdProcAtivos->execute([
			':trabId'=>$trabId,
			':status'=>2,
		]);
		$data = $qtdProcAtivos->fetch(PDO::FETCH_ASSOC);
		$qtdProcAtivos->closeCursor();
		$qtd = $data['qtd'];
		if($qtd > 0){
			echo "Aguardando " . $qtd . " processo(s) devolverem as imagens para diretório de origem.<br>";
			sleep(1);
		}
	} while($qtd > 0);

	$deleteProc->execute([':trabId'=>$trabId]);
	$deleteProc->closeCursor();
	$deleteDist->execute([':trabId'=>$trabId]);
	$deleteDist->closeCursor();
	header("Location: index.php");

} else {
  echo 'Qual o trabalho?';
}


