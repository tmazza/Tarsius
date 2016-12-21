<?php
$this->menu = [];

if($trabalho->status == 0){
	$this->menu[] = ['<i class="uk-icon uk-icon-play"></i> Distribuir',$this->createUrl('/trabalho/iniciar',[
		'id'=>$trabalho->id,])];
	$this->menu[] = ['<i class="uk-icon uk-icon-cog"></i> Configurar',$this->createUrl('/trabalho/editar',[
		'id'=>$trabalho->id,])];
	$this->menu[] = ['Apagar/reset',$this->createUrl('/trabalho/excluir',[
		'id'=>$trabalho->id,]),[
		'confirm'=>'Certeza?',]];
}
if($trabalho->status == 1)
	$this->menu[] = ['<i class="uk-icon uk-icon-pause"></i> Pausar trabalho',$this->createUrl('/trabalho/pausar',[
		'id'=>$trabalho->id,])];

$this->menu[] = ['Ver processadas',$this->createUrl('/trabalho/finalizadas',[
	'id'=>$trabalho->id,])];
$this->menu[] = ['Não exportadas',$this->createUrl('/trabalho/naoDistribuidas',[
	'id'=>$trabalho->id,])];

if($trabalho->status == 2){

	$this->menu[] = ['Forçar parada',$this->createUrl('/trabalho/forcaParada',[
		'id'=>$trabalho->id,
	]),[
		'confirm' => "Forçando a parada o processo será marcado como finalizado, " 
					. "mesmo sem haver confirmação de seu estado atual.\n\n" 
					. "Caso o processo não tenha encerrado e uma nova distribição " 
					. "seja iniciada o novo processo e o não encerrado podem ficar "
					. "concorrendo pelas imagens.\n\n"
					. "O código do processo atual é '{$trabalho->pid}'.\n\n"
					. "Você tem certeza que este processo já foi encerrado?",
	]];

}

?>
<h3>
	<?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?> 
	&raquo; <?=$trabalho->nome?>
</h3>
<hr>
<div id='status'>
	<?php $this->renderPartial('_ver',[
		'trabalho'=>$trabalho,
	 	'distribuido'=>$distribuido,
	 	'processado'=>$processado,
	 	'processosAtivos'=>$processosAtivos,
	 	'naoExportadas'=>$naoExportadas,
	 	'erros'=>$erros,
	]); ?>
</div>

<?php if($trabalho->status != 0): ?>
	<script>
	setInterval(function(){
		$.ajax({
			url: '<?=$this->createUrl('/trabalho/updateVer',['id'=>$trabalho->id]);?>',
		}).done(function(html) {
		    $('#status').html(html);
			if(count > 60) { notifyMe(); count = 0; }
			else count++;
		});
	}, 1000);
	</script>
<?php endif; ?>