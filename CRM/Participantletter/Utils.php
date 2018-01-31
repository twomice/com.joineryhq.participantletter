<?php

class CRM_Participantletter_Utils {
  
  public static function canSendEmail() {
    $result = civicrm_api3('Email', 'getactions', array(
      'sequential' => 1,
    ));
    return in_array('send', $result['values']);
  }

}
