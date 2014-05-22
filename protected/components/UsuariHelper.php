<?php

class UsuariHelper extends ApiHelper
{
  // --------- Interficie
  
  public function actionList($params) {
    $usuaris = $this->getUsuaris($params);
    $result = array();
    foreach ($usuaris as $usuari) {
      $result[] = $usuari->attributes; 
    }
    return $result;
  }
  
  public function actionView($id) {
    $usuari = $this->getUsuari($id);
    if ($usuari !== null) return $usuari->attributes;
    else return self::errorMsg(404, "VIEW: No existeix o no es pot accedir a l'usuari amb id=$id");
  }
  
  public function actionCreate($attrs) {
    if (!$this->user->es_admin) return self::errorMsg(403, "CREATE: L'usuari no té permís per crear usuaris"); 
    $usuari = new Usuari();
    $usuari->attributes = $attrs;
    $usuari->insert();
    return array('id'=>$usuari->id);
  }
  
  public function actionUpdate($id, $attrs) {
    if (!$this->user->es_admin) return self::errorMsg(403, "UPDATE: L'usuari no té permís per actualitzar usuaris");
    $usuari = Usuari::model()->findByPk($id);
    if ($usuari !== null) {
      $usuari->attributes = $attrs;
      $usuari->update(); 
      return array('id'=>$usuari->id);
    }
    else return self::errorMsg(404, "UPDATE: No existeix l'usuari amb id=$id");
  }
  
  public function actionDelete($id) {
    if (!$this->user->es_admin) return self::errorMsg(403, "DELETE: L'usuari no té permís per esborrar usuaris");
    $usuari = Usuari::model()->findByPk($id);
    if ($usuari !== null) {
      $usuari->delete();
      return array('id'=>$usuari->id);
    }
    else return self::errorMsg(404, "DELETE: No existeix l'usuari amb id=$id"); 
  }
  
  // --------- Metodes auxiliars
  
  private function getUsuaris($params) {
    if ($this->user->es_admin) {
      $criteria = self::makeCriteria($params);
      return Usuari::model()->findAll($criteria);
    }
    else {
      return array($this->user);
    }
  }
  
  private function getUsuari($id) {
    if ($this->user->es_admin) {
      return Usuari::model()->findByPk($id);
    }
    else {
      return ($this->user->id === $id) ? $this->user : null;
    }
  }
  
  private static function makeCriteria($params) {
    $conditions = array();
    $criteria = new CDbCriteria();
    if (isset($params['nom_usuari'])) {
      $conditions[] = 'nom_usuari LIKE :nom_usuari';
      $criteria->params[':nom_usuari'] = $params['nom_usuari'];  
    }
    if (isset($params['nom'])) {
      $conditions[] = 'nom LIKE :nom';
      $criteria->params[':nom'] = $params['nom'];  
    }
    if (isset($params['cognoms'])) { 
      $conditions[] = 'cognoms LIKE :cognoms';
      $criteria->params[':cognoms'] = $params['cognoms'];
    }
    if (isset($params['es_admin'])) { 
      $conditions[] = 'es_admin = :es_admin';
      $criteria->params[':es_admin'] = $params['es_admin'];
    }
    $criteria->condition = implode(' AND ', $conditions);
    return $criteria;
  }
  
}
