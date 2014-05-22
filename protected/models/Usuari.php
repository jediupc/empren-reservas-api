<?php

/**
 * This is the model class for table "usuari".
 *
 * The followings are the available columns in table 'usuari':
 * @property string $id
 * @property string $nom_usuari
 * @property string $contrasenya
 * @property string $nom
 * @property string $cognoms
 * @property integer $es_admin
 *
 * The followings are the available model relations:
 * @property Espai[] $espais
 * @property Reserva[] $reservas
 */
class Usuari extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'usuari';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('es_admin', 'numerical', 'integerOnly'=>true),
			array('nom_usuari, nom', 'length', 'max'=>45),
			array('contrasenya', 'length', 'max'=>20),
			array('cognoms', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, nom_usuari, contrasenya, nom, cognoms, es_admin', 'safe', 'on'=>'search'),
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
			'espais' => array(self::MANY_MANY, 'Espai', 'permis(usuari_id, espai_id)'),
			'reservas' => array(self::HAS_MANY, 'Reserva', 'usuari_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nom_usuari' => 'Nom Usuari',
			'contrasenya' => 'Contrasenya',
			'nom' => 'Nom',
			'cognoms' => 'Cognoms',
			'es_admin' => 'Es Admin',
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
		$criteria->compare('nom_usuari',$this->nom_usuari,true);
		$criteria->compare('contrasenya',$this->contrasenya,true);
		$criteria->compare('nom',$this->nom,true);
		$criteria->compare('cognoms',$this->cognoms,true);
		$criteria->compare('es_admin',$this->es_admin);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Usuari the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
