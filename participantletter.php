<?php

require_once 'participantletter.civix.php';
use CRM_Participantletter_ExtensionUtil as E;

function participantletter_civicrm_tabset($tabsetName, &$tabs, $context) {
  if ($tabsetName == 'civicrm/event/manage') {
    $eventId = CRM_Utils_Array::value('event_id', $context);
    if (!empty($eventId)) {
      $eventSettings = CRM_Participantletter_Settings::getEventSettings($eventId);
      $tabs['participantletter'] = array(
        'title' => ts('Participant Letter'),
        'link' => NULL, // 'link' is automatically provided if we're under the 'civicrm/event/manage' path.
        'class' => 'ajaxForm', // allows form to re-load itself on save.
        'valid' => (bool)CRM_Utils_Array::value('is_participantletter', $eventSettings),
        'active' => TRUE,
        'current' => TRUE,  // setting this to FALSE prevents the tab from getting
                            // focus when called directly, e.g., from under the
                            // "Configure" link on the Manage Events listing page.
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
    if ($eventId = CRM_Utils_Array::value('event_id', $context)) {
      $eventSettings = CRM_Participantletter_Settings::getEventSettings($eventId);
      $tabs[$eventId]['is_participantletter'] = CRM_Utils_Array::value('is_participantletter', $eventSettings);
    }
  }
}

function participantletter_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Participant' && $op == 'create') {
    $eventSettings = CRM_Participantletter_Settings::getEventSettings($objectRef->event_id);
    if (
      CRM_Utils_Array::value('is_participantletter', $eventSettings)
      && ($template_id = CRM_Utils_Array::value('template_id', $eventSettings))
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
      catch(CiviCRM_API3_Exception $e) {
        CRM_Core_Error::debug_log_message("Participantletter: Could not send email to participant_id: {$objectRef->id}, contact_id: {$objectRef->contact_id}, template_id: {$template_id}; Email.send API error: ". $e->getMessage());
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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function participantletter_civicrm_xmlMenu(&$files) {
  _participantletter_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function participantletter_civicrm_postInstall() {
  _participantletter_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function participantletter_civicrm_uninstall() {
  _participantletter_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function participantletter_civicrm_enable() {
  _participantletter_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function participantletter_civicrm_disable() {
  _participantletter_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function participantletter_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _participantletter_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function participantletter_civicrm_managed(&$entities) {
  _participantletter_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function participantletter_civicrm_caseTypes(&$caseTypes) {
  _participantletter_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function participantletter_civicrm_angularModules(&$angularModules) {
  _participantletter_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function participantletter_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _participantletter_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function participantletter_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function participantletter_civicrm_navigationMenu(&$menu) {
  _participantletter_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _participantletter_civix_navigationMenu($menu);
} // */
