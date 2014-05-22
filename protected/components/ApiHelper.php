<?php

class ApiHelper
{
  protected $model;
  protected $user;
  
  protected function __construct($model, $user) {
    $this->model = $model;
    $this->user = $user;
  }
  
  public static function makeHelper($model, $user) {
    switch ($model) {
      case 'espais':      return new EspaiHelper($model, $user);
      case 'usuaris':     return new UsuariHelper($model, $user);
      case 'reserves':    return new ReservaHelper($model, $user); 
      default:            return new ApiHelper($model, $user);
    }
  }
  
  public static function errorMsg($codError, $descError) {
    return array('codError'=>$codError, 'descError'=>$descError);
  }
  
  // ------------- Interficie
  
  public function actionList($params) {
    return array('codError'=>501, 'descError'=>'LIST: El model '.$this->model.' no està suportat');
  }
   
  public function actionView($id) {
    return array('codError'=>501, 'descError'=>'VIEW: El model '.$this->model.' no està suportat');
  }
  
  public function actionCreate($attrs) {
    return array('codError'=>501, 'descError'=>'CREATE: El model '.$this->model.' no està suportat');
  }
  
  public function actionUpdate($id, $attrs) {
    return array('codError'=>501, 'descError'=>'UPDATE: El model '.$this->model.' no està suportat');
  }
  
  public function actionDelete($id) {
    return array('codError'=>501, 'descError'=>'DELETE: El model '.$this->model.' no està suportat');
  }

}
