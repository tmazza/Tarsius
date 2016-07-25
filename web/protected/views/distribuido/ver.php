<?php
$this->menu = [
	['Voltar',$this->createUrl('/trabalho/finalizadas',['id'=>$model->trabalho->id])],
];
?>
<h3><?=$model->nome?></h3>
<hr>
<img id='main' src="<?=$debugImage;?>" style="width:100%;" />