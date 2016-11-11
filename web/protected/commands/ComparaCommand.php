<?php
class ComparaCommand extends CConsoleCommand {

	public $trabalho;
	public $concurso;
	public $folha;

	private function inicializar($trabalho,$concurso,$folha){
		if(!$trabalho)
			throw new Exception('Trabalho não informado. Use --trabalho=<trabalho_id>', 500);
		$this->trabalho = $trabalho;
		if(!$concurso)
			throw new Exception('Concurso não informado. Use --concurso=<CodConcurso>', 500);
		$this->concurso = $concurso;
		if(!$folha)
			throw new Exception('Folha não informada. Use --folha=<FolhaLeitura>', 500);
		$this->folha = $folha;
	}

	public function actionIndex($trabalho=false,$concurso=false,$folha=false){
		try {
			$this->inicializar($trabalho,$concurso,$folha);
			$this->processaResultados();		
		} catch(Exception $e) {
			echo $e->getMessage() . "\n";			
		}
	}

	private function processaResultados(){
		$data = Yii::app()->dbExport->createCommand()
				->select('NomeArquivo,RespostasOriginais')
				->from('dbo.LEITURA')
				->where('Concurso = ' . $this->concurso . ' AND FolhaLeitura=' . $this->folha)
				->queryAll();
		$iguais = $naoEncontradas = $respNaoDefinida = $diferencas = [];
		$count = 0;
		echo "\n";
		foreach ($data as $r) {
			$nome = $r['NomeArquivo'];
			$resp = trim($r['RespostasOriginais']);
			$distribuido = Distribuido::model()->find([
				'condition' => "trabalho_id={$this->trabalho} AND nome='{$nome}.jpg'",
			]);	
			if(is_null($distribuido)){
				$naoEncontradas[] = $nome;
			} else {
				$processado = $distribuido->resultado;
				$conteudo = json_decode($processado->conteudo,true);
				if(isset($conteudo['saidaFormatada']['respostas'])){
					$resp2 = trim($conteudo['saidaFormatada']['respostas']);
					if($resp == $resp2){
						$iguais[] = $nome;
					} else {
						$diferencas[$nome] = [
							'model'=>$distribuido,
							'respLocal' => $resp2,
							'respExpor' => $resp,
							'diferencas' => $this->getDiferencas($resp,$resp2),
						];
					}
				} else {
					$respNaoDefinida[] = $nome;
				}
			}
			$count++;
			echo "\rComparando... " . str_pad($count,6,'0',STR_PAD_LEFT);
		}
		echo ".\tFinalizado! \o/\n\n";
		echo 'Diferenças: ' . count($diferencas) . "\n";
		echo 'Iguais: ' . count($iguais) . "\n";
		echo 'Nao encontradas: ' . count($naoEncontradas) . "\n";
		echo 'RespNaoDefinida: ' . count($respNaoDefinida) . "\n";
		$this->geraArquivo($diferencas,$iguais,$naoEncontradas,$respNaoDefinida);
	}


	private function geraArquivo($diferencas,$iguais,$naoEncontradas,$respNaoDefinida){
		$model = Trabalho::model()->findByPk((int)$this->trabalho);
		$template = $this->render('comparacao',[
			'diferencas'=>$diferencas,
			'iguais'=>$iguais,
			'naoEncontradas'=>$naoEncontradas,
			'respNaoDefinida'=>$respNaoDefinida,
			'trabalho'=>$this->trabalho,
			'concurso'=>$this->concurso,
			'url'=>str_replace('/repositorios/',Yii::app()->params['urlBase'],$model->sourceDir).'/',
		]);

		$dir = __DIR__ . '/../../../data/comparacoes/';
		if(!is_dir($dir)) mkdir($dir);
		$nomeArquivo = "comparacao_trab_{$this->trabalho}_conc_{$this->concurso}_folha_{$this->folha}.html";
		$h = fopen($dir.$nomeArquivo,'w+');
		fwrite($h,$template);
		fclose($h);

		echo "Arquivo '{$nomeArquivo}' gerado.\n";

	}


	private function getDiferencas($str1,$str2){
		$arrResp = str_split($str1);
		$arrResp2 = str_split($str2);
		$diferencas = [];
		foreach ($arrResp as $k => $r) {
			if($r != $arrResp2[$k]){
				$diferencas[] = [
					'posicao' => $k,
					'local' => $arrResp2[$k],
					'export' => $r,
				];
			}
		}
		return $diferencas;
	}

	private function render($template,$data=[]) {
	    $path = Yii::getPathOfAlias('application.views.static').'/'.$template.'.php';
	    if(!file_exists($path)) throw new Exception('Template '.$path.' does not exist.');
	    return $this->renderFile($path, $data, true);
	}

}