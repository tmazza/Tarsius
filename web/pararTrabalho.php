<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$db = new PDO('sqlite:tarsius.db');
$setStatusTrabalho = $db->prepare('UPDATE trabalho SET status = :status WHERE id = :trabId');
if(isset($_GET['id'])){
  $a = $setStatusTrabalho->execute([
     ':trabId'=>$_GET['id'],
     ':status'=>2,
   ]);
 $setStatusTrabalho->closeCursor();
 header("Location: index.php");

} else {
  echo 'Qual o trabalho?';
}
