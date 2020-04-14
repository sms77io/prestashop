<?php/** * NOTICE OF LICENSE * * This file is licenced under the Software License Agreement. * With the purchase or the installation of the software in your application * you accept the licence agreement. * * You must not modify, adapt or create derivative works of this source code * * @author    sms77.io * @copyright 2019-present sms77 e.K. * @license   LICENSE */require_once __DIR__ . "/../../models/Sms77Message.php";require_once __DIR__ . "/../../classes/Util.php";class Sms77AdminController extends ModuleAdminController{    private static function sendBulk($msg, $countries = [], $groups = []) {        $activeCustomers = TableWrapper::getActiveCustomerAddressesByGroupsAndCountries($groups, $countries);        $hasPlaceholder = preg_match('{0}', $msg) || preg_match('{1}', $msg);        if ($hasPlaceholder) { // send multiple personalized messages            $res = [];            foreach ($activeCustomers as $activeCustomer) {                $res[] = Util::validateAndSend(                    (new Personalizer($msg, $activeCustomer))->toString(),                    self::getRecipient($activeCustomer));            }            return Util::insert($res, 'bulk_personalized', $groups, $countries) ? $res : null;        } else {            $phoneNumbers = array_map(static function ($d) {                return self::getRecipient($d);            }, $activeCustomers);            if (count($phoneNumbers)) {                $res = Util::validateAndSend($msg, $phoneNumbers);                return Util::insert($res, 'bulk', $groups, $countries) ? $res : null;            }            return null;        }    }    private static function getRecipient($customer) {        return '' === $customer['phone'] ? $customer['phone_mobile'] : $customer['phone'];    }    public function __construct() {        $this->table = 'sms77_message';        $this->className = 'Sms77Message';        $this->context = Context::getContext();        parent::__construct();    }    function renderList() {        $this->fields_list = [            'id_sms77_message' => [                'title' => $this->l('ID'),            ],            'timestamp' => [                'title' => $this->l('Timestamp'),            ],            'response' => [                'title' => $this->l('Response'),            ],            'type' => [                'title' => $this->l('Type'),            ],            'groups' => [                'title' => $this->l('Groups'),            ],            'countries' => [                'title' => $this->l('Countries'),            ],        ];        $lists = parent::renderList();        parent::initToolbar();        return $lists;    }    function renderForm() {        $this->fields_form = [            'tinymce' => true,            'legend' => [                'title' => $this->l('New Message'),            ],            'input' => [                [                    'label' => $this->l('Text'),                    'name' => 'text',                    'rows' => 5,                    'type' => 'textarea',                ],                [                    'label' => $this->l('Countries'),                    'multiple' => true,                    'name' => Constants::BULK_COUNTRIES . '[]',                    'options' => [                        'id' => 'id_country',                        'name' => 'name',                        'query' => Country::getCountries($this->context->language->id),                    ],                    'type' => 'select',                ],                [                    'label' => $this->l('Groups'),                    'multiple' => true,                    'name' => Constants::BULK_GROUPS . '[]',                    'options' => [                        'id' => 'id_group',                        'name' => 'name',                        'query' => Group::getGroups($this->context->language->id),                    ],                    'type' => 'select',                ],            ],            'submit' => [                'class' => 'button',                'title' => $this->l('Save'),            ],        ];        if (!($obj = $this->loadObject(true))) {            return;        }        return parent::renderForm();    }    function postProcess() {        if (!Tools::isSubmit('submitAdd' . $this->table)) {            return;        }        if (!Tools::strlen(Configuration::get(Constants::API_KEY))) {            $this->errors[] = Tools::displayError('No API key given.');        }        if (count($this->errors)) {            return;        }        $res = self::sendBulk(            Tools::getValue('text'),            Tools::getValue(Constants::BULK_COUNTRIES),            Tools::getValue(Constants::BULK_GROUPS));        if (null === $res || (is_array($res) && !count($res))) {            $this->errors[] = Tools::displayError('An error has occurred: ' . $res);        } else {            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);        }    }}