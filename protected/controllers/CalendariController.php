<?php

class CalendariController extends Controller
{
	public function actionIndex() 
  {
	   $model = new CalendariForm();
		 if (isset($_POST['CalendariForm'])) {
        $model->attributes = $_POST['CalendariForm'];
        if ($model->validate()) { 
          $model->load();
          Yii::app()->session['CalendariForm'] = $model->attributes;
        }
     }
    $this->render('vista', array('model'=>$model));
	}
	
	public function actionReservar() 
  {
	   $model = new CalendariForm();
	   $model->attributes = Yii::app()->session['CalendariForm'];
     if (isset($_POST['ItemCalendariForm'])) {
        $valid = true;
        foreach($model->getItems() as $i=>$dies) {
          foreach ($dies as $j=>$item) {
            if (isset($_POST['ItemCalendariForm'][$i][$j])) { 
              $item->attributes = $_POST['ItemCalendariForm'][$i][$j];
            } 
            $valid = $item->validate() && $valid;
          }
        }
        if ($valid) { $model->save(); }
     }
     $model->load();
     $this->render('vista', array('model'=>$model));       
  }
  
  public function actionAnular()
  {
    if (!Yii::app()->request->isAjaxRequest) return;
    if (isset($_POST['idReserva'])) {
      $ok = self::deleteReserva($_POST['idReserva']);
      if ($ok) {
        echo '(Anul·lat)';
        Yii::app()->end();
      }
      else throw new CHttpException(404, "No s'ha pogut anul·lar la reserva!"); 
    }
  }
  
  private static function deleteReserva($idReserva) {
    $trans = Reserva::model()->dbConnection->beginTransaction();
    try {
      // Comprovem que la reserva sigui de l'usuari actual per major seguretat
      $count = Reserva::model()->deleteByPk($idReserva, 'usuari_id=:idUsuari', array('idUsuari'=>Yii::app()->user->id));
      if ($count === 1) {
        $trans->commit();
        return true;
      }
      else {
        $trans->rollback();
        return false;
      }
    }
    catch (Exception $e) {
      $trans->rollback();
      Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
      return false;
    }
  }

}
