<?php
/* @var $this CalendariController */

$this->breadcrumbs=array(
	'Calendari',
);
?>

<h1>
  <?php $espai = Espai::model()->findByPk($model->idEspai); 
  echo ($espai !== null) ? CHtml::encode($espai->descripcio) : ""; ?>
</h1>

<div class="form">
  <?php $form = $this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl('calendari/index'))); ?>
    <div class="row">
      <?php echo $form->labelEx($model, 'idEspai'); ?>
      <?php echo $form->dropDownList($model, 'idEspai', CHtml::listData(Espai::model()->findAll(), 'id', 'descripcio'), 
                                     array('prompt'=>'<Selecció>')); ?>
      <?php echo $form->error($model, 'idEspai'); ?>
    </div>
    <div class="row">
      <?php echo $form->labelEx($model, 'data'); ?>
      <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array('model'=>$model, 'attribute'=>'data', 'language'=>'ca',
                           'options'=>array('dateFormat'=>'dd/mm/yy', 'showOn'=>'button'))); ?>
      <?php echo $form->error($model, 'data'); ?>  
    </div>
		<div class="row buttons">
      <?php echo Chtml::submitButton('Consultar'); ?>
	  </div>
  <?php $this->endWidget(); ?>
</div><!-- form -->
<?php
  if (!empty($model->idEspai) && isset($model->data) && !Yii::app()->user->isGuest):
    echo $this->renderPartial('_calendari', array('model'=>$model));
  endif;
?>

