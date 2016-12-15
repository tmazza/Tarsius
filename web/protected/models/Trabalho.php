<?php

/**
 * This is the model class for table "trabalho".
 *
 * The followings are the available columns in table 'trabalho':
 * @property integer $id
 * @property string $nome
 * @property string $sourceDir
 * @property integer $status
 * @property integer $pid
 * @property integer $tempoDistribuicao
 *
 * The followings are the available model relations:
 * @property Processo[] $processos
 * @property Distribuido[] $distribuidos
 */
class Trabalho extends CActiveRecord
{

    const statusParado = 0;
    const statusExecutando = 1;
    const statusFinalizado = 2;
    const statusDeveParar = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'trabalho';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('nome, sourceDir, tempoDistribuicao', 'required'),
            array('status, pid, tempoDistribuicao, perfil_id', 'numerical', 'integerOnly'=>true),
            array('status','default','setOnEmpty'=>true,'value'=>0),
            array('nome, sourceDir, template, export, urlImagens', 'safe'),
            array('id, nome, sourceDir, status, pid, tempoDistribuicao', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'processos' => array(self::HAS_MANY, 'Processo', 'trabalho_id', 'order'=>'id DESC', 'condition' => 'status != 2'),
            'distribuidos' => array(self::HAS_MANY, 'Distribuido', 'trabalho_id', 'select'=>'nome'),
            'perfil' => array(self::BELONGS_TO, 'TrabalhoPerfil', 'perfil_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'nome' => 'Nome',
            'sourceDir' => 'Diretório de trabalho',
            'status' => 'Status',
            'pid' => 'Pid',
            'tempoDistribuicao' => 'Tempo de distribuição',
            'urlImagens' => 'URL das imagens',
            'export' => 'Exportação',
            'perfil_id' => 'Configuração de processamento',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Trabalho the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function solicitaPausaProcessos()
    {
        return Processo::model()->updateAll([
            'status'=>self::statusDeveParar,
        ],"trabalho_id = {$this->id} and status = " . self::statusExecutando) > 0;
    }

    public function qtdProcessosAtivos()
    {
        return Processo::model()->count("trabalho_id = {$this->id} and status != " . self::statusFinalizado);
    }

    public function getLabelStatus(){
        switch ($this->status) {
            case 0: return 'Parado';
            case 1: return 'Distribuindo';
            case 2: return 'Parando...';
        }
    }

    public function getJaDistribuidos(){
        return array_map(function($i){
            return $i->nome;
        },$this->distribuidos);
    }

    public static function detailView($model){
        return [
            'sourceDir',
            'template',
            array(
                'label'=>'Status',
                'type'=>'raw',
                'value'=>$model->getLabelStatus(),
            ),
            array(
                'label'=>'Processo',
                'type'=>'raw',
                'value'=>is_null($model->pid) ? '<small>processo parado</small>' : $model->pid,
            ),
            array(
                'label'=>'Tempo de distribuição',
                'type'=>'raw',
                'value'=>$model->tempoDistribuicao . ' segundo(s)',
            ),
        ];
    }

    public function getNaoExportados(){
        return Distribuido::model()->count("trabalho_id={$this->id} AND exportado=0 AND output IS NOT NULL");
    }

    public function getFinalizados(){
        return new CActiveDataProvider('Distribuido', array(
            'criteria'=>array(
                'alias' => 'd',
                'select'=>'d.id,d.nome',
                'condition'=>'d.trabalho_id='.$this->id,
                'join'=>'JOIN finalizado f ON d.trabalho_id = f.trabalho_id AND f.nome = d.nome',
                // 'order'=>'?',
            ),
            'pagination'=>array(
                'pageSize'=>100,
            ),
        ));
    }

    public function setDistribuindo($status){
        $this->distribuindo = $status;
        $this->update(['distribuindo']);
    }


    /**
     * Exporta 1 arquivo
     *
     * @param int $id
     * @param CActiveRecord $modelFinalizado Registro de Finalizado
     * @param array $output Resultado do processamento
     */
    public static function export($id, $modelFinalizado, $output){
      try {

        $active = Configuracao::getActive();

        if ($modelFinalizado->exportado == 1) {
          throw new Exception("Arquivo '" . $modelFinalizado->nome . "' já exportado.");          
        }

        $trabalho = Trabalho::model()->findByPk((int) $id);

        if(is_null($trabalho)){
          throw new Exception("Trabalho {$id} não encontrado.");
        } else {

            # TODO: possibilitar configuração global
            $output['exportTime'] = date('Y-m-d H:i:s');
            $output['filename'] = pathinfo($modelFinalizado->nome, PATHINFO_FILENAME);

            $exportFileds = json_decode($trabalho->export, true);

            $exportContent = self::getExportContent($id, $output, $exportFileds);


            if ($active->isExportEnable()) {
                if (self::doExport($active, $exportContent)) {
                    $modelFinalizado->setAsExportado();
                }
            } else if ($active->isExportWating()) {
                throw new Exception("Exportação pendente. Defina como o arquivo deve ser exportado ou desabilite a exportação.");
            } else {
                $modelFinalizado->setAsExportado();
            }
        }

        return true;

      } catch(Exception $e) {

        Erro::insertOne($id, $e->getMessage(), $e->__toString());
        return $e->getMessage();

      }
    }


    /**
     * Aplica exportação de acordo com o tipo definido.
     */
    public static function doExport(&$active, &$exportContent)
    {
        if ($active->isHttpExport()) {

            $url = $active->exportUrl;
            $data = ['data'=>json_encode($exportContent)];

            $ch = curl_init();
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_HEADER => false,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_RETURNTRANSFER => true,
            ];
            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);
            $return = curl_getinfo($ch);

            curl_close($ch);

            $code = isset($return['http_code']) ? $return['http_code'] : 500;

            if ($code == 201) {
                return true;
            } else {
                throw new Exception("Falha ao exportar registro: " . $response);
            }

        } else {
            $active = Configuracao::getActive();
            $table = $active->exportTable;
            return Export::db()->createCommand()->insert($table, $exportContent) == 1;
        }
    }


    /**
     * Retorna array com chaves sendo o nome do atribuito definidio na exportação
     * do trabalho e valor interpretado da região.
     */
    public static function getExportContent($id, &$result, &$export)
    { 
        $exportContent = [];
        foreach ($export as $targetColumn => $outputID) {
          if (isset($result[$outputID])) {
            $exportContent[$targetColumn] = $result[$outputID];
          } else {
            Erro::insertOne($id, "Região {$outputID} definida para exportação, mas não encontrada no resultado do processamento.", is_null($result) ? 'SIM' : 'NAO');
          }
        }
        return $exportContent;
    }

}
