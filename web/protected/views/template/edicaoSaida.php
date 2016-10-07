<?php
$this->menu = [
	['Cancelar',$this->createUrl('/template/index')],
];
?>
<h2><?=$template?> &raquo; Edição de saída</h2>
<hr>
<pre>
<!-- <div contenteditable=true id=asd><?=CHtml::encode($content);?></div> -->
<textarea id=asd style="width:100%;height:640px;"><?=CHtml::encode($content);?></textarea>
</pre>
<button onclick="enviar()">Atualizar</button>
<hr>
Highlight <small>Salve para atualizar</small><br>
<?php highlight_string($content);?>

<?=CHtml::beginForm('','POST',[
	'id' => 'form',
]);?>
<input type="hidden" name="config" id='config' />
<?=CHtml::endForm();?>
<script>
	function enviar(){
		// $('#config').val($('#asd').text());
		$('#config').val($('#asd').val());
		$('#form').submit();
	}	
</script>