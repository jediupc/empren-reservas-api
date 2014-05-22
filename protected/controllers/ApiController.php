<?php

class ApiController extends Controller
{
    public function actionList()
    {
      try {
        $user = $this->authenticate();
        if ($user !== null) {
          $helper = ApiHelper::makeHelper($_GET['model'], $user);
          $result = $helper->actionList($_GET);
          $this->sendResponse($result);
        }
      }
      catch (Exception $e) {
        Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        $this->sendResponse(ApiHelper::errorMsg(500, 'Error intern al servidor. Veure application.log'));
      }
    }
    
    public function actionView()
    {
      try {
        $user = $this->authenticate();
        if ($user !== null) {
          $helper = ApiHelper::makeHelper($_GET['model'], $user);
          $result = $helper->actionView($_GET['id']);
          $this->sendResponse($result);
        }
      }
      catch (Exception $e) {
        Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        $this->sendResponse(ApiHelper::errorMsg(500, 'Error intern al servidor. Veure application.log'));
      }
    }
    
    public function actionCreate()
    {
      try {
        $user = $this->authenticate();
        if ($user !== null) {
          $helper = ApiHelper::makeHelper($_GET['model'], $user);
          $result = $helper->actionCreate($_POST);
          $this->sendResponse($result);
        }
      }
      catch (Exception $e) {
        Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        $this->sendResponse(ApiHelper::errorMsg(500, 'Error intern al servidor. Veure application.log'));
      }
    }
    
    public function actionUpdate()
    {
      try {
        $user = $this->authenticate();
        if ($user !== null) {
          $put = CJSON::decode(file_get_contents('php://input'), true);
          $helper = ApiHelper::makeHelper($_GET['model'], $user);
          $result = $helper->actionUpdate($_GET['id'], $put);
          $this->sendResponse($result);
        }
      }
      catch (Exception $e) {
        Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        $this->sendResponse(ApiHelper::errorMsg(500, 'Error intern al servidor. Veure application.log'));
      }
    }
    
    public function actionDelete()
    {
      try {
        $user = $this->authenticate();
        if ($user !== null) {
          $helper = ApiHelper::makeHelper($_GET['model'], $user);
          $result = $helper->actionDelete($_GET['id']);
          $this->sendResponse($result);    
        }
      }
      catch (Exception $e) {
        Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        $this->sendResponse(ApiHelper::errorMsg(500, 'Error intern al servidor. Veure application.log'));
      }
    }
    
    
    private function authenticate() {
      if (!isset($_SERVER['HTTP_X_USERNAME']) || !isset($_SERVER['HTTP_X_PASSWORD'])) {
        $this->sendResponse(ApiHelper::errorMsg(401, 'Falta nom usuari i/o constrasenya'));
        return null;
      }
      else {
        $username = $_SERVER['HTTP_X_USERNAME'];
        $password = $_SERVER['HTTP_X_PASSWORD'];
        $usuari = Usuari::model()->findByAttributes(array('nom_usuari'=>$username));
        if ($usuari === null) {
          $this->sendResponse(ApiHelper::errorMsg(401, 'Nom usuari i/o constrasenya és incorrecte'));
          return null;
        }
        else if ($usuari->contrasenya !== $password) {
          $this->sendResponse(ApiHelper::errorMsg(401, 'Nom usuari i/o constrasenya és incorrecte'));
          return null;
        }
        else {
          return $usuari;  
        }
      }
    }
    
    private function sendResponse($result) {
      $isError = isset($result['codError']);
      if ($isError) {
        $statusHeader = 'HTTP/1.1 '.$result['codError'].' '.self::getStatusMsg($result['codError']);
        header($statusHeader);
      }
      header('Content-type: application/json');
      echo CJSON::encode($result);
    }
    
    private static function getStatusMsg($codigo) {
      $lista = array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
      );
      return (isset($lista[$codigo])) ? $lista[$codigo] : '';
    }
}
