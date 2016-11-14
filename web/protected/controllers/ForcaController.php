<?php
include_once(Yii::getPathOfAlias('webroot') . '/../src/Image.php');
class ForcaController extends BaseController {

	public function actionIndex($id,$msg=false){
		if(isset($_POST['minMatch'])){
			$validaTemplate = isset($_POST['validaTemplate']) && $_POST['validaTemplate'];
			$minMatch = (float) $_POST['minMatch'];
			list($ok,$model,$msg) = $this->processar($id,$minMatch,$validaTemplate);
			if($ok){
				$this->redirect($this->createUrl('/distribuido/ver',[
					'id'=>$model->id,
					'renovar'=>1,
				]));
			} else {
				$this->redirect($this->createUrl('/forca/index',[
					'id'=>$model->id,
					'msg'=>$msg . ' | com ' . $minMatch,
				]));
			}
		}
		$this->render('index',[
			'msg'=>$msg,
		]);
	}

	public function actionEmMassa()
	{
		set_time_limit(0);
		$minMatch = (float) $_POST['minMatch'];
		$folhas = $_POST['folha'];
		# TODO: passar parâmetro para desconsiderar número do template

		$resultados = [];
		foreach ($folhas as $id) {
			$resultados[$id] = $this->processar($id,$minMatch);

		}

		$this->redirect(Yii::app()->request->urlReferrer);
		// $this->render('emMassa',[
		// 	'resultados'=>$resultados,
		// ]);
	}

	private function processar($id,$minMatch,$validaTemplate=true){
		$model = Distribuido::model()->findByPk((int)$id);

		$ok = true;
		try {
			$image = new Image($model->trabalho->template,$model->trabalho->taxaPreenchimento,$minMatch);
			$image->validaTemplate = $validaTemplate;
			$image->exec($model->trabalho->sourceDir.'/'.$model->nome);
			$model->resultado->conteudo = json_encode($image->output);
			$model->resultado->update(['conteudo']);
			$msg = 'ok';			
		} catch (Exception $e) {
			$ok = false;
			$msg = $e->getMessage();
		}	

		return [$ok,$model,$msg];
	}

}