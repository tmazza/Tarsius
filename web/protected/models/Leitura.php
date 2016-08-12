<?php
class Leitura extends CActiveRecord {


	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'CONCURSOS.dbo.LEITURA';
	}

	public static function getTableName() {
		return 'CONCURSOS.dbo.LEITURA';
	}

	public function primaryKey() {
		return 'IdLeitura';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('NrFolha, NrParte, Local, Ordem, NumeroVirgem, FolhaIdentificada', 'default', 'value' => NULL, 'setOnEmpty' => true, 'message' => 'default'),
			array('NrFolha, NrParte, Local, Situacao, FolhaIdentificada', 'numerical', 'message' => 'numerical'),
			array('Anulada, ForaLocal, Ausente, UsoVirgem', 'in', 'range' => array('S', 'N'), 'message' => 'range'),
			array('Anulada, ForaLocal, Ausente, UsoVirgem', 'default', 'value' => 'N', 'setOnEmpty' => true, 'message' => 'default'),
			array('FolhaIdentificada', 'in', 'range' => array(0, 1), 'message' => 'range'),
			array('RespostasOriginais, RespostasConvertidas, Situacao, NrFolha, NrParte, Local', 'safe'),
			array('IdLeitura, Ordem, Concurso, TipoFolha, NomeArquivo, RespostasOriginais, RespostasConvertidas, Situacao, Ausente, AlinhamentoEsquerda, AlinhamentoDireita, ImagemInterna, CodigoBarras, FolhaIdentificada', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'IdLeitura' => 'ID',
			'Concurso' => 'Concurso',
			'TipoFolha' => 'Tipo da folha',
			'NomeArquivo' => 'Nome do arquivo',
			'Ordem' => 'Ordem',
			'Inscricao' => 'Inscrição',
			'RespostasOriginais' => 'Respostas originais',
			'RespostasConvertidas' => 'Respostas convertidas',
			'Situacao' => 'Situação',
			'Ausente' => 'Ausente',
			'Anulada' => 'Anulada',
			'ForaLocal' => 'Fora de local',
			'UsoVirgem' => 'Uso de folha virgem',
			'AlinhamentoEsquerda' => 'Alinhamento esquerda',
			'AlinhamentoDireita' => 'Alinhamento direita',
			'ImagemInterna' => 'Imagem interna do TeleForm',
			'CodigoBarras' => 'Codigo de barras',
			'FolhaIdentificada' => 'Folha identificada',
			'Local' => 'Local',
			'NrFolha' => 'Número da folha',
			'NrParte' => 'Número da parte',
			'qtd' => 'Quantidade',
		);
	}

	public function getDbConnection() {
		return $db=Yii::app()->dbExport;
	    if(self::$db!==null)
	        return self::$db;
	    else
	    {
	        if(self::$db instanceof CDbConnection)
	            return self::$db;
	        else
	            throw new CDbException(Yii::t('yii','Active Record requires a "dbExport" CDbConnection application component.'));
	    }
	}

}
