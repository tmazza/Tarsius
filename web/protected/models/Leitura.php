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

	public function getDbConnection() {
		return Yii::app()->dbExport;
	}

}
