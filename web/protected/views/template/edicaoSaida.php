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
<button onclick="enviar()" class="uk-button uk-button-primary">Atualizar</button>
<br><br>
<div>
Definição de campo para validar template:
<pre>'validaReconhecimento' => ['ID-REGIAO','VALOR-ESPERADO'],</pre>
Definição de saída agrupando mais de uma região.
<pre>
'NOME-SAIDA' => [
  'match' => 'EXPRESSAO-REGULAR',
  'order' => CLOSURE-PHP,
 ],
 </pre>
 Onde EXPRESSAO-REGULAR, deve buscar pelo ID das regiões. Exemplo<br>
<pre>
/^e-.*$/
</pre>
para buscar todas as regiões que possuem o prefixo 'e-'<br>
CLOSURE-PHP recebe dois IDS da seleção para definir a ordem, exemplo<br>
 <pre>
funtion ($a,$b){
	return $a > $b; 	
}</pre>
</div>
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