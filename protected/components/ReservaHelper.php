<?php

class ReservaHelper extends ApiHelper
{
  // --------- Interficie
  
  public function actionList($params) {
    $reserves = $this->getReserves($params);
    $result = array();
    foreach ($reserves as $reserva) {
      $attrs = $reserva->attributes;
      $attrs['espai_id'] = $reserva->espai->attributes;
      $attrs['usuari_id'] = ($this->user->es_admin) ? $reserva->usuari->attributes : null;
      $result[] = $attrs; 
    }
    return $result;
  }
  
  public function actionView($id) {
    $reserva = Reserva::model()->with('espai')->findByPk($id);
    if ($reserva !== null) {
      $attrs = $reserva->attributes;
      $attrs['espai_id'] = $reserva->espai->attributes;
      $attrs['usuari_id'] = ($this->user->es_admin) ? $reserva->usuari->attributes : null;
      return $attrs;
    }
    else return self::errorMsg(404, "VIEW: No existeix la reserva amb id=$id");
  }
  
  public function actionCreate($attrs) { 
    $reserva = new Reserva();
    $reserva->attributes = $attrs;
    $reserva->data_hora = self::formatDate($reserva->data_hora);
    if (!$this->user->es_admin || !isset($reserva->usuari_id)) $reserva->usuari_id = $this->user->id; 
    $reserva->data_modif = new CDbExpression('NOW()');
    $reserva->insert();
    return array('id'=>$reserva->id);
  }
  
  public function actionUpdate($id, $attrs) {
    if (!$this->user->es_admin) return self::errorMsg(403, "UPDATE: L'usuari no té permís per actualitzar reserves");
    $reserva = Reserva::model()->findByPk($id);
    if ($reserva !== null) {
      $reserva->attributes = $attrs;
      $reserva->data_hora = self::formatDate($reserva->data_hora);
      $reserva->data_modif = new CDbExpression('NOW()');
      $reserva->update(); 
      return array('id'=>$reserva->id);
    }
    else return self::errorMsg(404, "UPDATE: No existeix la reserva amb id=$id");
  }
  
  public function actionDelete($id) {
    $reserva = Reserva::model()->findByPk($id);
    if ($reserva !== null) {
      if ($this->user->es_admin || $this->user->id === $reserva->usuari_id) { 
        $reserva->delete();
        return array('id'=>$reserva->id);
      }
      else return self::errorMsg(403, "DELETE: L'usuari no té permís per esborrar la reserva amb id=$id");
    }
    else return self::errorMsg(404, "DELETE: No existeix la reserva amb id=$id"); 
  }
  
  // --------- Metodes auxiliars
  
  private function getReserves($params) {
    $conditions = array();
    $criteria = new CDbCriteria();
    if (isset($params['espai'])) {
      $conditions[] = 'espai_id = :espai_id';
      $criteria->params[':espai_id'] = $params['espai'];  
    }
    if (isset($params['usuari'])) { 
      $conditions[] = 'usuari_id = :usuari_id';
      $criteria->params[':usuari_id'] = $params['usuari'];
    }
    if (isset($params['inici']) && isset($params['fi'])) {
      $dataInici = self::formatDate($params['inici']);
      $dataFi = self::formatDate($params['fi']);
      //$criteria->addBetweenCondition('data_hora', $dataInici, $dataFi);
      $conditions[] = 'data_hora BETWEEN :data_inici AND :data_fi';
      $criteria->params[':data_inici'] = $dataInici;
      $criteria->params[':data_fi'] = $dataFi;
    }
    else if (isset($params['inici'])) {
      $dataInici = self::formatDate($params['inici']);
      $conditions[] = 'data_hora >= :data_inici';
      $criteria->params[':data_inici'] = $dataInici;
    }
    else if (isset($params['fi'])) {
      $dataFi = self::formatDate($params['fi']); 
      $conditions[] = 'data_hora <= :data_fi';
      $criteria->params[':data_fi'] = $dataFi;  
    }
    $criteria->condition = implode(' AND ', $conditions);
    return Reserva::model()->with('espai')->findAll($criteria);
  }
  
  
  private static function formatDate($strDate) { //yyyymmddhh
    if (strlen($strDate) < strlen('yyyymmddhh')) return '';
    $year = substr($strDate, 0, 4); //yyyy
    $month = substr($strDate, 4, 2); //mm
    $day = substr($strDate, 6, 2); //dd
    $hour = substr($strDate, 8, 2); //hh
    return $year.'-'.$month.'-'.$day.' '.$hour.':00:00'; //yyyy-mm-dd hh:mi:ss (MySQL)
  }
  
}
