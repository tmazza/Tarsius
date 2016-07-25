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
			array('status, pid, tempoDistribuicao', 'numerical', 'integerOnly'=>true),
			array('status','default','setOnEmpty'=>true,'value'=>0),
			array('nome, sourceDir, template', 'safe'),
			array('id, nome, sourceDir, status, pid, tempoDistribuicao', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
			'processos' => array(self::HAS_MANY, 'Processo', 'trabalho_id', 'order'=>'id DESC'),
			'distribuidos' => array(self::HAS_MANY, 'Distribuido', 'trabalho_id'),
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
			'sourceDir' => 'Source Dir',
			'status' => 'Status',
			'pid' => 'Pid',
			'tempoDistribuicao' => 'Tempo Distribuicao',
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

}
