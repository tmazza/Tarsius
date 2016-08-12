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

?>
<h3>
	<?=CHtml::link('Trabalhos',$this->createUrl('/trabalho/index'));?> 
	&raquo; <?=$trabalho->nome?>
</h3>
<hr>
<div id='status'>
	<?php $this->renderPartial('_ver',[
		'trabalho'=>$trabalho,
		'faltaProcessar'=>$faltaProcessar,
	]); ?>
</div>

<?php if($trabalho->status != 0): ?>
	<script>
	if(typeof(EventSource) !== "undefined") {
		var source = new EventSource('<?=$this->createUrl('/trabalho/updateVer',['id'=>$trabalho->id]);?>');
		source.onmessage = function(event) {
			data = JSON.parse(event.data);
			$('#status').html(data['html']);
		};
	} else {
		alert('Este navegador parece um pouco antigo e não suporta todos os recursos desta página. A página não será atualziada automaticamente quando houver alterações nas imagens sendo processadas');
	} 	
	</script>
<?php endif; ?>