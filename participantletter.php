<?php

require_once 'participantletter.civix.php';
use CRM_Participantletter_ExtensionUtil as E;

function participantletter_civicrm_tabset($tabsetName, &$tabs, $context) {
  if ($tabsetName == 'civicrm/event/manage') {
    $eventId = $context['event_id'] ?? NULL;
    if (!empty($eventId)) {
      $eventSettings = CRM_Participantletter_Settings::getEventSettings($eventId);
      $tabs['participantletter'] = array(
        'title' => E::ts('Participant Letter'),
        // 'link' is automatically provided if we're under the 'civicrm/event/manage' path.
        'link' => NULL,
        // allows form to re-load itself on save.
        'class' => 'ajaxForm',
        'valid' => (bool) CRM_Utils_Array::value('is_participantletter', $eventSettings),
        'active' => TRUE,
        // setting this to FALSE prevents the tab from getting
        // focus when called directly, e.g., from under the
        // "Configure" link on the Manage Events listing page.
        'current' => TRUE,
      );
    }
    else {
      $tabs['participantletter'] = array(
        'title' => E::ts('Participant Letter'),
        'url' => 'civicrm/event/manage/participantletter',
        'field' => 'is_participantletter',
      );
    }
  }

  // on manage events listing screen, this section sets particpantletter tab in configuration popup as enabled/disabled.
  if ($tabsetName == 'civicrm/event/manage/rows' && CRM_Utils_Array::value('event_id', $context)) {
    if ($eventId = $context['event_id'] ?? NULL) {
      $eventSettings = CRM_Participantletter_Settings::getEventSettings($eventId);
      $tabs[$eventId]['is_participantletter'] = $eventSettings['is_participantletter'] ?? NULL;
    }
  }
}

function participantletter_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Participant' && $op == 'create') {
    $eventSettings = CRM_Participantletter_Settings::getEventSettings($objectRef->event_id);
    if (
      CRM_Utils_Array::value('is_participantletter', $eventSettings)
      && ($template_id = $eventSettings['template_id'] ?? NULL)
      && CRM_Participantletter_Utils::canSendEmail()
      && !($objectRef->is_test)
    ) {
      $params = array(
        'template_id' => $template_id,
        'contact_id' => $objectRef->contact_id,
      );
      try {
        civicrm_api3('email', 'send', $params);
        CRM_Core_Error::debug_log_message("Participantletter: Successfully sent email to participant_id: {$objectRef->id}, contact_id: {$objectRef->contact_id}, template_id: {$template_id}");
      }
      catch (CRM_Core_Exception $e) {
        CRM_Core_Error::debug_log_message("Participantletter: Could not send email to participant_id: {$objectRef->id}, contact_id: {$objectRef->contact_id}, template_id: {$template_id}; Email.send API error: " . $e->getMessage());
      }
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function participantletter_civicrm_config(&$config) {
  _participantletter_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function participantletter_civicrm_install() {
  _participantletter_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function participantletter_civicrm_enable() {
  _participantletter_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 */
// function participantletter_civicrm_preProcess($formName, &$form) {

// } // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
// function participantletter_civicrm_navigationMenu(&$menu) {
//   _participantletter_civix_insert_navigation_menu($menu, NULL, array(
//     'label' => E::ts('The Page'),
//     'name' => 'the_page',
//     'url' => 'civicrm/the-page',
//     'permission' => 'access CiviReport,access CiviContribute',
//     'operator' => 'OR',
//     'separator' => 0,
//   ));
//   _participantletter_civix_navigationMenu($menu);
// } // */
