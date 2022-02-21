<?php

class AdminLoxboxController extends ModuleAdminController
{
    /**
     * @var gamification
     */
    public $module;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
        $this->meta_title = $this->l('Loxbox');
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryUI('ui.progressbar');

        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/loxbox_back.js');
        $link = new Link;
        $parameters = array("action" => "action");
        $ajax_link = $link->getModuleLink('loxbox','AdminLoxbox', $parameters);
    
        Media::addJsDef(array(
            "ajax_link" => $ajax_link
        ));

        // $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/jquery.isotope.js');
        // $this->addCSS([_MODULE_DIR_ . $this->module->name . '/views/css/bubble-popup.css', _MODULE_DIR_ . $this->module->name . '/views/css/isotope.css']);
    }

    public function renderView()
    {

        $this->base_tpl_view = 'admin_loxbox.tpl';
        $this->tpl_view_vars = [
            'test' => "hi baby",
            
        ];
        return parent::renderView();


    }

    public function ajaxProcessAdminLoxboxAction()
    {
    	echo json_encode('foo');//something you want to return
        exit;
    }



}