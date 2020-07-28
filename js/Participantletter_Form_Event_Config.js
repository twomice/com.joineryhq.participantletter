(function(ts) {
  CRM.$(function($) {
    var participantletterIsActiveChange = function participantletterIsActiveChange() {
      var $el = $('select#template_id').closest('div.crm-section');
      if ($(this).is(':checked')) {
        $el.show();
      }
      else {
        $el.hide();
      }
    };

    $('input#is_participantletter').change(participantletterIsActiveChange).change();
  });
}(CRM.ts('com.joineryhq.participantletter')));