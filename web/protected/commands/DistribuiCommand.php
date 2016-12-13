<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

/**
 * Distribui um conjunto de imagens entre vários processos, repete esta iteração
 * enquanto o status do trabalho for Trabalho::statusExecutando
 */
class DistribuiCommand extends CConsoleCommand
{

    private $qtdProcessadores = false;
    private $limiteProcessos = false;
    private $tamMaxBlocoPorProcesso = 80;

    private $trabalho;
    private $dirBase;


    /** 
     * Distribui imagens de um diertorio de tempo em tempo. Cria um processo para 
     * cada processador disponível na máquina, caso não seja possível reconhecer
     * quantos processadores a máquina possui o valor default em {@var $qtdProcessadores}
     * será utilizado. 
     * 
     * Cada processo gerado executa em um diretório serparada. 1
     *
     * @param int $trabId ID do trabahlo sendo processado.
     */
    public function actionIndex($trabId=false)
    {
        if ($trabId) {
        
            try {
        
                $this->setProcessors($trabId);
                $this->dirBase = Yii::app()->params['runtimeDir'];

                $this->setTrabalho($trabId);  

                $execDir = Yii::app()->params['runtimeDir'] . "/trab-{$trabId}/exec";
                $readyDir = $execDir . '/ready';

                # Cria diretórios recursivamente
                if (!is_dir($readyDir)){
                    CFileHelper::createDirectory($readyDir, 0777, true);
                }

                # distribui imagens e monitor diretório definido no trabalho por novas imagens
                $this->loop($execDir, $readyDir);

                # para trabalho ao sair do loop
                $this->trabalho->status = 0;
                $this->trabalho->update(['status']); 

            } catch(Exception $e) {

                Erro::insertOne($trabId, $e->getMessage(), $e->__toString());

            }

        } else {

            echo "Qual o trabalho? Use --trabId=<ID-TRABALHO>\n";

        }
    }

    /**
     * Defini a quantidade de processadores disponícveis na máquina
     *
     * @param int $trabId
     */
    private function setProcessors($trabId)
    {
        # Define número de processadores da máquina
        $processadores = (int) exec("nproc");
        if ($processadores <= 0){
            throw new Exception('Número de processadores não foi identificado. Considerando ' . $this->qtdProcessadores);
        } else {
            $this->qtdProcessadores = $processadores;      
        }
        # Quantidade máxima de processos
        $config = Configuracao::model()->find("ativo=1");
        if (is_null($config)) {
            throw new Exception("Nenhuma configuração global ativa.");
        }
        $this->limiteProcessos = is_null($config->maxProcessosAtivos) ? $processadores : $config->maxProcessosAtivos;
        $this->tamMaxBlocoPorProcesso = is_null($config->maxAquivosProcessos) ? 80 : $config->maxAquivosProcessos;
        
    }

    /**
     * Distribuí imagens enquanto statud do trabalho dor Trabalho::statusExecutando.
     *
     * @param string $execDir
     * @param string $readyDir
     */ 
    private function loop($execDir, $readyDir)
    {
        do {

            # Diretório de origem das imagens
            $sourceDir = $this->trabalho->sourceDir;
            if (substr($sourceDir, -1) != '/') {
                $sourceDir .= '/';
            }

            # busca todos os arquivos do diretório definido no trabalho
            $files = CFileHelper::findFiles($this->trabalho->sourceDir, [
                'fileTypes' => ['jpg'],
                'absolutePaths' => false,
            ]);

            # desconsidera arquivos que já foram processados
            $files = array_diff($files, $this->trabalho->getJaDistribuidos());

            # define quantidade de processos que podem ser criados.
            $processosAtivos = $this->trabalho->qtdProcessosAtivos();
            $processosLivres = $this->limiteProcessos ? $this->limiteProcessos - $processosAtivos : $processosAtivos;   

            echo $processosLivres;

            $qtdArquivos = count($files);
    
            if ($qtdArquivos > 0 && $processosLivres > 0) {

                # realiza distribuição
                $this->trabalho->setDistribuindo(1);

                # aplica limite na quantiade de imagens por processo
                $tamBlocoPorProcesso = ceil($qtdArquivos / $processosLivres);
                if ($this->tamMaxBlocoPorProcesso && $tamBlocoPorProcesso > $this->tamMaxBlocoPorProcesso) {
                    $tamBlocoPorProcesso = $this->tamMaxBlocoPorProcesso;
                }

                # aplica limite na quantidade total de arquivos processados
                $files = array_slice($files, 0, $tamBlocoPorProcesso * $processosLivres); 
                $blocos = array_chunk($files, $tamBlocoPorProcesso);
                
                # cria um processo para cada conjunto de imagens
                foreach ($blocos as $i => $bloco) {

                    # diretório temporário até mover todas as imagens
                    $dirName = hash('md5', microtime(true) . rand(0, 99));
                    $tempDir = $execDir . '/' . $dirName . '/';
                    CFileHelper::createDirectory($tempDir, 0777);


                    foreach ($bloco as $file) {
                        if (rename($sourceDir . $file, $tempDir . $file)){
                            Distribuido::insertOne($this->trabalho->id, $file, $dirName);
                        } else {
                            throw new Exception("Falha ao mover arquivo: {$file} \n");
                        }
                    }


                    # Diretorio final após buscar todas imagens do processo
                    rename($tempDir, $readyDir . '/' . $dirName); 
                    
                    # comando para processar diretório
                    $cmd = $this->trabalho->command . ' ';
                    $cmd .= Yii::getPathOfAlias('application') .'/tarsius processa directory';
                    $cmd .= " --dirIn={$dirName}";
                    $cmd .= " --dirOut={$this->trabalho->sourceDir}";
                    $cmd .= " --trabId={$this->trabalho->id}";

                    # Executa e registro PID do processo
                    $pid = exec($cmd . ' > /dev/null 2>&1 & echo $!; ');
                    Processo::insertOne($pid, $dirName, count($bloco), $this->trabalho->id);

                }

                # Atualiza situação do trabalho
                $this->trabalho->setDistribuindo(0);

            }

            # Aguarda próximo ciclo de distribuição
            sleep($this->trabalho->tempoDistribuicao);

            # Atualiza estado do objeto trabalho
            $this->trabalho = Trabalho::model()->findByPk($this->trabalho->id);

        } while ($this->trabalho->status == 1);
    }

    /**
     * Busca informações do trabalho. Verifica se diretório existe
     * e é acessível.
     *
     * @param int $id
     */
    private function setTrabalho($id)
    {
        $this->trabalho = Trabalho::model()->findByPk((int)$id);

        if (is_null($this->trabalho)) {
            throw new Exception('Trabalho ' . $id . ' não encontrado.');
        }
        if (is_null($this->trabalho->sourceDir)) {
            throw new Exception('Diretório com imagens não definido no trabalho.');
        }
        if (!is_readable($this->trabalho->sourceDir)) {
            throw new Exception("Sem permissão de leitura no diretório {$this->trabalho->sourceDir}.");
        }
        if (!is_dir($this->trabalho->sourceDir)) {
            throw new Exception("Diretório de trabalho não existe ou sem permissão de acesso.");
        }
    }


}