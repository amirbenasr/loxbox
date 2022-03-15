<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Loxbox extends CarrierModule
{
    public $tabs = [
        [
            'name' => 'Merchant Expertise', // One name for all langs
            'class_name' => 'AdminGamification',
            'visible' => true,
            'parent_class_name' => 'ShopParameters',
        ],
    ];

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
        $this->displayName = 'LoxBox Services';
        $this->description = $this->l('Loxbox is the first tunisian product to offer Powerful Pickup Solution.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('Loxbox')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        $carrier = $this->addCarrier();
        $this->addZones($carrier);
        $this->addGroups($carrier);
        $this->addRanges($carrier);
        Configuration::updateValue('Loxbox', 'default-token');
        Configuration::updateValue('loxboxRelayId', 15);

        include dirname(__FILE__) . '/sql/install.php';

        return parent::install() &&
        $this->installTab() &&
        $this->installTab2() &&
        $this->installTab3() &&

        $this->registerHook('header') &&
        $this->registerHook('backOfficeHeader') &&
         $this->registerHook('displayBackOfficeHeader') &&
        $this->registerHook('updateCarrier') &&
        $this->registerHook('displayCarrierExtraContent') &&
        $this->registerHook('updateExtraCarrier') 
        && $this->registerHook('actionCarrierUpdate')
        && $this->registerHook('actionValidateOrder')
        && $this->registerHook('actionBeforeAjaxDieOrderOpcControllerinit')
        && $this->registerHook('actionCarrierUpdate');
    }

    
    public function uninstall()
    {
        Configuration::deleteByName('Loxbox');

        include dirname(__FILE__) . '/sql/uninstall.php';

        return parent::uninstall() && $this->uninstallTab();
    }

    public function installTab()
    {
        $tab = new Tab();
        $tab->active = true;
        $tab->class_name = 'AdminLoxbox';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Loxbox';
        }

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            //AdminPreferences
            $tab->id_parent = (int) Db::getInstance((bool) _PS_USE_SQL_SLAVE_)
                ->getValue(
                    'SELECT MIN(id_tab)
                        FROM `' . _DB_PREFIX_ . 'tab`
                        WHERE `class_name` = "' . pSQL('SELL') . '"'
                );
        } else {
            // AdminAdmin
            $tab->id_parent = (int) Tab::getIdFromClassName('AdminAdmin');
        }
        $tab->icon = 'local_shipping';
        $tab->module = $this->name;

        return $tab->add();
    }

  public function  installTab2()
    {
        $tab = new Tab();
        $tab->active = true;
        $tab->class_name = 'AdminLoxboxParametres';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Paramètres';
        }

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            //AdminPreferences
            $tab->id_parent = (int) Db::getInstance((bool) _PS_USE_SQL_SLAVE_)
                ->getValue(
                    'SELECT MIN(id_tab)
                        FROM `' . _DB_PREFIX_ . 'tab`
                        WHERE `class_name` = "' . pSQL('AdminLoxbox') . '"'
                );
        } else {
            // AdminAdmin
            $tab->id_parent = (int) Tab::getIdFromClassName('AdminAdmin');
        }
        $tab->module = $this->name;
        $tab->position = 2;

        return $tab->add();
    }

    public function  installTab3()
    {
        $tab = new Tab();
        $tab->active = true;
        $tab->class_name = 'AdminLoxboxExpedition';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Expédition';
        }

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            //AdminPreferences
            $tab->id_parent = (int) Db::getInstance((bool) _PS_USE_SQL_SLAVE_)
                ->getValue(
                    'SELECT MIN(id_tab)
                        FROM `' . _DB_PREFIX_ . 'tab`
                        WHERE `class_name` = "' . pSQL('AdminLoxbox') . '"'
                );
        } else {
            // AdminAdmin
            $tab->id_parent = (int) Tab::getIdFromClassName('AdminAdmin');
        }
        $tab->module = $this->name;
        $tab->position = 3;

        return $tab->add();
    }
    public function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminLoxbox');
        $id_tab2 = (int) Tab::getIdFromClassName('AdminLoxboxParametres');
        $id_tab3 = (int) Tab::getIdFromClassName('AdminLoxboxExpedition');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab2 = new Tab($id_tab2);
            $tab3 = new Tab($id_tab3);

            return $tab->delete() && $tab2->delete() && $tab3->delete() ;
        }

        return false;
    }

    public function getContent()
    {
        $this->context->controller->addCss(array(
            $this->_path . 'views/css/loxbox.css',
        ));
        $this->context->controller->addJs(array(
            $this->_path . 'views/js/loxbox.js',
        ));

        if (Tools::isSubmit('saveToken')) {

            $token = Tools::getValue('ltoken');
            Configuration::updateValue('Loxbox', $token);
        }
        $this->context->smarty->assign(array(
            'LoxboxToken' => Configuration::get('Loxbox'),

        ));
        return $this->display(__FILE__, 'views/templates/admin/loxbox_config.tpl')  ;
    }

    public function hookDisplayBackOfficeHeader()
    {
       

        return '<script>
            var admin_loxbox_link = ' . (string) json_encode(
            $this->context->link->getAdminLink('AdminLoxbox')
        ) . ';
            var current_id_tab = ' . (int) $this->context->controller->id . ';
        </script>';
    }

public function hookHeader($params)
{
 
    // Only on product page
    if ('order' === $this->context->controller->php_self) {
        
        $token = Configuration::get('Loxbox');
        // $id_carrier = $this->context->cart->id_carrier;
        $db = Db::getInstance();
        $id_carrier =  Configuration::get('PS_CARRIER_DEFAULT');
        $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'carrier` WHERE id_carrier=' .(int) $id_carrier;
        // var_dump($query);

        
        $carrier = $db->getRow($query);
        $new_carrier = new Carrier();
        $isloxbox=false;
        if($id_carrier!=0)
        {
            $new_carrier->hydrate($carrier);

        }
       
        if(($new_carrier->external_module_name == "loxbox" && $new_carrier->is_module==1) )
        {
            $isloxbox=true;
        }
      
                Media::addJsDef(array(
            'isLoxbox' => $isloxbox,
            'Loxbox_TOKEN' => $token,
            'front_link'=>$this->context->link->getModuleLink('loxbox','task')
        ));
        
        $this->context->controller->registerStylesheet(
            'module-loxbox-style',
            'modules/'.$this->name.'/views/css/style.css',
            [
              'media' => 'all',
              'priority' => 200,
            ]
        );

        $this->context->controller->registerJavascript(
            'module-loxbox-widget',
            'modules/'.$this->name.'/views/js/widget.js',
            [
              'priority' => 200,
              'attribute' => 'async',
            ]
        );
    }

  
}
    public function hookDisplayCarrierExtraContent()
    {
        $controller = $this->context->controller->php_self;

        ///get token from configuration
        $token = Configuration::get('Loxbox');
        $id_carrier = $this->context->cart->id_carrier;
        $db = Db::getInstance();
        $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'carrier` WHERE id_carrier=' . $id_carrier;
        $carrier = $db->getRow($query);
        $new_carrier = new Carrier();
        // var_dump($this->context->cart);
        // die();
        if($id_carrier!=0)
        {
        $new_carrier->hydrate($carrier);
            
        }
        Media::addJsDef(array(
            'isLoxbox' => ($new_carrier->external_module_name == "loxbox" && $new_carrier->is_module==1) ? true : false,
            'Loxbox_TOKEN' => $token,
        ));
        //test token
        $response = get_web_page('https://www.loxbox.tn/api/Welcome/', $token);
        if ($response == 200) {
         

            
       /*  
        $this->context->controller->registerJavascript(
            'module-loxbox-widget',
            'modules/'.$this->name.'/views/js/widget.js',
            [
              'priority' => 200,
              'attribute' => 'async',
            ]
        );
        */
        }

        $this->context->smarty->assign(array(
            'valid' => $response,
            'js_inclusion_template' => _PS_ALL_THEMES_DIR_ . 'javascript.tpl',
            'fromAjax' => $this->context->controller->ajax,
            'isLoxbox' => ($new_carrier->external_module_name == "loxbox" && $new_carrier->is_module==1 ) ? true : false,


        ));
        // print_r($this->context->cart);
        return $this->display(__FILE__, 'views/templates/hook/new_widget.tpl');
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        if (Context::getContext()->customer->logged == true) {
            $id_address_delivery = Context::getContext()->cart->id_address_delivery;
            $address = new Address($id_address_delivery);

            /**
             * Send the details through the API
             * Return the price sent by the API
             */
            return $address->price;
        }

        return $shipping_cost;
    }

    public function getOrderShippingCostExternal($params)
    {
        return true;
    }



    public function hookActionValidateOrder($params)
    {
        //the thing you want to do when the hook's executed goes here
        $token = Configuration::get('Loxbox');
        $db = Db::getInstance();

        $carrier_id = $params['cart']->id_carrier;
        $cart_id = $params['cart']->id;
        $orderDetails = $params['order'];
        $customer = $params['customer'];
        $product_list = $orderDetails->product_list;
        $content= "";
        $delivery_address = new Address((int)$orderDetails->id_address_delivery);
        $payment_method=0;

        if(strcmp($orderDetails->payment,'Payments by check')===0)
        {
            $payment_method=1;
        }


			// $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');

        foreach($product_list as $product)
        {
            $content .= $product['name'].',';
        }
        $fetch_carrier_query = 'SELECT `id_carrier`  FROM `'._DB_PREFIX_.'loxbox_last` WHERE id_cart= '.$cart_id;

       

        $payload = array(
            "Content"=>$content ?? "",
            "detail"=>"",
            "IsPaid"=>0,
            "Price"=>$orderDetails->total_paid ?? 0,
            "Size"=>1,
            "Weight"=>$product_list[0]['weight'],
            "DestRelaypoint"=> $db->getValue($fetch_carrier_query)?? 15,
            "ReceiverName"=>$customer->firstname.' '.$customer->lastname,
            "ReceiverMail"=>$customer->email,
            "ReceiverNumber"=> empty($delivery_address->phone ?? "") ? 000000 :$delivery_address->phone ,
            "ReceiverAddress"=>$delivery_address->address1 ?? "",
            "Comment"=>$orderDetails->note.' '.$orderDetails->payment ?? "",
            "AcceptsCheck"=>$payment_method        
         ) ;
        // var_dump($customer);
        // var_dump($payload);
        // var_dump($delivery_address);
        // var_dump($orderDetails->product_list);
        //  die();
        ///if id carrier belongs to loxbox module
        ///we update the order address_delivery to the latest
        ///delivery of that customer within lopxboxmodule

        $query_1 = 'SELECT * FROM `' . _DB_PREFIX_ . 'carrier` where `id_carrier`=' . $orderDetails->id_carrier . ';';
        $carrier = $db->getRow($query_1);
        $carrier_class = new Carrier();
        $carrier_class->hydrate($carrier);

        if ($carrier_class->external_module_name == 'loxbox') {

            $sql = 'SELECT MAX(id_address) FROM `' . _DB_PREFIX_ . 'address` WHERE id_customer=' . $orderDetails->id_customer;
            $db->getValue($sql);
            $orderDetails->id_address_delivery = $db->getValue($sql);
            $orderDetails->id_address_invoice = $db->getValue($sql);
            $orderDetails->update();

            //api call
            makeTransac($token,$payload);
        }
    }
    public function addCarrier()
    {
        $carrier = new Carrier();

        $carrier->name = $this->l('Livraison Point Relais LoxBox');
        $carrier->is_module = true;
        $carrier->active = 1;
        $carrier->range_behavior = 1;
        $carrier->need_range = 1;
        $carrier->shipping_external = false;
        $carrier->range_behavior = 0;
        $carrier->external_module_name = $this->name;
        $carrier->shipping_method = 2;

        foreach (Language::getLanguages() as $lang) {
            $carrier->delay[$lang['id_lang']] = $this->l('Super fast delivery');
        }

        if ($carrier->add() == true) {
            @copy(dirname(__FILE__) . '/Logo-125.jpg', _PS_SHIP_IMG_DIR_ . '/' . (int)$carrier->id . '.jpg');
            Configuration::updateValue('MYSHIPPINGMODULE_CARRIER_ID', (int)$carrier->id);
            return $carrier;
        }

        return false;
    }

    public function addGroups($carrier)
    {
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group) {
            $groups_ids[] = $group['id_group'];
        }

        $carrier->setGroups($groups_ids);
    }

    public function addRanges($carrier)
    {
        $range_price = new RangePrice();
        $range_price->id_carrier = $carrier->id;
        $range_price->delimiter1 = '0';
        $range_price->delimiter2 = '10000';
        $range_price->add();

        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '0';
        $range_weight->delimiter2 = '10000';
        $range_weight->add();
    }

    public function addZones($carrier)
    {
        $zones = Zone::getZones();

        foreach ($zones as $zone) {
            $carrier->addZone($zone['id_zone']);
        }

    }

    public function changePrice($carrier)
    {
        $price_list[] = array(
            'id_range_price' => ($range_type == Carrier::SHIPPING_METHOD_PRICE ? (int)$range->id : null),
            'id_range_weight' => ($range_type == Carrier::SHIPPING_METHOD_WEIGHT ? (int)$range->id : null),
            'id_carrier' => (int)$carrier->id,
            'id_zone' => (int)$id_zone,
            'price' => 4,
        );

        $range_table = $carrier->getRangeTable();
        $carrier->deleteDeliveryPrice($range_table);
        $carrier->addDeliveryPrice($price_list);
    }
}

function get_web_page($url, $token)
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true, // return web page
        CURLOPT_HEADER => false, // don't return headers
        CURLOPT_FOLLOWLOCATION => false, // follow redirects
        CURLOPT_HTTPHEADER => ['Authorization: Token ' . $token],
        CURLOPT_POST => false,
        CURLOPT_ENCODING => "", // handle all encodings
        CURLOPT_USERAGENT => "spider", // who am i
        CURLOPT_AUTOREFERER => true, // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
        CURLOPT_TIMEOUT => 120, // timeout on response
        CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false, // Disabled SSL Cert checks
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $header['errno'] = $err;
    $header['errmsg'] = $errmsg;
    $header['content'] = $content;

    $response = $http_code;

    return $response;
}

 function makeTransac($token,$payload)
{
    
$curl = curl_init();
// or use https://httpbin.org/ for testing purposes
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_URL, 'https://www.loxbox.tn/api/NewTransaction/');
curl_setopt($curl, CURLOPT_FAILONERROR, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//// Require fresh connection
//curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

//// Send POST request instead of GET and transfer data
//$postData = array(
//    'name' => 'John Doe',
//    'submit' => '1'
//);
//curl_setopt($curl, CURLOPT_POST, true);
//curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));

//// Use a different request method
//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
//// If the target does not accept custom HTTP methods
//// then use a regular POST request and a custom header variable
//curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT'));
//// Note: PHP only converts data of GET queries and POST form requests into
//// convenient superglobals (»$_GET« & »$_POST«) - To read the incoming
//// cURL request data you need to access PHPs input stream instead
//// using »parse_str(file_get_contents('php://input'), $_INPUT);«

//// Send JSON body via POST request
//$postData = array(
//    'name' => 'John Doe',
//    'submit' => '1'
//);+

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
//// Set headers to send JSON to target and expect JSON as answer
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Accept:application/json','Authorization: Token '.$token.' '));
//// As said above, the target script needs to read `php://input`, not `$_POST`!

//// Timeout in seconds
//curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
//curl_setopt($curl, CURLOPT_TIMEOUT, 10);

//// Dont verify SSL certificate (eg. self-signed cert in testsystem)
//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$output = curl_exec($curl);
if ($output === FALSE) {
    echo 'An error has occurred: ' . curl_error($curl) . PHP_EOL;
}
else {
    echo $output;
}


}

