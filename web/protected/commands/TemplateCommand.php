<?php
include_once(Yii::getPathOfAlias('webroot') . '/../../src/GeraTemplate.php');
include_once(Yii::getPathOfAlias('webroot') . '/../../src/GeraTemplateDuasReferencias.php');
include_once(Yii::getPathOfAlias('webroot') . '/../../src/GeraTemplateQuatroReferencias.php');

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

	public function actionDouble($nome=false){
		if($nome){
			$dir = Yii::getPathOfAlias('webroot') . '/../../data/template/' . $nome;
			$img = $dir . '/base.jpg';
			$config = include $dir . '/gerador.php';

			$g = new GeraTemplateDuasReferencias();
			$g->gerarTemplate($img,$config,300);
		} else {
			echo "\tQual o nome do template?\n".
				 "\tUse --nome=<nome> sendo <nome> um diretorio em /data/tempalte\n";
		}
	}

	public function actionQuadruple($nome=false){
		if($nome){
			$dir = Yii::getPathOfAlias('webroot') . '/../../data/template/' . $nome;
			$img = $dir . '/base.jpg';
			$config = include $dir . '/gerador.php';

			$g = new GeraTemplateQuatroReferencias();
			$g->gerarTemplate($img,$config,300);
		} else {
			echo "\tQual o nome do template?\n".
				 "\tUse --nome=<nome> sendo <nome> um diretorio em /data/tempalte\n";
		}
	}

}