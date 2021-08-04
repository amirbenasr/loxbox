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
            && $this->registerHook('displayBeforeCarrier')
            && Configuration::updateValue('Loxbox', 'default-token')
            && $this->installDb();
             
    }

  
    public function uninstall(): bool
    {
        return (parent::uninstall()
            && Configuration::deleteByName('Loxbox'))
            && $this->uninstallDb();
    }


    public function uninstallDb()
    {
        $db = Db::getInstance();
        $query ='SELECT (id_carrier) from ps_carrier where external_module_name="loxbox"';
        $id = $db->getValue($query);

        $db->delete('carrier','id_carrier = '.(int)$id.'');
        $db->delete('carrier_lang','id_carrier = '.(int)$id.'');
        $db->delete('carrier_group','id_carrier = '.(int)$id.'');
        $db->delete('carrier_lang','id_carrier = '.(int)$id.'');
        $db->delete('carrier_shop','id_carrier = '.(int)$id.'');
        $db->delete('carrier_shop','id_carrier = '.(int)$id.'');
        $db->delete('carrier_zone','id_carrier = '.(int)$id.'');
        return true;
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


    public function installDb() 
    {


        $db = Db::getInstance();
        
        $sql = 'SELECT MAX(id_carrier) FROM '._DB_PREFIX_.'carrier';
        $totalCarriers = Db::getInstance()->getValue($sql);
        
        $db->insert('carrier',array(
            'id_reference'=>(int)$totalCarriers+1,
            'name'=>'loxbox',
            'external_module_name'=>'Loxbox',
            'active'=>(int)1,
            'is_free'=>(int)1
            
        ));
        $db->insert('carrier_lang',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_shop'=>(int)1,
            'id_lang'=>(int)1,
            'delay'=>"Instant delivery"
        ));
        $db->insert('carrier_lang',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_shop'=>(int)1,
            'id_lang'=>(int)2,
            'delay'=>"Livraison instantanée"
        ));
        $db->insert('carrier_zone',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_zone'=>(int)4,
        ));
        $db->insert('carrier_group',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_group'=>(int)1,
        ));
        $db->insert('carrier_group',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_group'=>(int)2,
        ));
        $db->insert('carrier_group',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_group'=>(int)3,
        ));
        $db->insert('carrier_shop',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_shop'=>(int)1,
        ));
        copy(_PS_MODULE_DIR_.'loxbox/logo.png', _PS_SHIP_IMG_DIR_ . '/' . (int) $totalCarriers+1 . '.png');
  
return true;
    }

    public function hookDisplayBeforeCarrier() 
    {
        $this->context->controller->addJs(array(
            $this->_path.'views/js/list.js'
            ));
        $this->context->controller->addJs(array(
            $this->_path.'views/js/map_script.js'
        ));

       
 

        $this->context->controller->addJs(array(
            $this->_path.'views/js/widget.js'
        ));
        $this->context->controller->addCss(array(
            $this->_path.'views/css/style.css'
        ));
        $this->context->smarty->assign(array(
            'carrierx' => $carrier
        ));

       return $this->display(__FILE__,'views/templates/hook/display_widget.tpl');
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
