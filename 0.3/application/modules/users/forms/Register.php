<?php

class Users_Form_Register extends Zend_Form
{
    private $_type;

    public function __construct($type = "register", $options = null) {
        $this->_type = $type;
        parent::__construct($options);
    }

    public function init()
    {
        $zt = Zend_Registry::get("Zend_Translate");
        if ($this->_type == "register") {
            $this->addElement("Text","user_name",array(
                "label" => "Username",
                'validators' => array(
                    'alnum',
                    array('regex', false, '/^[a-z0-9\._-]+$/i')
                ),
                'required' => true,
                'filters'  => array('StringToLower'),
            ));

            $this->addElement("Password","user_pass",array(
                "label" => "Password",
                'required' => true,
            ));

            $this->addElement("Password","user_pass2",array(
                "label" => "Repeat password",
                'required' => true,
            ));
        }
        $this->addElement("Text","user_mail",array(
            "label" => "E-Mail",
            'required' => true,
            'validators' => array(
                array('regex',false,'/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/'),

            )
        ));

        $this->addElement("Select","user_locale",array(
            "label" => "Language",
            "required" => true,
            "multiOptions" => array(
                "en_EN" => "English",
                "it_IT" => "Italiano",
            )
        ));

        if ($this->_type == "register") {
            $this->addElement("Captcha","captcha",array(
                "label" => "Please enter the letters displayed below:",
                "required" => true,
                "captcha" => array(
                    "captcha" => "Figlet",
                    "wordLen" => 6,
                    "timeout" => 300,
                )
            ));
            //
            $this->addElement("Textarea","disclaymer",array(
                "label" => "Iscrivendoti al sito accetti il regolamento in tutte le sue parti.",
                "value" => $zt->translate('DISCLAIMER')
            ));
        } else {
            //Altri campi
            $this->addElement("Select","user_notifications",array(
                'label' => 'Do you want receive notifications via mail?',
                'multiOptions' => array(
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ));
            $this->addElement("Select","user_preferred_color",array(
                'label' => 'Preferred color',
                'onchange' => 'setMenuBackGround(this);',
                'multiOptions' => array(),
            ));
        }

        $this->addElement("Button","submit",array(
            "type" => "submit",
            "label" => "Register",
            "prepend" => "ciao"
        ));

    }

    public function setColors($values,$value) {
        $this->user_preferred_color->setMultiOptions($values);
        $this->user_preferred_color->setValue($value);
    }

    public function setValues($values) {
        //
        $this->user_mail->setValue($values->user_mail);
        $this->user_locale->setValue($values->user_locale);
        $this->user_notifications->setValue($values->user_notifications);
        $this->submit->setLabel("Edit!");
    }

}

