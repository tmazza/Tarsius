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
		$validaTemplate = isset($_POST['validaTemplate']) ? (bool) $_POST['validaTemplate'] : false;
		$folhas = $_POST['folha'];
		# TODO: passar parâmetro para desconsiderar número do template

		$resultados = [];
		foreach ($folhas as $id) {
			$resultados[$id] = $this->processar($id,$minMatch,$validaTemplate);

		}

		$this->redirect(Yii::app()->request->urlReferrer);
		// $this->render('emMassa',[
		// 	'resultados'=>$resultados,
		// ]);
	}

	private function processar($id,$minMatch,$validaTemplate=true){
		$model = Distribuido::model()->findByPk((int)$id);
		$model->status = Distribuido::StatusReprocessamento;
		$ok = $model->update(['status']);

		$validaTemplate = (int) $validaTemplate;
        $cmd = 'hhvm ' . Yii::getPathOfAlias('application') .'/tarsius processa reprocessa';
	    $cmd .= " --id={$id}";
        $cmd .= " --minMatch={$minMatch}";
        $cmd .= " --validaTemplate={$validaTemplate}";

        $pid = exec($cmd . ' > /dev/null 2>&1 & echo $!; ');

		return [$ok,$model,$pid];
	}

}