<?php
include_once(Yii::getPathOfAlias('webroot') . '/../src/GeraTemplate.php');

class TemplateController extends BaseController {

	public $strFuncoes=[];

	protected function beforeAction($action){
		return parent::beforeAction($action);
	}

	public function actionIndex(){
		$this->render('index',[
			'templates'=>$this->getTemplate(),
		]);
	}

	public function actionCriar(){
		$model = new Template();

		if(isset($_FILES['file']) && isset($_POST['nome'])){
			$model->nome = $_POST['nome'];
			$model->file = $_FILES['file']['name'];

			# Cria diretorio para template
			$dirBasename = HView::dirname($model->nome);
			$dir = Yii::app()->params['templatesDir'] . '/' . $dirBasename ;
			if(!is_dir($dir))
				CFileHelper::createDirectory($dir,0777);

			# Move imagem para diretorio criado
			$filename = $dir.'/base.jpg';
			rename($_FILES['file']['tmp_name'],$filename);
			chmod($filename,0777);

			# Rediriona para tela edição/criação
			$this->redirect($this->createUrl('/template/editar',[
				'template'=>$dirBasename,
			]));
		}

		$this->render('upload',[
			'model'=>$model,
		]);
	}

	public function actionEditar($template){
		$this->layout = '//layouts/base';

		$dir = Yii::app()->params['templatesDir'] . '/' . $template ;
		if(file_exists($dir.'/gerador.php')){
			$config = include $dir.'/gerador.php';
			$blocos = $this->regiosFormatadas($config);			
		}

		$urlImage = Yii::app()->baseUrl . '/../data/template/'.$template.'/base.jpg';
		$this->render('gerar',[
			'blocos' => isset($blocos) ? $blocos : false,
			'qtdBlocos' => isset($blocos) ? count($config['regioes']) : false,
			'template' => $template,
			'urlImage' => $urlImage,
		]);
	}

	private function regiosFormatadas($config){
		$regioes = $config['regioes'];
		$formatadas = [];
		foreach ($regioes as $r) {
			$strFuncoes = unserialize(base64_decode($r['strFuncoes']));
			$formatadas[] = [
				'p1' => ['x'=>ceil($r['p1'][0]),'y'=>ceil($r['p2'][1]),'state'=>$r['tipo']],
				'p2' => ['x'=>ceil($r['p2'][0]),'y'=>ceil($r['p1'][1]),'state'=>$r['tipo']],
				'tipo' => (int) $r['tipo'],
				'colunasPorLinha' => $r['colunasPorLinha'],
			    'agrupaObjetos' => $r['agrupaObjetos'],
			    'minArea' => $r['minArea'],
			    'maxArea' => $r['maxArea'],
			    'id' => is_string($r['id']) ? $r['id'] : base64_encode($strFuncoes['id']),
			    'casoTrue' => is_string($r['casoTrue']) ? $r['casoTrue'] : base64_encode($strFuncoes['casoTrue']),
			    'casoFalse' => is_string($r['casoFalse']) ? $r['casoFalse'] : base64_encode($strFuncoes['casoFalse']),
			];
		}
		return CJSON::encode($formatadas);
   	}

	public function actionProcessar($template){
		$blocos = json_decode($_POST['pontos'],true);
		$dir = Yii::app()->params['templatesDir'] . '/' . $template;

		# formata arquivo gerador de template
		$regioes = $this->gerRegioesFormatadas($blocos,$dir);
		$strFuncoes = serialize($this->strFuncoes);
		$templateGerador = include $dir . '/../baseGerador.php';

		# grava arquivo gerador de template
		$handle = fopen($dir.'/'.'gerador.php', 'w+');
		fwrite($handle,"<?php\n".$templateGerador . "\n?>");
		fclose($handle);

		$this->gerarTempalteEstatico($dir);
		$this->redirect($this->createUrl('/template/index'));
	}

	public function actionPreview($template){
		$urlImage = Yii::app()->baseUrl . '/../data/template/'.$template.'/preview.jpg';
		echo CHtml::image($urlImage,'',['style'=>'width:100%']);	
	}

	/**
	 * Gerar arquivo .json com cada uma das regiões encontradas
	 */
	private function gerarTempalteEstatico($dir){
		$img = $dir . '/base.jpg';
		$config = include $dir . '/gerador.php';
		$g = new GeraTemplate();
		$g->gerarTemplate($img,$config,300);
	}

	/**
	 * Gera string com sintaxe em PHP da lista de regiõs.
	 */
	private function gerRegioesFormatadas($regioes,$dir){
		$strRegioes = '';
		foreach ($regioes as $r) {
			$this->strFuncoes = [];
			$tipo = (int) $r['tipo']; 
			$p1x = (float) $r['p1']['x']; 
			$p1y = (float) $r['p2']['y'];
			$p2x = (float) $r['p2']['x'];
			$p2y = (float) $r['p1']['y'];
			$colPorLin = (int) $this->getAttr($r,'colunasPorLinha');
			$agrupa = (int) $this->getAttr($r,'agrupaObjetos');
			$minArea = (int) $this->getAttr($r,'minArea');
			$maxArea = (int) $this->getAttr($r,'maxArea');
			$id = $this->getAttr($r,'id');
			$casoTrue = $this->getAttr($r,'casoTrue');
			$casoFalse = $this->getAttr($r,'casoFalse') ;
			$strFuncoes = "'" . base64_encode(serialize($this->strFuncoes)) . "'";
			$strRegioes .= include $dir . '/../baseRegiao.php';
		}
		return $strRegioes;
	}
	
	/**
	 * Retorna valor de $attr em $r. Caso $attr não exista, retorna o valor default
	 * definido para $attr.
	 */
	private function getAttr($r,$attr){
		if(in_array($attr, ['id','casoTrue','casoFalse'])){ # campos que podem conter callback como valor
			$return = isset($r[$attr]) ? $r[$attr] : $this->getDefault($attr);
			$this->strFuncoes[$attr] = "'" . $return . "'";
			return strpos($return, 'function') === false ? "'{$return}'" : $return;
		} else {
			return isset($r[$attr]) ? $r[$attr] : $this->getDefault($attr);
		}
	}

	/**
	 * Definição dos valores default dos attributos do arquivo gerador de template
	 */
	private function getDefault($attr){
		switch ($attr) {
			case 'colunasPorLinha': return 0;
			case 'agrupaObjetos': return 0;
			case 'minArea': return 300;
			case 'maxArea': return 3000;
			case 'id': return 0;
			case 'casoTrue': return 'S';
			case 'casoFalse': return 'N';
		}
	}

}