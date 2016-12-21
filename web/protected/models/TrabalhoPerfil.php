 <?php

/**
 * This is the model class for table "trabalho_perfil".
 *
 * The followings are the available columns in table 'trabalho_perfil':
 * @property integer $id
 * @property string $descricao
 * @property integer $enableDebug
 * @property integer $threshold
 * @property integer $minArea
 * @property integer $maxArea
 * @property double $areaTolerance
 * @property double $minMatchObject
 * @property integer $maxExpansions
 * @property double $expasionRate
 * @property integer $searchArea
 * @property double $minMatchEllipse
 * @property integer $templateValidationTolerance
 * @property integer $dynamicPointReference
 *
 * The followings are the available model relations:
 * @property Trabalho[] $trabalhos
 */
class TrabalhoPerfil extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'trabalho_perfil';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('descricao, minMatchObject', 'required'),
            array('enableDebug, threshold, minArea, maxArea, maxExpansions, searchArea, templateValidationTolerance, dynamicPointReference', 'numerical', 'integerOnly'=>true),
            array('areaTolerance, minMatchObject, expasionRate, minMatchEllipse', 'numerical'),
            array('id, descricao, enableDebug, threshold, minArea, maxArea, areaTolerance, minMatchObject, maxExpansions, expasionRate, searchArea, minMatchEllipse, templateValidationTolerance, dynamicPointReference', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'trabalhos' => array(self::HAS_MANY, 'Trabalho', 'perfil_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'descricao' => 'Descrição',
            'enableDebug' => 'Habilitar depuração',
            'threshold' => 'Threshold',
            'minArea' => 'Área mínima',
            'maxArea' => 'Área máxima',
            'areaTolerance' => 'Tolerância área',
            'minMatchObject' => 'Igualdade mínima entre objetos',
            'maxExpansions' => 'Quantidade máxima de expansões',
            'expasionRate' => 'Taxa de expansão',
            'searchArea' => 'Search Area',
            'minMatchEllipse' => 'Taxa de preenchimento mínimo para elipses',
            'templateValidationTolerance' => 'Tolerância na validação do template',
            'dynamicPointReference' => 'Referência dinâmica nos pontos',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TrabalhoPerfil the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}