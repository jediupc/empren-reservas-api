<?php

class CalendariForm extends CFormModel
{
	public $idEspai;
	public $data; //string
	
	private $_items;
	private $_setmana;
	
	const K_INI_HORES = 8, K_FI_HORES = 20; //8h a 20h
	const K_INI_DIES = 1, K_FI_DIES = 5; //Dilluns a Divendres
  
  const K_DATE_MASK = 'd/m/Y';
  const K_BD_DATETIME = 'Y-m-d H:i:s'; 


	public function rules()
	{
		return array(
			array('idEspai', 'required'),
			array('data', 'date', 'format'=>'dd/MM/yyyy'),
			array('data', 'default', 'value'=>date(self::K_DATE_MASK))
		);
	}

	public function attributeLabels()
	{
		return array(
			'idEspai'=>'Espai',
		);
	}
	
	public function load() {
    $inici = self::stringToTime2($this->data);
    $this->_setmana = self::makeSetmana($inici);
    $this->makeItems();
    $reserves = $this->getReserves();
    foreach ($reserves as $reserva) {
      $time = self::stringToTime2($reserva->data_hora);
      $hora = (int) date('H', $time);
      $dia = (int) date('w', $time);
      $this->_items[$hora][$dia]->setReserva($reserva);
    }
  }
  
  public function save() {
    $inici = self::stringToTime2($this->data);
    $this->_setmana = self::makeSetmana($inici);
    $trans = Reserva::model()->dbConnection->beginTransaction();
    try {
      foreach($this->_items as $hora=>$dies)
        foreach ($dies as $dia=>$item) {
          if ($item->seleccio) { $this->saveReservaBd($dia, $hora); }  
      }
      $trans->commit();
    }
    catch (Exception $e) {
      $trans->rollback();
      Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
    }
  }
 
  public function getItems() {
    if ($this->_items === null) $this->makeItems();
    return $this->_items;
  }
  
  public function getSetmana() {
    $dies = array(1=>'Dilluns',2=>'Dimarts',3=>'Dimecres',4=>'Dijous',5=>'Divendres',6=>'Dissabte',0=>'Diumenge');
    $result = array();
    for ($i = self::K_INI_DIES; $i <= self::K_FI_DIES; $i++) {
      $result[$i] = $dies[$i] . "\n" . date(self::K_DATE_MASK, $this->_setmana[$i]); 
    }
    ksort($result);
    return $result;
  }
  
  private function getReserves() {
    $inici = reset($this->_setmana);
    $fi = end($this->_setmana);
    $inici = self::setHoraMinSeg($inici, 0, 0, 0);
    $fi = self::setHoraMinSeg($fi, 23, 59, 59);
    return $this->getReservesBd(date(self::K_BD_DATETIME, $inici), date(self::K_BD_DATETIME, $fi));
  }
  
  private function getReservesBd($dataInici, $dataFi) {
    $criteria = new CDbCriteria();
    $criteria->condition = 'espai_id=:idEspai';
    $criteria->params = array(':idEspai'=>$this->idEspai);
    $criteria->addBetweenCondition('data_hora', $dataInici, $dataFi);
    return Reserva::model()->findAll($criteria);
  }
  
  private function makeItems() {
    for ($i = self::K_INI_HORES; $i <= self::K_FI_HORES; $i++)
      for ($j = self::K_INI_DIES; $j <= self::K_FI_DIES; $j++) {
        $this->_items[$i][$j] = new ItemCalendariForm();
    }
  }
  
  private function saveReservaBd($dia, $hora) {
    $reserva = new Reserva();
    $reserva->data_hora = date(self::K_BD_DATETIME, self::setHoraMinSeg($this->_setmana[$dia], $hora));
    $reserva->espai_id = $this->idEspai;
    $reserva->usuari_id = Yii::app()->user->id; 
    $reserva->data_modif = new CDbExpression('NOW()');
    $reserva->save(); 
  }
  
  
  // ------- Funcions auxiliars sobre dates (timestamp)
  
  private static function makeSetmana($dataIni) {
    $dataActual = $dataIni; 
    for ($i = 0; $i < 7; $i++) {
      $diaActual = (int) date('w', $dataActual);
      $result[$diaActual] = $dataActual;
      $dataActual = self::getNextDate($dataActual);
    }
    asort($result);
    return $result; 
  }
    
  private static function getNextDate($data) {
    return strtotime(date('Y-m-d', $data) . ' +1 day');
  }
  
  private static function setHoraMinSeg($data, $hora, $min = 0, $seg = 0) {
    $result = date_create(date('Y-m-d', $data));
    date_time_set($result, $hora, $min, $seg);
    return $result->format('U'); 
  }
  
  private static function stringToTime2($strData) {
    return strtotime(str_replace('/', '-', $strData));
  }
  
}
