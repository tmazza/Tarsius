<?php
$this->menu = [
	['Voltar',$this->createUrl('/trabalho/ver',[
		'id' => $id,
	])],
];
?>
<h2>Erros no trabalho</h2>
<hr><br>
<?php foreach ($erros as $e): ?>
    <div class="uk-text-right">
        <?=CHtml::link('Excluir <i class="uk-icon uk-icon-trash"></i>',$this->createUrl('/trabalho/DeleteErro',[
            'id' => $e->id,
        ]),[
            'onclick' => '$(this).text("Excluindo...");',
            'confirm' => 'Confirma exclusão?'
        ]);?>
         | 
        <?=CHtml::link('Excluir todos do mesmo tipo <i class="uk-icon uk-icon-trash"></i>',$this->createUrl('/trabalho/DeleteAllErro',[
            'id' => $e->id,
        ]),[
            'onclick' => '$(this).text("Excluindo...");',
            'confirm' => 'Confirma exclusão de todos do memsmo tipo?'
        ]);?>
    </div>
    <div class="uk-alert">
        <a href='#!' onclick="$(this).parent().next().slideToggle()" class="uk-float-right uk-button">+</a>
        <?=$e->texto;?>
    </div>
	<pre style="display:none;"><?=$e->trace;?></pre><hr><br>
<?php endforeach; ?>

