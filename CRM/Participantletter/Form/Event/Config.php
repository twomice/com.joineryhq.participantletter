<?php

use CRM_Participantletter_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Participantletter_Form_Event_Config extends CRM_Event_Form_ManageEvent {
  // class CRM_Participantletter_Form_Event_Config extends CRM_Core_Form {

  /**
   * The ID of the entity (in this case, the event) which we're configuring.
   *
   * @var int
   * @see getEntityId()
   */
  private $entityId = NULL;

  /**
   * Returns the ID of the entity (in this case, the event) which we're configuring.
   *
   * @return int
   */
  protected function getEntityId() {
    if ($this->entityId === NULL) {
      $this->entityId = !empty($this->_id) ? $this->_id : CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);
    }
    return $this->entityId;
  }

  public function buildQuickForm() {
    // add form elements
    // field type and name
    $this->add(
      'hidden',
      'event_id'
    );
    // field type, name, and label
    $this->add(
      'checkbox',
      'is_participantletter',
      E::ts('Email letter upon registration?')
    );
    // field type, name, label, list of options and is required
    $this->add(
      'select',
      'template_id',
      E::ts('Message Template'),
       $this->getTemplateOptions(),
      FALSE
    );
    $this->addButtons(array(
      array(
        'type' => 'done',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    if (CRM_Core_Permission::check('edit message templates')) {
      $link = '<a href="' . CRM_Utils_System::url('civicrm/admin/messageTemplates', 'reset=1') . '">' . E::ts('manage Message Templates') . '</a>';
      $manageMessageTemplatesHelpLink = E::ts('(You can also %1).', array('1' => $link));
      $this->assign('manageMessageTemplatesHelpLink', $manageMessageTemplatesHelpLink);
    }
    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());

    $eventSettings = CRM_Participantletter_Settings::getEventSettings($this->getEntityId());
    $defaults = array(
      'event_id' => $this->getEntityId(),
      'template_id' => CRM_Utils_Array::value('template_id', $eventSettings),
      'is_participantletter' => CRM_Utils_Array::value('is_participantletter', $eventSettings),
    );
    $this->setDefaults($defaults);

    // Add JS to handle show/hide stuff.
    CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.participantletter', 'js/Participantletter_Form_Event_Config.js');

    // Present a warning if email.send api is not avaialable.
    if (!CRM_Participantletter_Utils::canSendEmail()) {
      CRM_Core_Session::setStatus(
        E::ts('The "Participant Letter" option requires the <em>E-mail API</em> extension, which is not enabled. To allow Participant Letter emails to be sent, please enable the extension.'),
        E::ts("Extension missing"),
        "error"
      );
    }

    parent::buildQuickForm();
  }

  public function postProcess() {
    $eventSettings = array(
      'template_id' => CRM_Utils_Array::value('template_id', $this->_submitValues),
      'is_participantletter' => CRM_Utils_Array::value('is_participantletter', $this->_submitValues),
    );
    if (CRM_Participantletter_Settings::saveAllEventSettings($this->getEntityId(), $eventSettings)) {
      CRM_Core_Session::setStatus(" ", E::ts('Settings saved.'), "success");
    }
    else {
      CRM_Core_Session::setStatus(" ", E::ts('Error. Settings not saved.'), "error");
    }
    parent::postProcess();
  }

  private function getTemplateOptions() {
    return array('0' => '- select -') + CRM_Core_BAO_MessageTemplate::getMessageTemplates(FALSE);
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  private function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
