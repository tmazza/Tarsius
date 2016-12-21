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
    public $pid;

    /**
     * Processa uma imagem aplicando o template. resultado é retornado em stdin
     *
     * @param string $arquivo Caminho absoluto para image que deve ser processdao
     * @param string $template Nome do template a ser aplicado, $template deve ser
     *      um nome existente dentro do diretório definido em templatesDir no arquivo
     *      de configuração da aplicação.
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
                    
                    $this->applyJobConfiguration();

                    $template = Yii::app()->params['templatesDir'] . '/' . $this->trabalho->template . '/template.json';
                }

                # interpreta regiões da imagem
                if ($this->trabalho->status == Trabalho::statusExecutando) {
                    
                    $basename = basename($imageName);

                    try {
                        $form = new Tarsius\Form($imageName, $template);
                        $result = $form->evaluate();
                        $content = json_encode($result);
                        $exported = $this->export($imageName, $result['result']);
                    } catch(Exception $e) {
                        $exported = false;
                        $content = json_encode($e->getMessage() . ' <hr> ' . $e->__toString());
                    }

                    Finalizado::insertOne($this->trabalho->id, $basename, $content, $exported);
                } 

                # move arquivo para diretório destino/de saída
                if (!rename($imageName, $dirOut . basename($imageName))) {
                    $diretoSaida = $dirOut . basename($imageName);
                    throw new Exception("Arquivo '{$imageName}' não pode ser movido para '{$diretoSaida}'. ");
                }

                # Cancela folha distruída caso trabalho tenha sido pausado.
                if ($this->trabalho->status != Trabalho::statusExecutando) {
                    $qtd = Distribuido::model()->updateAll([
                        'status'         => Distribuido::StatusParado,  
                        'nome'           => basename($imageName) . ' - canelada em ' . date('d/m/Y H:i:s'), 
                        'dataFechamento' => time(),   
                    ],[
                        'condition' => "trabalho_id={$this->trabalho->id}" 
                                . " AND status=" . Distribuido::StatusAguardando
                                . " AND nome='" . basename($imageName) . "'",
                    ]);
                    if ($qtd != 1) {
                        throw new Exception("Erro ao cancelar distribuição de '" . basename($imageName) . "'. QTD: {$qtd}");
                    }
                }

                
            }

            # remove todo o diretório de trabalho do processo
            if (!rmdir($this->dirIn)) {
                throw new Exception("Diretório '{$this->dirIn}' não pode ser removido. ");
            }

            # Atualiza status do processo. Usado para controlar a distribuição
            $dirIn = basename($this->dirIn);
            $qtd = Processo::model()->updateAll([
                'status'  => Processo::StatusFinalizado,
                'dataFim' => time(),
            ], "trabalho_id={$trabId} AND workDir='{$dirIn}'");
            if ($qtd != 1) {
                throw new Exception("Erro ao atualizar processo. PID: '{$this->pid}'.");
            }

        } catch(Exception $e) {

            # TODO: mover imagens apra diretorios de destino

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
    private function initParameters(&$dirIn, &$dirOut, $trabId)
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
        $this->pid = getmypid();
    }

    /**
     * Salva no banco definido em dbExport resultado do processamento da imagem
     *
     * @todo Exportar registro. Criar modelo defaul export.
     *
     * @param array $result
     */
    private function export(&$name, &$result)
    {
        $active = Configuracao::getActive();
     
        # TODO: possibilitar configuração global
        $result['exportTime'] = date('Y-m-d H:i:s');
        $result['filename'] = pathinfo($name, PATHINFO_FILENAME);

        $exportFileds = json_decode($this->trabalho->export, true);
        $exportContent = Trabalho::getExportContent($this->trabalho->id, $result, $exportFileds);

        if ($active->isExportEnable()) {
            return Trabalho::doExport($active, $exportContent);
        } else if ($active->isExportWating()) {
            return false;
        } else {
            return true;
        }

        return false;
    }

    /**
     * Aplica as confirações do trabalho.
     */
    private function applyJobConfiguration()
    {
        if(!is_null($this->trabalho->perfil)) {
            $parameters = Tarsius\Tarsius::getParameters();
            $filtered = [];
            foreach ($this->trabalho->perfil->attributes as $key => $value) {
                if (in_array($key, $parameters)) {
                    $filtered[$key] = $value;
                }
            }
            Tarsius\Tarsius::config($filtered);
        }        
    }


    /**
     * Processa conjunto de registro definidos em $args aplicando as confirgurações de 
     * do valor mínimo para considerar dois objetos iguais mínimo e uso do validador 
     * de tempalte definidos. 
     *
     * @param array $args Conjunto de PK dos registro em Distribuido
     * @param float $minMatch Valor mínimo para considerar dois objetos iguais
     * @param bool $validaTemplate Se deve ser aplicada a validação do template.
     */
    public function actionRedo($args=[], $minMatch=0.8, $validaTemplate=true)
    {

        $config = [
            'minMatchObject' => $minMatch,
        ];
        if (!$validaTemplate) {
            $config['templateValidationTolerance'] = 1000;
        }
        Tarsius\Tarsius::config($config);

        try {

            foreach ($args as $id) {
                $model = Distribuido::model()->findByPk($id, "status = " . Distribuido::StatusReprocessamento);
                
                if (is_null($model)){
                    throw new Exception("Arquivo para reprocessamento não encontrado. ID: '{$id}'");
                } else {

                    $template = Yii::app()->params['templatesDir'] . '/' . $model->trabalho->template . '/template.json';
                    if (substr($model->trabalho->sourceDir, -1) !== '/') {
                        $model->trabalho->sourceDir .= '/';
                    }

                    try {
                        
                        $arquivo = $model->trabalho->sourceDir . $model->nome;
                        $form = new Tarsius\Form($arquivo, $template);
                        $output = $form->evaluate();
                        $model->resultado->conteudo = json_encode($output);
                        $model->resultado->update(['conteudo']);
                    
                    } catch(Exception $e) {
                        
                    }

                    $model->status = Distribuido::StatusAguardando;
                    $model->update(['status']);



                }
            }
        } catch (Exception $e) {

            echo $e->getMessage();
            if (is_null($model)) {
                Erro::insertOne(0, $e->getMessage(), $e->__toString());
            } else {
                Erro::insertOne($model->trabalho->id, $e->getMessage(), $e->__toString());
            }
        }
    }

}