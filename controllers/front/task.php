<?php

class LoxboxTaskModuleFrontController extends ModuleFrontControllerCore {

    public function __construct() 
    {
        parent::__construct();
    }

    public function init() 
    {

        parent::init();

    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(

            'message' => 'hello from controller'
        ));

         $this->setTemplate('task.tpl');

    }


}