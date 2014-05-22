<?php

class EspaiHelper extends ApiHelper
{
  // --------- Interficie
  
  public function actionList($params) {
    $espais = $this->getEspais($params);
    $result = array();
    foreach ($espais as $espai) {
      $result[] = $espai->attributes; 
    }
    return $result;
  }
  
  public function actionView($id) {
    $espai = $this->getEspai($id);
    if ($espai !== null) return $espai->attributes;
    else return self::errorMsg(404, "VIEW: No existeix o no es pot accedir a l'espai amb id=$id");
  }
  
  public function actionCreate($attrs) {
    if (!$this->user->es_admin) return self::errorMsg(403, "CREATE: L'usuari no té permís per crear espais"); 
    $espai = new Espai();
    $espai->attributes = $attrs;
    $espai->insert();
    return array('id'=>$espai->id);
  }
  
  public function actionUpdate($id, $attrs) {
    if (!$this->user->es_admin) return self::errorMsg(403, "UPDATE: L'usuari no té permís per actualitzar espais");
    $espai = Espai::model()->findByPk($id);
    if ($espai !== null) {
      $espai->attributes = $attrs;
      $espai->update(); 
      return array('id'=>$espai->id);
    }
    else return self::errorMsg(404, "UPDATE: No existeix l'espai amb id=$id");
  }
  
  public function actionDelete($id) {
    if (!$this->user->es_admin) return self::errorMsg(403, "DELETE: L'usuari no té permís per esborrar espais");
    $espai = Espai::model()->findByPk($id);
    if ($espai !== null) {
      $espai->delete();
      return array('id'=>$espai->id);
    }
    else return self::errorMsg(404, "DELETE: No existeix l'espai amb id=$id"); 
  }
  
  // --------- Metodes auxiliars
  
  private function getEspais($params) {
    if ($this->user->es_admin) {
      $criteria = self::makeCriteria($params);
      return Espai::model()->findAll($criteria);
    }
    else {
      return $this->user->espais;
    }
  }
  
  private function getEspai($id) {
    if ($this->user->es_admin) {
      return Espai::model()->findByPk($id);
    }
    else {
      $result = null;
      foreach ($this->user->espais as $espai) {
        if ($espai->id === $id) $result = $espai;
      }
      return $result;
    }
  }
  
  private static function makeCriteria($params) {
    $conditions = array();
    $criteria = new CDbCriteria();
    if (isset($params['codi'])) {
      $conditions[] = 'codi LIKE :codi';
      $criteria->params[':codi'] = $params['codi'];  
    }
    if (isset($params['descripcio'])) { 
      $conditions[] = 'descripcio LIKE :descripcio';
      $criteria->params[':descripcio'] = $params['descripcio'];
    }
    $criteria->condition = implode(' AND ', $conditions);
    return $criteria;  
  }
  
}
