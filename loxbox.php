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
        $this->displayName = 'loxbox';
        $this->description = $this->l('Loxbox is the first tunisian product to offer Powerful Relay Solution.');

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

        include dirname(__FILE__) . '/sql/install.php';

        return parent::install() &&
        $this->installTab() &&

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
                        WHERE `class_name` = "' . pSQL('AdminParentShipping') . '"'
                );
        } else {
            // AdminAdmin
            $tab->id_parent = (int) Tab::getIdFromClassName('AdminAdmin');
            $tab->icon = 'local_shipping';
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminLoxbox');
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
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
        $id_carrier = $this->context->cart->id_carrier;
        $db = Db::getInstance();
        $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'carrier` WHERE id_carrier=' .(int) $id_carrier;
        // var_dump($query);
        
        $carrier = $db->getRow($query);
        $new_carrier = new Carrier();
        if($id_carrier!=0)
        {
            $new_carrier->hydrate($carrier);

        }
        Media::addJsDef(array(
            'isLoxbox' => $new_carrier->external_module_name == "loxbox" ? true : false,
            'Loxbox_TOKEN' => $token,
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
        if($id_carrier!=0)
        {
        $new_carrier->hydrate($carrier);
            
        }
        Media::addJsDef(array(
            'isLoxbox' => $new_carrier->external_module_name == "loxbox" ? true : false,
            'Loxbox_TOKEN' => $token,
        ));
        
        //test token
        $response = get_web_page('https://www.loxbox.tn/api/Welcome/', $token);
        if ($response == 200) {
         

         
        $this->context->controller->registerJavascript(
            'module-loxbox-widget',
            'modules/'.$this->name.'/views/js/widget.js',
            [
              'priority' => 200,
              'attribute' => 'async',
            ]
        );
        }

        $this->context->smarty->assign(array(
            'valid' => $response,
            'js_inclusion_template' => _PS_ALL_THEMES_DIR_ . 'javascript.tpl',
            'fromAjax' => $this->context->controller->ajax,
            'isLoxbox' => $new_carrier->external_module_name == "loxbox" ? true : false,


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

        $carrier_id = $params['cart']->id_carrier;
        $cart_id = $params['cart']->id;
        $orderDetails = $params['order'];

        ///if id carrier belongs to loxbox module
        ///we update the order address_delivery to the latest
        ///delivery of that customer within lopxboxmodule
        $db = Db::getInstance();

        $query_1 = 'SELECT * FROM `' . _DB_PREFIX_ . 'carrier` where `id_carrier`=' . $orderDetails->id_carrier . ';';
        var_dump($query_1);
        $carrier = $db->getRow($query_1);
        $carrier_class = new Carrier();
        $carrier_class->hydrate($carrier);

        if ($carrier_class->external_module_name == 'loxbox') {
            $sql = 'SELECT MAX(id_address) FROM `' . _DB_PREFIX_ . 'address` WHERE id_customer=' . $orderDetails->id_customer;
            $db->getValue($sql);

            $orderDetails->id_address_delivery = $db->getValue($sql);
            $orderDetails->id_address_invoice = $db->getValue($sql);
            $orderDetails->update();
        }
    }
    public function addCarrier()
    {
        $carrier = new Carrier();

        $carrier->name = $this->l('Transporteur Loxbox');
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
