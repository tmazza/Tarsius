<!DOCTYPE html>
<html>
	<head>
		<title>Trabalho <?=$trabalho?> Concurso <?=$concurso?> | Comparação de resultados</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.27.1/css/uikit.min.css" />
		<meta charset="UTF-8"/>
	</head>
	<body>
		<div class="uk-container uk-container-center">
			<br>
			<h2>Comparação de resultados do trabalho <?=$trabalho?> com o concurso <?=$concurso?></h2>
			<!-- resumo -->
			<div class="uk-grid">
				<div class="uk-grid-1-3">
					<table class="uk-table">
						<tr>
							<th colspan="2">Resumo</th>							
						</tr>
						<tr>
							<td>Iguais</td>			
							<td style='text-align:right;'><?=count($iguais);?></td>			
						</tr>			
						<tr>
							<td>Diferentes</td>			
							<td style='text-align:right;'><?=count($diferencas);?></td>			
						</tr>			
						<tr>
							<td>Não encontradas no trabalho</td>			
							<td style='text-align:right;'><?=count($naoEncontradas);?></td>			
						</tr>			
						<tr>
							<td>Outros erros <small>(ancoras, imagem incorreta, ...)</small></td>			
							<td style='text-align:right;'><?=count($respNaoDefinida);?></td>			
						</tr>			
					</table>
				</div>
			</div>


			<!-- detalhe das diferenças -->
			<h3>Diferentes</h3>
			<table class="uk-table uk-table-condensed uk-table-hover">
				<tr>
					<th style="text-align:left;">Resultado do processamento</th>
					<th>
						<table>
							<tr>
								<td style='width:80px;text-align:;'>Questão</td>
								<td style='width:80px;text-align:;'>Alternativa</td>
								<td style='width:80px;text-align:center;'>Resultado Trabalho</td>
								<td style='width:80px;text-align:center;'>Resultado Concurso</td>
							</tr>
						</table>
					</th>
				</tr>
				<?php foreach ($diferencas as $f => $d): ?>
					<tr>
						<td>
							<?php
							$model = $d['model'];	
							echo CHtml::link($f,
								Yii::app()->params['urlBase']
								. '/concurso/tarsius2/web/index.php/distribuido/ver/id/'
								. $model->id,[
								'target'=>'_blank',
							]);
							?>
						</td>
						<td>
							<table>
									<?php
									foreach ($d['diferencas'] as $diff) {
										$pos = $diff['posicao'];
										$trab = $diff['local'];
										$conc = $diff['export'];
										$alt = $pos % 5;
										$qst = ($pos - $alt) / 5 + 1;
										echo '<tr><td style="width:80px;text-align:right">';
										echo $qst;
										echo '</td><td style="width:80px;text-align:right">';
										switch ($alt) {
											case 0: echo 'A'; break;
											case 1: echo 'B'; break;
											case 2: echo 'C'; break;
											case 3: echo 'D'; break;
											case 4: echo 'E'; break;
										}
										echo '</td><td style="width:80px;text-align:center">';
										echo $trab;
										echo '</td><td style="width:80px;text-align:center">';
										echo $conc;
										echo '</td></tr>';
									}
									?>

							</table>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<br>
			<!-- detalhe das naoEncontradas -->
			<?php if(count($naoEncontradas) > 0): ?>
				<h3>Não encontradas no trabalho</h3>
				<pre><?=implode(' ', array_map(function($f) use($url) {
					return CHtml::link($f,$url.$f.'.jpg') . '|';
				},$naoEncontradas));?></pre>
				<hr>
			<?php endif; ?>
			<!-- detalhe das respNaoDefinida -->
			<?php if(count($respNaoDefinida) > 0): ?>
				<h3>Outros erros <small>(ancoras, imagem incorreta, ...)</h3>
				<pre><?=implode(' ', array_map(function($f) use($url) {
					return CHtml::link($f,$url.$f.'.jpg') . '|';
				},$respNaoDefinida));?></pre>
				<hr>
			<?php endif; ?>
			<style>
				.uk-table td, .uk-table th {
					border-bottom: 1px solid #E5E5E5;
				}
			</style>
		</div>
	</body>
</html>