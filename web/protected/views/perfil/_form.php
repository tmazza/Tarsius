
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'trabalho-perfil-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class' => 'uk-form uk-form-horizontal']
)); ?>

	<?= $form->errorSummary($model); ?>


	<fieldset>
		<legend>Básico</legend>
		<div class="uk-form-row">
			<?= $form->labelEx($model,'descricao',['class' => 'uk-form-label']); ?>
			<?= $form->textField($model,'descricao',array('rows'=>6, 'cols'=>50)); ?>
			<?= $form->error($model,'descricao'); ?>
			<br>
			<small>
				Nome para identificar a configuração. Use somente letras, números e espaços.
			</small>
		</div>
	</fieldset>
	<br><br>

	<fieldset>
		<legend>Geral</legend>

		<div class="uk-form-row">
			<?= $form->labelEx($model,'enableDebug',['class' => 'uk-form-label']); ?>
			<?= $form->textField($model,'enableDebug'); ?>
			<?= $form->error($model,'enableDebug'); ?>
			<br>
			<small>
				Se deve gerar dados intermediários para visualização
	            e análise dos resultados parciais obtidos durante o processamento.
	            Irá gera uma imagem para cada etapa do processo. NÃO deve ser usado
	            em produção, somente para testes e validação do que está sendo processado.
			</small>
		</div>

		<div class="uk-form-row">
			<?= $form->labelEx($model,'threshold',['class' => 'uk-form-label']); ?>
			<?= $form->textField($model,'threshold'); ?>
			<?= $form->error($model,'threshold'); ?>
			<br>
			<small>
			Corte entre pixel pretos e brancos. Pontos com ton de cinza abaixo do valor definido serão consiredos preto.
			</small>
		</div>
	</fieldset>

	<br><br>

	<fieldset>
		<legend>Busca das âncoras</legend>
	
		<div class="uk-alert">A localização das âncoras é o primeiro passo para interpretar as regiões da imagem. A busca das âncoras é feita através da comparação de suas imagens com os objetos contidos na imagem. Os parâmetros abaixo configuram como a busca deve ser feita e as características do objeto procurado.</div>

		<hr>
		<div class="uk-margin-large-left">
			<h3>Configuração busca</h3>

			<div class="uk-form-row">
				<?= $form->labelEx($model,'searchArea',['class' => 'uk-form-label']); ?>
				<?= $form->textField($model,'searchArea'); ?>
				<?= $form->error($model,'searchArea'); ?>
				<br>
				<small>
				Quantidade de 'escalas' para a definir o primeiro tamanho da 
		        área de busca. Por exemplo, com uma resolução de 300dpi são aproximadamente
		        11.81 pixel por milímetro, com $searchArea igual 10 a primeira área de busca
		        seria um quadrado de 10*11.81 pixel de lado.
				</small>
			</div>

			<div class="uk-form-row">
				<?= $form->labelEx($model,'maxExpansions',['class' => 'uk-form-label']); ?>
				<?= $form->textField($model,'maxExpansions'); ?>
				<?= $form->error($model,'maxExpansions'); ?>
				<br>
				<small>
				Quantidade máxima de expansões na busca de um objeto. Região
		        de busca é expandida enquanto nenhum objeto com $minMatchsObject mínimo seja 
		        encontrado ou o limite $maxExpasions seja atingido.
				</small>
			</div>

			<div class="uk-form-row">
				<?= $form->labelEx($model,'expasionRate',['class' => 'uk-form-label']); ?>
				<?= $form->textField($model,'expasionRate'); ?>
				<?= $form->error($model,'expasionRate'); ?>
				<br>
				<small>
				O quanto a região deve aumentar a cada expansão ($maxExpansions).
		        Por exemplo, tendo uma área inicial de busca igual a 100 pixel e a taxa de expansão
		        igual a 0.5, após a primeira iteração a área de busca será expandida para um quadrado
		        de lado 150, após 225, etc. Aumentando a busca em 50% a cada iteração.
				</small>
			</div>
		</div>
		<hr>
		<div class="uk-margin-large-left">

			<h3>Características da âncora</h3>

			<div class="uk-form-row">
				<div class="uk-form-label">Área entre</div>
				<?= $form->textField($model,'minArea'); ?> e
				<?= $form->textField($model,'maxArea'); ?> pixel
				<br>
				<small>
				Área mínima e máxima para considerar o objeto durante carregamento e busca
		        das âncoras. 
		        <br>Após encontrar a primeira âncora esse valores serão alterados, será usado
		        como referência a área do objeto encontrado e a tolerância definida em $areaTolerance
				</small>
				<?= $form->error($model,'minArea'); ?>
				<?= $form->error($model,'maxArea'); ?>
			</div>

			<div class="uk-form-row">
				<?= $form->labelEx($model,'areaTolerance',['class' => 'uk-form-label']); ?>
				<?= $form->textField($model,'areaTolerance'); ?>
				<?= $form->error($model,'areaTolerance'); ?>
				<br>
				<small>
				Tolerância na busca das âncoras, usado após a primeira âncora ser encontrada.
		        Por exemplo, caso a área da âncora encontrada seja de 1000 pixel e $areaTolerance
		        seja 0.4 o valor de $minArea e $maxArea serão, respectivamente, 600 e 1400.
				</small>
			</div>

			<div class="uk-form-row">
				<?= $form->labelEx($model,'minMatchObject',['class' => 'uk-form-label']); ?>
				<?= $form->textField($model,'minMatchObject'); ?>
				<?= $form->error($model,'minMatchObject'); ?>
				<br>
				<small>
				Porcentagem mínima na comparação de dois objetos para 
		     	considerá-los iguais.
				</small>
			</div>
		</div>
	</fieldset>

	<br><br>

	<fieldset>
		<legend>Interpretação das regiões do template</legend>
		<div class="uk-form-row">
			<?= $form->labelEx($model,'minMatchEllipse',['class' => 'uk-form-label']); ?>
			<?= $form->textField($model,'minMatchEllipse'); ?>
			<?= $form->error($model,'minMatchEllipse'); ?>
			<br>
			<small>
			Valor mínimo para considerar uma elipse preenchida.
			</small>
		</div>

		<div class="uk-form-row">
			<?= $form->labelEx($model,'templateValidationTolerance',['class' => 'uk-form-label']); ?>
			<?= $form->textField($model,'templateValidationTolerance'); ?>
			<?= $form->error($model,'templateValidationTolerance'); ?>
			<br>
			<small>
			Quantidade de diferenças aceitas na comparação da região usada para validação template.
			Usado somente se o campo validaReconhecimento foi definido no template.
			</small>
		</div>

		<div class="uk-form-row">
			<?= $form->labelEx($model,'dynamicPointReference',['class' => 'uk-form-label']); ?>
			<?= $form->textField($model,'dynamicPointReference'); ?>
			<?= $form->error($model,'dynamicPointReference'); ?>
			<br>
			<small>
			Para definir um ponto, por exemplo o centro de um elipse, as
		    quatro âncoras são utilizadas como referência. Caso $dynamicPointReference não seja aplicado
		    todos os pontos terão o mesmo 'peso' na hora definir o ponto real (único). Com o uso de $dynamicPointReference
		    quanto mais próximo da âncora de referência o ponto estiver maior será o seu 'peso' ao definir
		    o ponto real.
			</small>
		</div>
	</fieldset>
	<br><br>
	<?= CHtml::submitButton($model->isNewRecord ? 'Criar' : 'Atualizar',[
		'class' => 'uk-button uk-button-primary',
	]); ?>

<?php $this->endWidget(); ?>

</div>