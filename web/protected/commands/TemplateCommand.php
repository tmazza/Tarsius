<?php
include_once(Yii::getPathOfAlias('webroot') . '/../../src/GeraTemplate.php');
include_once(Yii::getPathOfAlias('webroot') . '/../../src/GeraTemplateDuasReferencias.php');
include_once(Yii::getPathOfAlias('webroot') . '/../../src/GeraTemplateQuatroReferencias.php');


class TemplateCommand extends CConsoleCommand {

	private $resolucaoBase = 300;
	/**
	 * Usa como referência somente a âncora 1
	 */
	public function actionIndex($nome=false){
		if($nome){
			$dir = Yii::getPathOfAlias('webroot') . '/../../data/template/' . $nome;
			$img = $dir . '/base.jpg';
			$config = require $dir . '/gerador.php';
			$g = new GeraTemplate();
			$g->gerarTemplate($img,$config,$this->resolucaoBase);
		} else {
			echo "\tQual o nome do template?\n".
				 "\tUse --nome=<nome> sendo <nome> um diretorio em /data/tempalte\n";
		}
	}

	/**
	 * Usa como referência a média entre as âncora 1 e 3.
	 */
	public function actionDouble($nome=false){
		if($nome){
			$dir = Yii::getPathOfAlias('webroot') . '/../../data/template/' . $nome;
			$img = $dir . '/base.jpg';
			$config = include $dir . '/gerador.php';

			$g = new GeraTemplateDuasReferencias();
			$g->gerarTemplate($img,$config,$this->resolucaoBase);
		} else {
			echo "\tQual o nome do template?\n".
				 "\tUse --nome=<nome> sendo <nome> um diretorio em /data/tempalte\n";
		}
	}


	/**
	 * Usa como referência somente todas as âncoras. Calculando a média entre
	 * 1 e 3 e entre 2 e 4. Depois a média entre dois resultados.
	 */
	public function actionQuadruple($nome=false){
		if($nome){
			$dir = Yii::getPathOfAlias('webroot') . '/../../data/template/' . $nome;
			$img = $dir . '/base.jpg';
			$config = include $dir . '/gerador.php';

			$g = new GeraTemplateQuatroReferencias();
			$g->gerarTemplate($img,$config,$this->resolucaoBase);
		} else {
			echo "\tQual o nome do template?\n".
				 "\tUse --nome=<nome> sendo <nome> um diretorio em /data/tempalte\n";
		}
	}

}