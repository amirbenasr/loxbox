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

            && $this->registerHook('displayCarrierList')
            && $this->registerHook('actionCarrierUpdate')
            && $this->registerHook('actionValidateOrder')
            && $this->registerHook('actionBeforeAjaxDieOrderOpcControllerinit')
            && $this->registerHook('actionCarrierUpdate')
            
            && Configuration::updateValue('Loxbox', 'default-token')
            && $this->installDb();

             
    }

  
    public function uninstall(): bool
    {
        return parent::uninstall()
            && Configuration::deleteByName('Loxbox')
            && $this->uninstallDb();
    }



    public function uninstallDb()
    {
        $db = Db::getInstance();
        $query ='SELECT (id_carrier) from ps_carrier where external_module_name="Loxbox"';
        $id = $db->getValue($query);
      

       $db->delete('carrier', '`id_carrier` = '.(int)$id);

  
        $db->delete('carrier_lang','id_carrier = '.(int)$id.'');
        $db->delete('carrier_group','id_carrier = '.(int)$id.'');
        $db->delete('carrier_lang','id_carrier = '.(int)$id.'');
        $db->delete('carrier_shop','id_carrier = '.(int)$id.'');
        $db->delete('carrier_shop','id_carrier = '.(int)$id.'');
        $db->delete('carrier_zone','id_carrier = '.(int)$id.'');
        return true; 
    }


    // public function hookActionBeforeAjaxDieOrderOpcControllerinit($params)
    // {
    //     // If we're not using a Mondial Relay carrier...
        
    //     die(Tools::jsonEncode($this->context->cart->id_carrier));
    // }

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
        
        // $sql = 'SELECT MAX(id_carrier) FROM '._DB_PREFIX_.'carrier';
        $sql = "SELECT AUTO_INCREMENT - 1 as CurrentId FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '"._DB_NAME_."' AND TABLE_NAME = '"._DB_PREFIX_."carrier' ";
       
        $totalCarriers = Db::getInstance()->getValue($sql);
        Configuration::set('PS_CARRIER_DEFAULT',$totalCarriers+1);

        $db->insert('carrier',array(
            'id_reference'=>(int)$totalCarriers+1,
            'name'=>'Livraison point relais LOXBOX',
            'external_module_name'=>'Loxbox',
            'active'=>(int)1,
            'is_free'=>(int)0,
            'shipping_handling'=>(int)0,
            
            
        ));

        $db->insert('delivery',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_shop'=>(int)1,
            'price'=>4.000000,
            'id_zone'=>(int)4,
            'id_range_weight'=>(int)6            
        ));

        $db->insert('range_weight',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'delimiter1'=>(int)0.000000,
            'delimiter2'=>0.100000,
                      
        ));

        $db->insert('carrier_lang',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_shop'=>(int)1,
            'id_lang'=>(int)1,
            'delay'=>"Livraison entre 24/48 heures"
        ));
        $db->insert('carrier_lang',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_shop'=>(int)1,
            'id_lang'=>(int)2,
            'delay'=>"Livraison entre 24/48 heures"
        ));
        $db->insert('carrier_zone',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_zone'=>(int)4,
        ));
        $db->insert('carrier_zone',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_zone'=>(int)1,
        ));
        $db->insert('carrier_zone',array(
            'id_carrier'=>(int)$totalCarriers+1,
            'id_zone'=>(int)2,
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

       copy(_PS_MODULE_DIR_.'loxbox/Logo-125.jpg', _PS_SHIP_IMG_DIR_ . ($totalCarriers+1) .'.jpg');
   
        return true;
    }

    public function hookDisplayCarrierList() 
    {
        $controller = $this->context->controller->php_self;

        ///get token from configuration
        $token = Configuration::get('Loxbox');
        $id_carrier = $this->context->cart->id_carrier;
        $db = Db::getInstance();
        $query = "SELECT * FROM `ps_carrier` WHERE id_carrier=$id_carrier";
        $carrier = $db->getRow($query);
        $new_carrier = new Carrier();
        // var_dump("test");
        // die;
        $new_carrier->hydrate($carrier);    
        Media::addJsDef(array(
            'isLoxbox' => $new_carrier->external_module_name=="Loxbox",
            'Loxbox_TOKEN'=>$token
        ));
        //test token
        $response =  get_web_page('https://www.loxbox.tn/api/Welcome/',$token);
        if($response==200)
        {
          
        
            // $this->context->controller->addJs(array(
            //     $this->_path.'views/js/list.js'
            //     ));
            $this->context->controller->addJs(array(
                $this->_path.'views/js/map_script.js'
            ));
            
           
            $this->context->controller->addJs(array(
                $this->_path.'views/js/widget.js'
            ));
            $this->context->controller->addCss(array(
                $this->_path.'views/css/style.css'
            ));

        }
        $this->context->controller->addJs(array(
            $this->_path.'views/js/widget.js'
        ));
       $this->context->smarty->assign(array(
        'valid'=>$response
    ));
       return $this->display(__FILE__,'views/templates/hook/display_widget.tpl');
    }

    

   
   public function hookActionValidateOrder($params)
    {
        //the thing you want to do when the hook's executed goes here
     
            
            $carrier_id = $params['cart']->id_carrier;
            $cart_id = $params['cart']->id;
            $orderDetails = $params['order'];

            ///if id carrier belongs to loxbox module
            ///we update the order address_delivery to the latest
            ///delivery of that customer within lopxboxmodule
            $db = Db::getInstance();

            $query_1 = 'SELECT * FROM `'._DB_PREFIX_.'carrier` where `id_carrier`='.$orderDetails->id_carrier.';';
            var_dump($query_1);
            $carrier = $db->getRow($query_1);
            $carrier_class = new Carrier();
            $carrier_class->hydrate($carrier);


            if($carrier_class->external_module_name=='Loxbox')
            {
                $sql = "SELECT MAX(id_address) FROM `ps_address` WHERE id_customer=".$orderDetails->id_customer.";";
                $db->getValue($sql);
             
                $orderDetails->id_address_delivery=$db->getValue($sql);
                $orderDetails->id_address_invoice=$db->getValue($sql);
                $orderDetails->update();
             

            }

          


            // $query = "SELECT * FROM `ps_carrier` WHERE id_carrier=$carrier_id";
            // $query2 = "SELECT * FROM `ps_cart` WHERE id_cart=$cart_id";
            // var_dump($query2);
            // $carrier = $db->getRow($query);
            // $cart = $db->getRow($query2);
            // $new_carrier = new Carrier();
            // $new_cart = new Cart();
            // $new_carrier->hydrate($carrier);
            // $new_cart->hydrate($cart);
            // $new_carrier->id_address_delivery = 44;
            // $new_cart->id_address_delivery = 8;
            // $new_cart->update();
            // var_dump($this->context->order);
            // var_dump($new_cart->id_address_delivery);
            // die;
            
            // if($new_carrier->external_module_name=="Loxbox")
            // {
            //     // $query2 = ''
            //             }
        

    }

}

function get_web_page( $url,$token )
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => false,     // follow redirects
        CURLOPT_HTTPHEADER => ['Authorization: Token '.$token],
        CURLOPT_POST=>false,
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
   
     
    $response = $http_code;

    return $response;
}

