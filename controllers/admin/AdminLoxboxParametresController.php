<?php


class AdminLoxboxParametresController extends ModuleAdminController 
{



    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        $this->toolbar_title[] = 'Paramètres';
        parent::__construct();
        $this->meta_title = $this->l('Paramètres');
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function renderView()
    {

        $this->base_tpl_view = 'view.tpl';
        return parent::renderView();


    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/loxbox.js');
        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/parametres.css');

        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/loxbox.css');


    }

    public function init()
    {

       
        if (Tools::isSubmit('saveToken')) {

            $token = Tools::getValue('ltoken');
            Configuration::updateValue('Loxbox', $token);
        }
        $this->context->smarty->assign(array(
            'LoxboxToken' => Configuration::get('Loxbox'),

        ));

        return parent::init();
    }

}