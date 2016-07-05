<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$db = new PDO('sqlite:tarsius.db');
$setStatusTrabalho = $db->prepare('UPDATE trabalho SET status = :status,pid=:pid WHERE id = :trabId');
if(isset($_GET['id'])){
  $cmd = 'php distribui.php ' . $_GET['id'];
  $pid = exec($cmd . ' > /dev/null 2>&1 & echo $!; ');

  $a = $setStatusTrabalho->execute([
     ':trabId'=>$_GET['id'],
     ':status'=>1,
     ':pid'=>$pid,
   ]);
 $setStatusTrabalho->closeCursor();
 header("Location: index.php");

} else {
  echo 'Qual o trabalho?';
}
