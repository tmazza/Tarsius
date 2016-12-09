<?php
/**
 */
class Erro extends CActiveRecord
{

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'erro';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('trabalho_id,texto,read', 'required'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Processo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Registra erro no trabalho
	 */
	public static function insertOne($trabalhoID, $text, $trace = false)
	{
		Yii::app()->db->createCommand()->insert('erro', array(
		    'trabalho_id' => $trabalhoID,
		  	'texto'		  => $text,	
		  	'trace'		  => $trace,	
		));
	}

}
