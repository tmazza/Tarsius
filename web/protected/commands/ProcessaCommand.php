<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

/**
 * Aplica máscara a uma imagem ou a um diretório de trabalho.
 */
class ProcessaCommand extends CConsoleCommand 
{

	public $dirIn;
	public $trabalho;

	/**
	 * Processa uma imagem aplicando o template. resultado é retornado em stdin
	 *
	 * @param string $arquivo Caminho absoluto para image que deve ser processdao
	 * @param string $template Nome do template a ser aplicado, $template deve ser
	 * 		um nome existente dentro do diretório definido em templatesDir no arquivo
	 *		de configuração da aplicação.
	 */
	public function actionIndex($arquivo=false, $template=false)
	{
		if (!($arquivo && $template)){
			die("Informe o trabalho e o template em uso. \n\n\t--template=<ID-TRABALHO>\n\t--arquivo=<ID-ARQUIVO>\n\n");
		}
		if (!file_exists($arquivo)) {
			die("Imagem '{$arquivo}' não existe ou não pode ser encontrada.\n");
		}
		$template = Yii::app()->params['templatesDir'] . '/' . $template . '/template.json';

		$form = new Tarsius\Form($arquivo, $template);
		try {
			$output = $form->evaluate();
			print_r($output);
			echo 'Tempo decorrido: ' . $output['totalTime'] . "\n";
		} catch(Exception $e) {
			echo $e->getMessage() . "\n" . $e;	
		}
	}

	/**
	 * Processa diretório definido em $dirIn do trabalho na pasta do trabalho $trabId, 
	 * a qual terá como diretório base o caminho definido em runtimeDir no arquivo de configuração
	 * da aplicação movendo cada imagem processada para $dirOut
	 *
	 * @param string Caminho relativo do diretório dentro de $runtimeDir/trab-$trabId.
	 * @param string Caminho absoluto do diretório onde as imagens devem ser removidas. 
	 * @param int $trabId Número do trabalho a ser processado.
	 */
	public function actionDirectory($dirIn=false, $dirOut=false, $trabId=false){
		try {
			$this->initParameters($dirIn, $dirOut, $trabId);

			# Busca todos os arquivos jpg do diretório
			$files = CFileHelper::findFiles($this->dirIn, [
				'fileTypes' => ['jpg'],
			]);		

			$count = 0; $first = true;

			foreach ($files as $imageName) {
			   	$count++;

			   	# atualiza objeto trabalho com valores do banco
			   	if ($first || $count % 10 == 0) {
			   		$this->trabalho = Trabalho::model()->findByPk($trabId);
			   		if (is_null($this->trabalho)) {
			   			throw new Exception("Trabalho '{$trabId}' não encontrado.");
			   		}
					# TODO: configurar tarsius (inlcurir capos no tarbalho e passar para Tarsius::config)
				 	$template = Yii::app()->params['templatesDir'] . '/' . $this->trabalho->template . '/template.json';
				}

				# interpreta regiões da imagem
		   	 	if ($this->trabalho->status == Trabalho::statusExecutando) {
					$form = new Tarsius\Form($imageName, $template);
					$result = $form->evaluate();
					$basename = basename($imageName);
					$content = json_encode($result);
					$exported = $this->export($result);
					Finalizado::insertOne($this->trabalho->id, $basename, $content, $exported);
				} 

				# move arquivo para diretório destino/de saída
				if (!rename($imageName, $dirOut . basename($imageName))) {
					$diretoSaida = $dirOut . basename($imageName);
					throw new Exception("Arquivo '{$imageName}' não pode ser movido para '{$diretoSaida}'. ");
				}

				# Cancela folha distruída caso trabalho tenha sido pausado.
				if ($this->trabalho->status !== Trabalho::statusExecutando) {
					$qtd = Distribuido::model()->updateAll([
						'status' 		 => Distribuido::StatusParado,	
						'nome' 			 => basename($imageName) . ' - canelada em ' . date('d/m/Y H:i:s'),	
						'dataFechamento' => time(),	  
					],[
						'condition' => "trabalho_id={$this->trabalho->id}" 
								. " AND status=" . Distribuido::StatusParado
								. " AND nome='" . basename($imageName) . "'",
					]);
					if ($qtd !== 1) {
						throw new Exception("Erro ao cancelar distribuição de '{$imageName}'. ");
					}
				}

				
			}

	  		# remove todo o diretório de trabalho do processo
			if (!rmdir($this->dirIn)) {
				throw new Exception("Diretório '{$this->dirIn}' não pode ser removido. ");
			}

		} catch(Exception $e) {

			Erro::insertOne($this->trabalho->id, $e->getMessage(), $e->__toString());

		}
	}

	/**
	 * Verifica se parãmetros informados estão de acordo como o esperado.
	 * 
	 * @param string $dirIn
	 * @param string $dirOut
	 * @param int $trabId
	 */
	private function initParameters($dirIn, $dirOut, $trabId)
	{
		if(!$dirIn) die("Informe um diretorio de trabalho. Use --dirIn=<CAMINHO-RELATIVO>\n");
		if(!$dirOut) die("Informe um diretorio para expotar as imagens. Use --dirOut=<CAMINHO-ABSOLUTO>\n");
		if(!$trabId) die("Qual o ID do trabalho? Use --trabId=<ID-TRABALHO>\n");
			
		$runtimeDir = Yii::app()->params['runtimeDir'];
		if(!is_dir($runtimeDir)){
			die("Diretorio '{$runtimeDir}' não encontrado ou não existe.\n");
		}
		if(!is_dir($dirOut)){
			die("Diretorio '{$dirOut}' não encontrado ou não existe.\n");
		}

		$dirOut = trim($dirOut);
		if (substr($dirOut, -1) !== '/') {
			$dirOut .= '/';
		}

		$this->dirIn .=  "{$runtimeDir}/trab-{$trabId}/exec/ready/{$dirIn}";
	}

	/**
	 * Salva no banco definido em dbExport resultado do processamento da imagem
	 *
	 * @todo Exportar registro. Criar modelo defaul export.
	 *
	 * @param array $result
	 */
	private function export($result)
	{
		try {
			return false;
		} catch(Exception $e) {
			return false;
		}
	}

}