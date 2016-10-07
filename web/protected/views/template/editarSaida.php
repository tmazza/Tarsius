<?php
$this->menu = [
	['Cancelar',$this->createUrl('/template/index')],
];
?>
<h2><?=$template?></h2>
<hr>

<div class="uk-grid">
	<div class="uk-width-3-5">
		<div class="uk-panel uk-panel-box uk-panel-box-secondary">
			Incluir novo registro de saída <br><br>
			ID <input id='add-one-id' />
			&nbsp;&nbsp;
			<input type="checkbox" id='add-one-single' /> Várias regiões
			&nbsp;&nbsp;
			<button id='add-one'> + </button>	
		</div>
		<br>
		<?=CHtml::beginForm();?>
			<table class='uk-table uk-table-striped'>
				<tr><th>ID de saída</th><th>Região(ões)</th><th></th></tr>
				<?php foreach ($formatoSaida as $id => $v): ?>
					<tr>
						<td>
							<?= $id; ?>
						</td>
						<td>
							<?php if(is_string($v)): ?>
								<input name="<?=$id?>" value='<?=$v?>' placeholder='match'/><br>
							<?php elseif(is_bool($v)): ?>
								<?php $value = $v ? 'SIM' : 'NAO'?>
								<input name="<?=$id?>" value='<?=$value?>' placeholder='match'/><br>
							<?php else: ?>
								<?php
								$match = isset($v['match']) ? $v['match'] : '';
								$order = isset($v['order']) ? $v['order'] : '';
								$order = is_bool($order) ? ($order ? 'SIM' : 'NAO') : $order;
								?>
								<input name="<?=$id?>[match]" value='<?=$match?>' placeholder='match'/><br>
								<input name="<?=$id?>[order]" value='<?=$order?>' placeholder='order'/>
							<?php endif; ?>
						</td>
						<td class="uk-text-right">
							<?=CHtml::link('<i class="uk-icon uk-icon-times"></i> remover','#!',[
								'onclick' => '$(this).parent().parent().remove()',
							]);?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?=CHtml::submitButton('Salvar',[
				'class' => 'uk-button uk-button-primary'
			]);?>
		<?=CHtml::endForm();?>

	</div>
	<div class="uk-width-2-5">
		<h4 class="uk-text-center">Regiões do template</h4>
		<hr>
		asd	
	</div>
</div>
<script type="">
	$(document).ready(function(){
		$('#add-one').click(function(){
			id = $('#add-one-id').val(); $('#add-one-id').val('');
			single = $('#add-one-single:checkbox:checked').length <= 0;
			var newLine = '<tr><td>' + id + '</td><td>';
				if(single){
					newLine += '<input name="'+id+'" value="" placeholder="ID de 1 região">';
				} else {
				newLine += '<input name="'+id+'[match]" value="" placeholder="match"><br><input name="'+id+'[order]" value="" placeholder="order">';
				}
				newLine += '</td><td class="uk-text-right"><a onclick="$(this).parent().parent().remove()" href="#!"><i class="uk-icon uk-icon-times"></i> remover</a></td></tr>';			
			$('table').append(newLine);
		});
	});
</script>



