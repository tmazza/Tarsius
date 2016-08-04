<?php
include_once(Yii::getPathOfAlias('webroot') . '/../../src/GeraTemplate.php');

class TemplateCommand extends CConsoleCommand {

	public function actionIndex($nome=false){
		if($nome){
			$dir = Yii::getPathOfAlias('webroot') . '/../../data/template/' . $nome;
			$img = $dir . '/base.jpg';
			$config = include $dir . '/gerador.php';

			$g = new GeraTemplate();
			$g->gerarTemplate($img,$config,300);
		} else {
			echo "\tQual o nome do template?\n".
				 "\tUse --nome=<nome> sendo <nome> um diretorio em /data/tempalte\n";
		}
	}

}