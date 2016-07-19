<?php
class BaseController extends CController {

	public $wb;
	public $menu = [];

	protected function beforeAction($action){
		Yii::app()->clientScript->registerCoreScript('jquery');
		$this->wb = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.webroot'));
		return parent::beforeAction($action);
	}

}