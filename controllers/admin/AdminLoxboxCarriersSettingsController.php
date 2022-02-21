<?php

class AdminLoxboxCarriersSettingsController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        $this->name="CarrierSettings";
        parent::__construct();
        $this->meta_title = $this->l('CarrierSettings');
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }
    // public function init()
    // {
    //     $this->initNewMondialrelayCarrierFormFields();

    //     parent::init();
    // }
    // protected function initNewMondialrelayCarrierFormFields()
    // {
    //     $description = $this->module->l('Create a new carrier(s) associated with the Mondial Relay module. [br] You will be able to add additional settings to this carrier once it is created via [a] Shipping > Carriers[/a]. [br] Please note that it is required to modify your carrier shipping fees, delivery time, package weight, height, etc... [br] Please pay attention that, by default, a new carrier will be available for every zone enabled in your shop.', 'AdminMondialrelayCarriersSettingsController', array('href' => $this->context->link->getAdminLink('AdminCarriers')));
        
    //     $this->fields_form_newMondialrelayCarrier = array(array(
    //         'form' => array(
    //             'legend' => array(
    //                 'title' => $this->module->l('Create a New Carrier', 'AdminMondialrelayCarriersSettingsController'),
    //                 'icon' => 'icon-cog'
    //             ),
    //             'description' => $description,
    //             'input' => array(
    //                 array(
    //                     'label' => $this->module->l('Carrier name', 'AdminMondialrelayCarriersSettingsController'),
    //                     'name' => 'name',
    //                     'type' => 'text',
    //                     'required' => true,
    //                 ),
    //                 array(
    //                     'label' => $this->module->l('Delivery time', 'AdminMondialrelayCarriersSettingsController'),
    //                     'name' => 'delay',
    //                     'type' => 'text',
    //                     'required' => true,
    //                 ),
    //                 array(
    //                     'label' => $this->module->l('Delivery mode', 'AdminMondialrelayCarriersSettingsController'),
    //                     'name' => 'delivery_mode',
    //                     'type' => 'select',
    //                     'options' => array(
    //                         'id' => 'value',
    //                         'name' => 'label',
    //                         'query' => MondialrelayTools::formatArrayForSelect($this->deliveryModesList),
    //                     ),
    //                     'hint' => $this->module->l('Please consult the details of your offer to find informations about your delivery mode options.', 'AdminMondialrelayCarriersSettingsController'),
    //                     'required' => true,
    //                 ),
    //                 array(
    //                     'label' => $this->module->l('Insurance', 'AdminMondialrelayCarriersSettingsController'),
    //                     'name' => 'insurance_level',
    //                     'type' => 'select',
    //                     'options' => array(
    //                         'id' => 'value',
    //                         'name' => 'label',
    //                         'query' => MondialrelayTools::formatArrayForSelect($this->insuranceLevelsList),
    //                     ),
    //                     'hint' => $this->module->l('Please consult the details of your offer to find informations about your insurance options.', 'AdminMondialrelayCarriersSettingsController'),
    //                     'required' => true,
    //                 ),
    //             ),
    //             'submit' => array(
    //                 'title' => $this->module->l('Save', 'AdminMondialrelayCarriersSettingsController'),
    //                 'name' => 'submitAddNewMondialrelayCarrier',
    //                 // We have to change our button id, otherwise some PS native
    //                 // JS script will hide it.
    //                 'id' => 'mondialrelay_submit-carrier-btn'
    //             ),
    //         ),
    //     ));
    // }
}
