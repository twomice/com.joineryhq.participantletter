<?php

use CRM_Participantletter_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Participantletter_Form_Event_Config extends CRM_Core_Form {
  public function buildQuickForm() {

    // add form elements
    $this->add(
      'checkbox', // field type
      'send_letter', // field name
      'Send letter upon registration?' //, // field label
//      FALSE // is required
    );
    $this->add(
      'select', // field type
      'message_template_id', // field name
      'Message Template', // field label
       $this->getTemplateOptions(), // list of options
      FALSE // is required
    );
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    if (CRM_Core_Permission::check('edit message templates')) {
      $link = '<a href="'. CRM_Utils_System::url('civicrm/admin/messageTemplates', 'reset=1') .'">manage Message Templates</a>';
        
      $manageMessageTemplatesHelpLink = "(You can also $link).";
      $this->assign('manageMessageTemplatesHelpLink', $manageMessageTemplatesHelpLink);
    }
    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $options = $this->getTemplateOptions();
    CRM_Core_Session::setStatus(E::ts('You picked template "%1"', array(
      1 => $options[$values['message_template_id']],
    )));
    parent::postProcess();
  }

  public function getTemplateOptions() {
    return CRM_Core_BAO_MessageTemplate::getMessageTemplates(FALSE);
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
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
