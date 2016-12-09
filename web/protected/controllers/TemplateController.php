<?php

class TemplateController extends BaseController {

	public $strFuncoes=[];
	private $resolucaoBase = 300;

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
			if($_FILES['file']['error'] == UPLOAD_ERR_OK) {
				$model->nome = $_POST['nome'];
				$model->file = $_FILES['file']['name'];

				# Cria diretorio para template
				$dirBasename = HView::dirname($model->nome);
				$dir = Yii::app()->params['templatesDir'] . '/' . $dirBasename ;
				if(!is_dir(Yii::app()->params['templatesDir'])){
					CFileHelper::createDirectory(Yii::app()->params['templatesDir'],0777);
				}
				if(!is_dir($dir)){
					CFileHelper::createDirectory($dir,0777);
				}

				# Move imagem para diretorio criado
				$filename = $dir.'/base.jpg';
				rename($_FILES['file']['tmp_name'],$filename);
				chmod($filename,0777);

				# Rediriona para tela edição/criação
				$this->redirect($this->createUrl('/template/editar',[
					'template'=>$dirBasename,
				]));
			} else {
				HView::fMsg('Erro ' . $this->getUploadError($_FILES['file']['error']));
			}
		}

		$this->render('upload',[
			'model'=>$model,
		]);
	}

	private function getUploadError($errorID)
	{
		switch ($errorID) {

			case UPLOAD_ERR_INI_SIZE: 
				return "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
			case UPLOAD_ERR_FORM_SIZE: 
				return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
			case UPLOAD_ERR_PARTIAL: 
				return "The uploaded file was only partially uploaded.";
			case UPLOAD_ERR_NO_FILE: 
				return "No file was uploaded.";
			case UPLOAD_ERR_NO_TMP_DIR: 
				return "Missing a temporary folder. Introduced in PHP 5.0.3.";
			case UPLOAD_ERR_CANT_WRITE: 
				return "Failed to write file to disk. Introduced in PHP 5.1.0.";
			case UPLOAD_ERR_EXTENSION: 
				return "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.";
			default:
				return "Erro desncohecido ao fazer upload.";
		}
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

	public function actionEditarSaida($template)
	{
		$dir = Yii::app()->params['templatesDir'] . '/' . $template ;
		if(!file_exists($dir.'/gerador.php')){
			echo 'erro'; exit;	
		}

		$file = $dir.'/gerador.php';
		$h = fopen($file,'r');
		$content = fread($h, filesize($file));
		fclose($h);

		if(isset($_POST['config'])){
			$content = $_POST['config'];
			$content = trim(str_replace('<?phpre', "<?php\nre", $content));
			$h = fopen($file,'w+');
			fwrite($h, $content);
			fclose($h);
		}

		$this->render('edicaoSaida',[
			'template' => $template,
			'content' => $content,
		]);

	}	

	/**
	 * Gera arquivo JSON do template.
	 */
	public function actionReprocessar($template,$tipo=1)
	{
		ini_set("memory_limit","2048M");

		$templateDir = Yii::getPathOfAlias('webroot') . '/../data/template/' . $template;
		$config = include $templateDir . '/gerador.php';

		$gerador = new Tarsius\MaskGenerator($template, $templateDir . '/base.jpg', $config);
		try {
			$gerador->generate();
			HView::fMsg(CHtml::tag('h3', [], 'Reprocessamento de ' . $template . ' finalizado.'));	
		} catch (Exception $e) {
			HView::fMsg(CHtml::tag('h3', [], $e->getMessage()) . CHtml::tag('pre', [], $e));				
		}

		$this->redirect($this->createUrl('/template/index'));
	}

	public function actionExcluir($template)
	{
		$dir = Yii::app()->params['templatesDir'] . '/' . $template ;
		if(is_dir($dir)){
			CFileHelper::removeDirectory($dir);
		}
		$this->redirect($this->createUrl('/template/index'));
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
		$regioes = $this->getRegioesFormatadas($blocos, $dir);
		$formatoSaida = $this->getFormatoSaida();	
		$templateGerador = $this->getBaseGerador($template, $regioes, $formatoSaida);

		# grava arquivo gerador de template
		$handle = fopen($dir.'/'.'gerador.php', 'w+');
		fwrite($handle,"<?php\n".$templateGerador . "\n?>");
		fclose($handle);

		$this->gerarTempalteEstatico($dir);
		$this->redirect($this->createUrl('/template/index'));
	}

	public function actionPreview($template){
		$urlImage = Yii::app()->baseUrl . '/../data/template/'.$template.'/template.png';
		echo CHtml::tag('div', [
			'style'=>'width:80%;margin: 0 auto;',
		], CHtml::image($urlImage));	
	}

	/**
	 * Gerar arquivo .json com cada uma das regiões encontradas
	 * 
	 * @todo unificar com reprocessar(). Faz a mesma coisa. 
	 *
	 */
	private function gerarTempalteEstatico($dir)
	{
		ini_set("memory_limit","2048M");

		$config = include $dir . '/gerador.php';
		$gerador = new Tarsius\MaskGenerator($dir, $dir . '/base.jpg', $config);
		try {
			$gerador->generate();
		} catch (Exception $e) {
			HView::fMsg(CHtml::tag('h3', [], $e->getMessage()) . CHtml::tag('pre', [], $e));				
		}
	}

	/**
	 * Gera string com sintaxe em PHP da lista de regiõs.
	 */
	private function getRegioesFormatadas($regioes,$dir){
		$strRegioes = '';
		foreach ($regioes as $r) {
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
			$strRegioes .= $this->getTemplateRegiao($tipo,$p1x,$p1y,$p2x,$p2y,$colPorLin,$agrupa,$minArea,$maxArea,$id,$casoTrue,$casoFalse);
		}

		return $strRegioes;
	}

	/**
	 * Array definindo a configuração da string final com os resultados.
	 * A chave deve conter um ID identificando a seleçaõ de resultados.
	 * O valor pode ser string o array.
	 *   - Se for uma string ela ser o ID de um região do template
	 * 	 - Se for array este deve possuir dous atributos.
	 * 		- match: deve ser uma expressão regular definindo quais as regiões 
	 *			do template serão selecionadas. A busca será feita pelo ID da
     *			região.
	 *		- order: pode ser uma boolean(false) ou uma função callback().
	 * 			boolean: deve ser explicitada a intenção da ordem, caso não deva
	 *				  ser aplica nenhuma ordem o valor false deve ser informado.
	 *			callback: será aplicada na lista de elemento selecionadas usando
	 *				usort() (http://php.net/manual/pt_BR/function.usort.php)
	 */
	public function getFormatoSaida(){
		return <<<DEFAULTOUTPUTFORMAT
[
    'respostas' => [
      'match' => '/^e-.*-\d$/',
	  'order' => false,
    ],
  ]
DEFAULTOUTPUTFORMAT;
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

	private function getBaseGerador($template,$regioes,$formatoSaida){
		return <<<BASEGERADOR
return [
  'nome' => '{$template}',
  'regioes' => [
    {$regioes}
  ],
  'formatoSaida' => {$formatoSaida},
];
BASEGERADOR;
	}

	private function getTemplateRegiao($tipo,$p1x,$p1y,$p2x,$p2y,$colPorLin,$agrupa,$minArea,$maxArea,$id,$casoTrue,$casoFalse){
		return <<<TEMPLATEREGIAO
[
    'tipo' => $tipo,
    'p1' => [$p1x,$p1y],
    'p2' => [$p2x,$p2y],
    'colunasPorLinha' => $colPorLin,
    'agrupaObjetos' => $agrupa,
    'minArea' => $minArea,
    'maxArea' => $maxArea,
    'id' => $id,
    'casoTrue' => $casoTrue,
    'casoFalse' => $casoFalse,
  ],
TEMPLATEREGIAO;
	}


}