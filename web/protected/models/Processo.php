<?php

/**
 * This is the model class for table "processo".
 *
 * The followings are the available columns in table 'processo':
 * @property integer $id
 * @property integer $pid
 * @property integer $status
 * @property integer $trabalho_id
 * @property string $workDir
 * @property integer $qtd
 *
 * The followings are the available model relations:
 * @property Trabalho $trabalho
 */
class Processo extends CActiveRecord
{

	const StatusExecutando = 1;
	const StatusFinalizado = 2;
	const StatusParadaForcada = 3;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'processo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('pid, status, trabalho_id, qtd', 'numerical', 'integerOnly'=>true),
			array('workDir', 'safe'),
			array('id, pid, status, trabalho_id, workDir, qtd', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'trabalho' => array(self::BELONGS_TO, 'Trabalho', 'trabalho_id'),
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

	public static function insertOne($pid, $dirHash, $qtdArquivos, $trabId)
	{
        $model = new Processo();
        $model->status = 1;
        $model->pid = $pid;
        $model->trabalho_id = $trabId;
        $model->workDir = $dirHash;
        $model->qtd = $qtdArquivos;
        $model->dataInicio = time();
        if (!$model->save()) {
        	throw new Exception("Falha ao inserir registro de processo {$pid}.");
        }
	}
}
