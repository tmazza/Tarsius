<?php
class Template extends CFormModel
{
	public $nome;
	public $file;

	public function rules()
	{
		return array(
			array('nome,file', 'require'),
		);
	}


}
