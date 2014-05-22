<?php

/**
 * This is the model class for table "espai".
 *
 * The followings are the available columns in table 'espai':
 * @property string $id
 * @property string $codi
 * @property string $descripcio
 *
 * The followings are the available model relations:
 * @property Usuari[] $usuaris
 * @property Reserva[] $reservas
 */
class Espai extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'espai';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('codi', 'length', 'max'=>20),
			array('descripcio', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, codi, descripcio', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'usuaris' => array(self::MANY_MANY, 'Usuari', 'permis(espai_id, usuari_id)'),
			'reservas' => array(self::HAS_MANY, 'Reserva', 'espai_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'codi' => 'Codi',
			'descripcio' => 'Descripcio',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('codi',$this->codi,true);
		$criteria->compare('descripcio',$this->descripcio,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Espai the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
