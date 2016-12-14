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

	const EXPORT_NONE = 0;
	const EXPORT_WAIT = 1;
	const EXPORT_MYSQL = 2;
	const EXPORT_HTTP = 3;
	const EXPORT_SQLSERVER = 4;

	public static $active = false;

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
			array('id, ativo, descricao, maxProcessosAtivos, maxAquivosProcessos, exportType, exportHost, exportDatabase, exportPort, exportTable, exportUser, exportPwd, exportUrl', 'safe'),
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
			'exportType' => 'Tipo banco de dados',
			'exportHost' => 'Endereço (host)',
			'exportDatabase' => 'Base de dados',
			'exportPort' => 'Porta',
			'exportTable' => 'Tabela',
			'exportUser' => 'Usuário',
			'exportPwd' => 'Senha',
			'exportUrl' => 'URL',
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

	/**
	 * Ativa configuração $id
	 *
	 * @return bool 
	 */
	public function makeActive()
	{
		self::model()->updateAll([
			'ativo' => 0,
		]);
		return self::model()->updateAll([
			'ativo' => 1,
		],[
			'condition' => 'id = ' . $this->id,
		]) == 1;

	}

	/**
	 * Retorna o perfil de confdiguração ativo.
	 *
	 * @throws Exception Caso nenhum perfil esteja ativo.
	 *
	 * @return CActiveRecord
	 */
	public static function getActive()
	{
		if (!self::$active) {
			self::$active = self::model()->find("ativo=1");
			if (is_null(self::$active)) {
				throw new Exception("Nenhum configuração ativa.");
			}
		}
		return self::$active;
	}

	public static function getTipos()
	{
		return [
			self::EXPORT_NONE => 'Desabilitada',
			self::EXPORT_WAIT => 'Pendente',
			self::EXPORT_MYSQL => 'Mysql',
			self::EXPORT_SQLSERVER => 'SQL Server',
			self::EXPORT_HTTP => 'Requisição HTTP/POST',
		];
	}

	public function isMySqlExport()
	{
		return $this->exportType == self::EXPORT_MYSQL;
	}

	public function isSqlServerExport()
	{
		return $this->exportType == self::EXPORT_SQLSERVER;
	}


	public function isExportEnable()
	{
		return $this->exportType != self::EXPORT_NONE && $this->exportType != self::EXPORT_WAIT;
	}
	
	public function isExportWating()
	{
		return $this->exportType == self::EXPORT_WAIT;
	}

	public function isHttpExport()
	{
		return $this->exportType == self::EXPORT_HTTP;
	}

}
