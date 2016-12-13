<?php

/**
 * This is the model class for table "configuracao".
 *
 * The followings are the available columns in table 'configuracao':
 * @property integer $id
 * @property integer $ativo
 * @property string $descricao
 * @property integer $maxProcessosAtivos
 * @property integer $maxAquivosProcessos
 */
class Configuracao extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'configuracao';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('descricao', 'required'),
			array('ativo, maxProcessosAtivos, maxAquivosProcessos', 'numerical', 'integerOnly'=>true),
			array('id, ativo, descricao, maxProcessosAtivos, maxAquivosProcessos', 'safe', 'on'=>'search'),
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
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ativo' => 'Ativo',
			'descricao' => 'Descrição',
			'maxProcessosAtivos' => 'Limite de processo ativos',
			'maxAquivosProcessos' => 'Limite de arquivos por processo',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Configuracao the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
