<?php



if (!defined('_PS_VERSION_')) {
    exit;
}


class Loxbox extends Module
{

    public function __construct()
    {
        $this->name = 'loxbox';
        $this->version = '1.0.0';
        $this->author = 'LoxboxDev';
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => '1.7.99',
        ];
        parent::__construct();
        $this->displayName = 'loxbox';
        $this->description = $this->l('Loxbox is the first tunisian product to offer Powerful Relay Solution.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('Loxbox')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install(): bool
    {

        return parent::install()
            && $this->registerHook('Header')
            && $this->registerHook('displayTopColumn')
            && Configuration::updateValue('Loxbox', 'default-token');
             
    }

  
    public function uninstall(): bool
    {
        return (parent::uninstall()
            && Configuration::deleteByName('Loxbox'));
    }

    public function getContent()
    {
        $this->context->controller->addCss(array(
            $this->_path.'views/css/loxbox.css'
         ));
         $this->context->controller->addJs(array(
             $this->_path.'views/js/loxbox.js'
         ));
         
        if(Tools::isSubmit('saveToken'))
        {   
            $token = Tools::getValue('ltoken');
            Configuration::updateValue('Loxbox',$token);
         
        }
        $this->context->smarty->assign(array(
            'LoxboxToken' => Configuration::get('Loxbox')

        ));
        return $this->display(__FILE__,'views/templates/admin/loxbox_config.tpl');
    }


  public function  installDb() 
    {


        $db = Db::getInstance();
        
        $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'shop';
        $totalCarriers = Db::getInstance()->getValue($sql);
        $db->insert('carrier',array(
            'id_reference'=>(int)$totalCarriers+1,
            'name'=>'loxbox',
        ));
        


    }
   public function hookDisplayTopColumn()
   {
  
       $latestToken = Configuration::get('Loxbox');
       

        $sql = "SELECT COUNT(*) as oldValue\n". " FROM ps_carrier;";
        $items = Db::getInstance()->executeS($sql);

       
       $this->context->smarty->assign(array(
           'LoxboxToken' => $items
       ));
       return $this->display(__FILE__,'views/templates/hook/display_top.tpl');
   }

    public function hookHeader()
    {

        $this->context->controller->addCss(array(
           $this->_path.'views/css/loxbox.css'
        ));
        $this->context->controller->addJs(array(
            $this->_path.'views/js/loxbox.js'
        ));
       return $this->display(__FILE__,'views/templates/hook/display_top.tpl');

    }

}
