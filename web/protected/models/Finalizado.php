<?php

/**
 * This is the model class for table "finalizado".
 *
 * The followings are the available columns in table 'distribuido':
 * @property integer $id
 * @property string $nome
 * @property integer $status
 * @property integer $trabalho_id
 * @property string $tempDir
 *
 * The followings are the available model relations:
 * @property Trabalho $trabalho
 */
class Finalizado extends CActiveRecord
{	

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'finalizado';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('trabalho_id', 'numerical', 'integerOnly'=>true),
			array('nome, dataFechamento, conteudo', 'safe'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'trabalho' => [self::BELONGS_TO,'Trabalho','trabalho_id'],
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
			'trabalho_id' => 'Trabalho',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Distribuido the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Insere um registro em finalizado.
	 *
	 * @param int
	 * @param string
	 * @param string
	 * @param bool
	 */
	public static function insertOne($trabalhoID, $fileName, $content, $exported)
	{
		$qtd = Yii::app()->db->createCommand()->insert('finalizado', array(
		    'trabalho_id'	 => $trabalhoID,
		  	'nome'			 => $fileName,	
		    'conteudo'		 => $content,
		    'exportado'		 => (int) $exported,
	  		'dataFechamento' => time(),	  
		));
		if ($qtd !== 1) {
			throw new Exception("Falha salvar registro de finalizado.");
		}
	}

	public function setAsExportado()
	{
      $this->exportado=1;
      $this->update(['exportado']);
	}

}
