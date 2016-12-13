<?php

class ConfiguracaoController extends BaseController
{
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Configuracao;

		if(isset($_POST['Configuracao']))
		{
			$model->attributes=$_POST['Configuracao'];
			if($model->save()){
				if($model->ativo) {
					Configuracao::model()->updateAll([
						'ativo' => 0,
					],[
						'condition' => 'id != ' . $model->id,
					]);
				}
				$this->redirect(array('index'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['Configuracao']))
		{
			$model->attributes=$_POST['Configuracao'];
			if($model->save()){
				if($model->ativo) {
					Configuracao::model()->updateAll([
						'ativo' => 0,
					],[
						'condition' => 'id != ' . $model->id,
					]);
				}
				$this->redirect(array('index'));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Configuracao');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}


	public function actionMakeActive($id)
	{
		if($this->loadModel($id)->makeActive()){
			HView::fMsg('Configuração ativa alterada.');
		} else {
			HView::fMsg('NÃO foi possível atualizar a configuração ativa ativa alterada.');
		}
		$this->redirect($this->createUrl('/configuracao/index'));
	}	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Configuracao the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Configuracao::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Configuracao $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='configuracao-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
