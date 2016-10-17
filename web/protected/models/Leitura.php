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
			['RespostasOriginais,Ausente,RespostasOriginais','safe'],
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
			'RespostasOriginais' => 'Respostas convertidas',
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
		return Yii::app()->dbExport;
	}

}
