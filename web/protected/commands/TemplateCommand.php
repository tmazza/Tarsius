<?php
include_once(Yii::getPathOfAlias('webroot') . '/../../src/GeraTemplate.php');

class TemplateCommand extends CConsoleCommand {

	public function actionIndex(){
		$img = Yii::getPathOfAlias('webroot') . '/../../data/gerarTemplate/a.jpg';
		$g = new GeraTemplate('TEMPLATE_1');
		$g->exec($img);
	}

}