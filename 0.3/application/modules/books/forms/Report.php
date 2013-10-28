<?php

class Books_Form_Report extends Zend_Form
{

    public function init()
    {
        $this->setAttrib('id','form_report_submitter');
        $this->addElement("Text","obj",array(
            'label' => 'Problem',
            'required' => true,
        ));
        $this->addElement("Textarea","body",array(
            'label' => "Description",
            'rows' => 15,
            'required' => true,
        ));
        $this->addElement("Button","sbm",array(
            "type" => 'submit',
            'label' => 'Send!',
            'onclick' => 'this.disabled="false";this.innerHTML="Wait...";form_report_submitter.submit();'
        ));
    }

}

