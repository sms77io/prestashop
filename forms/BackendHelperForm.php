<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    sms77.io
 * @copyright 2019-present sms77.io
 * @license   LICENSE
 */

class BackendHelperForm extends HelperForm
{
    public function __construct($name)
    {
        parent::__construct($name);

        $defaultLang = Configuration::get('PS_LANG_DEFAULT');

        $this->allow_employee_form_lang = $defaultLang;

        $this->currentIndex = AdminController::$currentIndex . "&configure=$name";

        $this->default_form_language = $defaultLang;

        $this->fields_value = [
            'config[SMS77_API_KEY]' => Configuration::get('SMS77_API_KEY'),
            'config[SMS77_ON_INVOICE]' => Configuration::get('SMS77_ON_INVOICE'),
            'config[SMS77_ON_DELIVERY]' => Configuration::get('SMS77_ON_DELIVERY'),
            'config[SMS77_ON_SHIPMENT]' => Configuration::get('SMS77_ON_SHIPMENT'),
            'config[SMS77_ON_PAYMENT]' => Configuration::get('SMS77_ON_PAYMENT'),
            'config[SMS77_MSG_ON_DELIVERY]' => Configuration::get('SMS77_MSG_ON_DELIVERY'),
            'config[SMS77_MSG_ON_INVOICE]' => Configuration::get('SMS77_MSG_ON_INVOICE'),
            'config[SMS77_MSG_ON_SHIPMENT]' => Configuration::get('SMS77_MSG_ON_SHIPMENT'),
            'config[SMS77_MSG_ON_PAYMENT]' => Configuration::get('SMS77_MSG_ON_PAYMENT'),
        ];

        $this->fields_form = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Settings'),
                    ],
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'config[SMS77_API_KEY]',
                            'label' => $this->l("API-Key"),
                            'hint' => $this->l('Your sms77.io API-Key.'),
                            'desc' => $this->l('An API-Key is needed for sending. Get yours now at sms77.io'),
                            'required' => true,
                        ],

                        $this->makeSwitch(
                            "INVOICE",
                            'Text on invoice generation?',
                            'Send a text message after an invoice has been created?'
                        ),
                        $this->makeSwitch(
                            "PAYMENT",
                            'Text on payment?',
                            'Send a text message after payment has been received?'
                        ),
                        $this->makeSwitch(
                            "SHIPMENT",
                            'Text on shipment?',
                            'Send a text message after shipment?'
                        ),
                        $this->makeSwitch(
                            "DELIVERY",
                            'Text on delivery?',
                            'Send a text message after delivery?'
                        ),

                        $this->makeTextarea(
                            "INVOICE",
                            'Sets the text message sent to the customer after invoice generation.'
                        ),
                        $this->makeTextarea(
                            "PAYMENT",
                            'Sets the text message sent to the customer after payment.'
                        ),
                        $this->makeTextarea(
                            "SHIPMENT",
                            'Sets the text message sent to the customer after shipment.'
                        ),
                        $this->makeTextarea(
                            "DELIVERY",
                            'Sets the text message sent to the customer after delivery.'
                        ),
                    ],
                    'submit' => [
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right'
                    ]
                ]
            ]
        ];

        $this->module = $this;

        $this->name = $name;

        $this->name_controller = $name;

        $this->title = $name;

        $this->token = Tools::getAdminTokenLite('AdminModules');

        $this->show_toolbar = true;

        $this->submit_action = 'submit' . $name;

        $this->toolbar_btn = [
            'save' =>
                [
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . "&configure=$name&save$name&token="
                     . Tools::getAdminTokenLite('AdminModules'),
                ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        $this->toolbar_scroll = true;
    }

    private function makeTextarea(string $action, string $trans)
    {
        $trans = $this->l($trans);

        return [
            'type' => 'textarea',
            'name' => "config[SMS77_ON_$action]",
            'label' => $trans,
            'hint' => $trans,
            'desc' => $trans,
        ];
    }

    private function makeSwitch(string $action, string $label, string $desc)
    {
        $descHit = $this->l($desc);

        return [
            'type' => 'switch',
            'name' => "config[SMS77_MSG_ON_$action]",
            'label' => $this->l($label),
            'desc' => $descHit,
            'hint' => $descHit,
            'is_bool' => true,
            'values' => [
                [
                    'id' => "on_" . Tools::strtolower($action) . "_on",
                    'value' => 1,
                    'label' => $this->l('Yes')
                ],
                [
                    'id' => "on_" . Tools::strtolower($action) . "_off",
                    'value' => 0,
                    'label' => $this->l('No')
                ]
            ]
        ];
    }
}
