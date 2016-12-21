<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

/**
 * Gerência de um trabalho.
 */
class TrabalhoController extends BaseController
{

    /**
     * Lista trabalhos
     */
    public function actionIndex()
    {
        $trabalhos = Trabalho::model()->findAll([
            'order' => 'id DESC',
        ]);
        $this->render('index',[
          'trabalhos'=>$trabalhos,
        ]);
    }

    /**
     * Criação de novo trabalho
     */
    public function actionNovo()
    {
        $model = new Trabalho();
        $model->export = json_encode([], true);

        if(isset($_POST['Trabalho'])){
            $model->attributes = $_POST['Trabalho'];

            $export = is_array($model->export) ? $model->export : [];
            if (isset($_POST['export']) && is_array($_POST['export'])) {
              $keys = array_column($_POST['export'], 'a');
              $values = array_column($_POST['export'], 'b');
              $export = array_combine($keys, $values);
            }
            $model->export = json_encode($export);

            if($model->validate()){
                $model->save();
                $this->redirect($this->createUrl('/trabalho/index'));
            }
        }
        
        $model->export = json_decode($model->export, true);

        $this->render('form',[
            'model'=>$model,
            'templates' => $this->getTemplate(),
            'perfis' => CHtml::listData(TrabalhoPerfil::model()->findAll(['order'=>'descricao ASC']), 'id', 'descricao'),
        ]);
    }

    /**
     * Visualização de um trabalho
     */
    public function actionVer($id)
    {
        $this->render('ver',$this->getInfoTrabalho($id));
    }

    /**
     * Edição das configurações do trabalho
     */
    public function actionEditar($id){
        $model = Trabalho::model()->findByPk((int)$id);
        $model->export = json_decode($model->export, true);

        if(isset($_POST['Trabalho'])){
            $model->attributes = $_POST['Trabalho'];


            $export = is_array($model->export) ? $model->export : [];
            if (isset($_POST['export']) && is_array($_POST['export'])) {
              $keys = array_column($_POST['export'], 'a');
              $values = array_column($_POST['export'], 'b');
              $export = array_combine($keys, $values);
            }
            $model->export = json_encode($export);

            if($model->validate()){
                $model->save();
                $this->redirect($this->createUrl('/trabalho/index'));
            } else {
              $model->export = json_decode($model->export, true);
            }
        } 


        $this->render('form',[
            'perfis' => CHtml::listData(TrabalhoPerfil::model()->findAll(['order'=>'descricao ASC']), 'id', 'descricao'),
            'model'=>$model,
            'templates' => $this->getTemplate(),
        ]);
    }

    /**
     * Lista todas as imagens não exportadas
     */
    public function actionNaoDistribuidas($id,$pageSize=8){
        Yii::app()->clientScript->registerScriptFile($this->wb.'/jquery.elevatezoom.min.js');
        $model = Trabalho::model()->findByPk((int)$id);

        $criteria=new CDbCriteria([
            'alias' => 'd',
            'with' => [
                'resultado' => [
                    'alias'=>'f',
                    'condition' => 'f.exportado=0',
                ],
            ],
            //'join'=>'JOIN finalizado f ON f.trabalho_id = d.trabalho_id AND f.nome = f.nome',
            'condition'=>"d.trabalho_id={$model->id} AND status !=  " . Distribuido::StatusReprocessamento,
            'order'=>'d.id DESC',
            // 'limit'=>30,
        ]);
        
        $count=Distribuido::model()->count($criteria);
        $pages=new CPagination($count);

        $pages->pageSize=$pageSize;
        $pages->applyLimit($criteria);
        $models=Distribuido::model()->findAll($criteria);

        $this->render('naoDistribuidas',[
            'trabalho'=>$model,
            'naoDistribuidas'=>$models,
            'pages'=>$pages,
        ]);
    }

    /**
     * Retorna informações do trabalho, usada ao mostrar um trabalaho
     * e para atualizaçõa por AJAX do conteúdo da página
     */
    private function getInfoTrabalho($id)
    {       
        $trabalho = Trabalho::model()->findByPk((int)$id);

        $qtdDistribuida = Yii::app()->db->createCommand()
                ->select('count(*)')
                ->from('distribuido')
                ->where('trabalho_id = ' . $id . ' AND status = 1')
                ->queryColumn();

        $qtdFinalizada = Yii::app()->db->createCommand()
                ->select('count(*)')
                ->from('finalizado')
                ->where('trabalho_id = ' . $id)
                ->queryColumn();

        $processosAtivos = Yii::app()->db->createCommand()
                ->select('*')
                ->from('processo')
                ->where('trabalho_id = ' . $id . ' AND status=1')
                ->queryAll();

        $naoExportadas = Yii::app()->db->createCommand()
                ->select('count(*)')
                ->from('finalizado')
                ->where('trabalho_id = ' . $id . ' AND exportado=0')
                ->queryColumn();

        return [
            'trabalho' => $trabalho,
            'distribuido' => array_shift($qtdDistribuida),
            'processado' => array_shift($qtdFinalizada),
            'processosAtivos' => $processosAtivos,
            'naoExportadas' => array_shift($naoExportadas),
            'erros' => Erro::model()->findAll("trabalho_id = $id"),
         ];
    }

    /**
     * Inicia distribuição de do trabalho
     */
    public function actionIniciar($id)
    {
        $trabalho = Trabalho::model()->findByPk((int)$id);
        $trabalho->status = 1;

        $cmd = 'php ' . Yii::getPathOfAlias('application') . '/tarsius distribui --trabId=' . $trabalho->id;
        $trabalho->pid = exec($cmd . ' > /dev/null 2>&1 & echo $!;');

        $trabalho->update(['status', 'pid']);

        $this->redirect($this->createUrl('/trabalho/ver',[
            'id'=>$trabalho->id,
        ]));
    }

    /**
     * Pausa distribuição de do trabalho
     */
    public function actionPausar($id){
        $trabalho = Trabalho::model()->findByPk((int)$id);
        $trabalho->status = 2;
        $trabalho->update(['status']);
        $this->redirect($this->createUrl('/trabalho/ver',['id'=>$trabalho->id]));
    }


    /**
     * Excluir um trabalho. Eliminando os registro de finalizado, distribuido
     * e exclui diretorio do trabalho em runtimeDir
     */
    public function actionExcluir($id)
    {
        $trabalho = Trabalho::model()->findByPk((int)$id);
        $trabalho->solicitaPausaProcessos();

        // Aguarda até que todos os arquivos sejam devolvidos para sourceDir
        while($trabalho->qtdProcessosAtivos() > 0){
            sleep(1);
        }

        # apaga registros
        Processo::model()->deleteAll("trabalho_id = {$trabalho->id}");
        Distribuido::model()->deleteAll("trabalho_id = {$trabalho->id}");
        Finalizado::model()->deleteAll("trabalho_id = {$trabalho->id}");

        # apaga arquivos de imagens de resultado de imagem processada
        $dir = Yii::getPathOfAlias('webroot') . '/../data/runtime/trab-' . $trabalho->id;
        CFileHelper::removeDirectory($dir);

        $this->redirect($this->createUrl('/trabalho/ver',['id'=>$trabalho->id]));

    }

    /**
     * Atualização AJAX das informações de 1 trabalho
     */ 
    public function actionUpdateVer($id){
        $this->renderPartial('_ver',$this->getInfoTrabalho($id));
    }

    /**
     * Lista imagens que foram exportadas
     */
    public function actionFinalizadas($id)
    {
        $this->render('finalizadas',[
            'trabalho'=>Trabalho::model()->findByPk((int)$id),
        ]);     
    }

    /**
     * Atualiza o status do trabalho para aguardando idependentemente do estado
     * dos processos.
     */
    public function actionForcaParada($id)
    {
        $trabalho = Trabalho::model()->findByPk((int)$id);
        $trabalho->status = Trabalho::statusParado;
        $trabalho->distribuindo = 0;
        $trabalho->update(['status','distribuindo']);
        $this->redirect($this->createUrl('/trabalho/ver',['id'=>$trabalho->id]));
    }

    /** 
     * Aciona comando de distribuição
     */
    private function runDistribui($trabalho)
    {
        $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);
        $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
        $runner->addCommands($commandPath);
        $args = array('yiic', 'distribui', '--trabId='.$trabalho->id);
        $runner->run($args);
    }

    /**
     * Tenta exportar connjunto de imagens não exportadas.
     */
    public function actionExportaResultado($id)
    {
        $finalizadas = Finalizado::model()->findAll([
          'condition'=>"trabalho_id=$id AND exportado=0 AND conteudo IS NOT NULL",
          'limit'=>512,
        ]);
        foreach ($finalizadas as $f) {
           $conteudo = json_decode($f->conteudo,true);
           if(isset($conteudo['result'])) {
            Trabalho::export($id, $f, $conteudo['result']);
          }
        }
        $qtd = count($finalizadas);
        HView::fMsg($qtd . ' ' . HView::plural('exportado',$qtd));
        $this->redirect($this->createUrl('/trabalho/ver',[
            'id'=>$id,
        ]));
    }
    
    /**
     * Exporta um arquivo.
     */
    public function actionForcaExport($id)
    {
        $model = Distribuido::model()->findByPk((int)$id);
        $output = json_decode($model->resultado->conteudo,true);
        if(isset($output['result'])) {
            $export = Trabalho::export($model->trabalho_id, $model->resultado,$output['result']);
            if($export === true){
              HView::fMsg('Export realizado.');
            } else {
              HView::fMsg($export);

            }
        } else {
            HView::fMsg('Valores do resultado do processamento não encontrados..');
        }
        $this->redirect($this->createUrl('/trabalho/naoDistribuidas',[
            'id'=>$model->trabalho_id,
        ]));
    }


    /**
     * Lista todos os erros que ocorrem no trabalho (erros gerados por DistribuiCommand e 
     * ProcessaCommand)
     */
    public function actionVerErros($id)
    {
        $erros = Erro::model()->findAll("trabalho_id = $id");
        $this->render('erros',[
            'erros' => $erros,
            'id' => $id,
        ]);
    }

    /**
     * Descarta um erro
     */
    public function actionDeleteErro($id)
    {
        $model = Erro::model()->findByPk((int) $id);
        $trabId = $model->trabalho_id;
        $model->delete();
        $this->redirect($this->createUrl('/trabalho/verErros',[
            'id' => $trabId,
        ]));
    }

    /**
     * Descarta todos os erros que tenho exatamente a mesma mensagem do 
     * erro informado em $id
     */
    public function actionDeleteAllErro($id)
    {
        $model = Erro::model()->findByPk((int) $id);
        $trabId = $model->trabalho_id;

        Erro::model()->deleteAll("texto = '{$model->texto}'");

        $this->redirect($this->createUrl('/trabalho/verErros',[
            'id' => $trabId,
        ]));
    }


    public function actionCancelaProcesso($id)
    {
        $processo = Processo::model()->findByPk((int) $id);
        $dir = $processo->workDir;

        $data = Yii::app()->db->createCommand()
            ->select('d.id, d.nome')
            ->from('distribuido d')
            ->leftJoin('finalizado f', 'f.nome=d.nome and f.trabalho_id=f.trabalho_id')
            ->where("tempDir = '{$dir}' and f.exportado IS NULL")
            ->queryAll();


        $dirIn = Yii::app()->params['runtimeDir'] . '/trab-' . $processo->trabalho->id . '/exec/ready/'. $dir .'/';
        if (substr($processo->trabalho->sourceDir, -1) != '/') {
            $processo->trabalho->sourceDir .= '/';
        }   
        $dirOut = $processo->trabalho->sourceDir;
        $ok = true;
        foreach ($data as $d) {
            if (rename($dirIn.$d['nome'], $dirOut.$d['nome'])) {
               Distribuido::model()->deleteByPk((int) $d['id']);
            } else {
                $ok = false;
            }
        }

        if ($ok) {
            $processo->status = Processo::StatusParadaForcada;
            $processo->update(['status']);
            HView::fMsg('Todas as imagens foram redistribuídas.');
        } else {
            HView::fMsg('Algumas imagens não foram redistribuídas, acesso o diretório de trabalho para confirmar.');
        }
        $this->redirect($this->createUrl('/trabalho/ver',[
            'id' => $processo->trabalho->id,
        ]));
    }

    

}
