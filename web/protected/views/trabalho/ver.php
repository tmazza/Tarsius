<?php
$this->menu = [];

if($trabalho->status == 0){
	$this->menu[] = ['Iniciar distribuição',$this->createUrl('/trabalho/iniciar',['id'=>$trabalho->id,])];
	$this->menu[] = ['Excluir trabalho',$this->createUrl('/trabalho/excluir',['id'=>$trabalho->id,]),[
		'confirm'=>'Certeza?',]];
}
if($trabalho->status == 1)
	$this->menu[] = ['Pausar distribuição',$this->createUrl('/trabalho/pausar',['id'=>$trabalho->id,])];

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
		function notifyMe() {
		  if (("Notification" in window)) {
			  if (Notification.permission === "granted") {
			  	notify();
			  } else if (Notification.permission !== 'denied') {
			    Notification.requestPermission(function (permission) {
			      if (permission === "granted")	notify();
			    });
			  }
		  } 
		}
		function notify(){
			var notification = new Notification("Distribuindo...");
		}
		let count = 0;
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