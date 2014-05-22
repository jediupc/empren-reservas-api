<div class="form">
  <?php $form = $this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl('calendari/reservar'))); ?>
  <table class>
    <tr>
      <th></th>
      <?php foreach ($model->getSetmana() as $dia): ?>
        <th scope="col"><?php echo nl2br(CHtml::encode($dia)); ?></th>
      <?php endforeach; ?>
    </tr>
    <?php foreach ($model->getItems() as $i=>$dies): ?>
    <tr>
      <th scope="row"><?php echo $i.'h'; ?></th>
      <?php foreach ($dies as $j=>$item): ?>
        <td>
          <?php
            $reserva = $item->getReserva();
            if ($reserva === null): 
              echo $form->checkBox($item, "[$i][$j]seleccio");
            elseif ($reserva->usuari_id !== Yii::app()->user->id):
              echo '(Reservat)';
            else:
              $id = 'anul_'.$i.'_'.$j;
              echo Chtml::ajaxLink('Anul·lar?', Yii::app()->createUrl('calendari/anular'), 
                                   array('type'=>'POST', 'data'=>array('idReserva'=>$reserva->id), 'replace'=>'#'.$id, 
                                   'error'=>'function(request, status, error) { alert(request.responseText); }'), 
                                   array('confirm'=>'Segur que vol anul·lar la reserva?', 'id'=>$id));
            endif; 
          ?>
        </td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </table>
   
  <?php if (!Yii::app()->user->isGuest): echo CHtml::submitButton('Reservar'); endif;?>
  <?php $this->endWidget(); ?>
  
</div><!-- form -->

