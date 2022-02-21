<?php

class AdminLoxboxExpeditionController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
        $this->meta_title = $this->l('Expédition');
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }


    public function renderView()

    {

        $this->base_tpl_view='view.tpl';


      return  parent::renderView();
    }
  
 
}
