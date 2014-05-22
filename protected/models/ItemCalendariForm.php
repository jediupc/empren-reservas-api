<?php

class ItemCalendariForm extends CFormModel
{
	public $seleccio;
	
	private $_reserva;
	
	public function rules()
	{
		return array(
			array('seleccio', 'safe')
		);
	}

	public function attributeLabels()
	{
		return array(
			'seleccio'=>''
		);
	}
	
	public function getReserva() {
    return $this->_reserva;
  }
  
  public function setReserva($reserva) {
    $this->_reserva = $reserva;
  }
}
