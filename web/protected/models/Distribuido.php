<?php

/**
 * This is the model class for table "distribuido".
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
class Distribuido extends CActiveRecord
{	

	const StatusFechado = 2;
	const StatusParado = 3;
	const StatusAguardando = 1;
	const StatusReprocessamento = 4;
	const StatusDescartado = 5;


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'distribuido';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('status, trabalho_id', 'numerical', 'integerOnly'=>true),
			array('nome, tempDir,dataDistribuicao,dataFechamento', 'safe'),
			array('id, nome, status, trabalho_id, tempDir', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'trabalho' => array(self::BELONGS_TO, 'Trabalho', 'trabalho_id'),
			'resultado' => [self::BELONGS_TO,'Finalizado',['nome'=>'nome','trabalho_id'=>'trabalho_id']],
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
			'status' => 'Status',
			'trabalho_id' => 'Trabalho',
			'tempDir' => 'Temp Dir',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('nome',$this->nome,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('trabalho_id',$this->trabalho_id);
		$criteria->compare('tempDir',$this->tempDir,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
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
	 * Insere um novo registro
	 */
	public static function insertOne($trabID, $filename, $dir)
	{
        $model = new Distribuido();
        $model->nome = $filename;
        $model->status = self::StatusAguardando;
        $model->trabalho_id = $trabID;
        $model->tempDir = $dir;
        $model->dataDistribuicao = time();
        if(!$model->save()) {
        	throw new Exception("Falha ao inserir registro {$file} em distribuído.");
        }
	}



    public function descartar()
    {
        $this->status = self::StatusDescartado;
        $this->update(['status']);
    }



}
