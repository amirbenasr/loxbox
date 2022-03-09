<?php

require_once _PS_MODULE_DIR_ . '/loxbox/classes/LoxboxCarrierMethod.php';

class AdminLoxboxExpeditionController extends ModuleAdminController
{
    protected $fields_form_newMondialrelayCarrier = array();


    public function __construct()
    {
        $this->bootstrap = true;
        $this->table='loxbox_carrier';
        $this->className = "LoxboxCarrierMethod";
        // $this->display="view";






        
        parent::__construct();
        $this->meta_title = $this->l('Expédition');
       $this->initList();
      
    }
    public function init()
    {
        $this->initNewMondialrelayCarrierFormFields();

        parent::init();
    }

    public function initList()
    {
        $this->explicitSelect = true;
        
        $this->fields_list = array(
            $this->identifier => array(
                'title' => 'Loxbox carrier ID ' ,
                'align' => 'center',
                'class' => 'fixed-width-xs',
                
            ),
            $this->identifier => array(
                'title' => 'Native ID Carrier',
                'filter_key' => 'p_c!id_carrier',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => 'name',
                'filter_key' => 'p_c!name'
            ),
            'delivery_mode' => array(
                'title' =>'delivery mode',
            ),
  
        );
        
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.Carrier::$definition['table'].'` p_c ON p_c.id_carrier = a.id_carrier ';
        $this->_join .= Shop::addSqlAssociation(Carrier::$definition['table'], 'p_c');
        $this->_where = 'AND p_c.deleted = 0';
        $this->_group = 'GROUP BY a.id_carrier';
        
        $this->actions = array('edit', 'delete');
    }


    public function initNewMondialrelayCarrierFormFields()
    {

        $options = array(
            array(
              'value' => '24R', 
              'label' => 'Livraison à domicile' 
            ),
          
  );
        // $description = 'Create a new carrier';
        
        $this->fields_form_newMondialrelayCarrier = array(array(
            'form' => array(
                'legend' => array(
                    'title' => 'Ajouter un nouveau transporteur ',
                    'icon' => 'icon-cog'
                ),
                'description' => "Ajouter un nouveau transporteur loxbox",
                'input' => array(
                    array(
                        'label' => 'Nom du transporteur',
                        'name' => 'name',
                        'type' => 'text',
                        
                    ),
                    array(
                        'label' => 'Mode de livraison',
                        'name' => 'delivery_mode',
                        'type' => 'select',
                        'options' => array(
                            'id' => 'value',
                            'name' => 'label',
                            'query'=>$options
                           
                        ),
                        'hint' => 'Mode de livraison',
                        'required' => true,
                    ),
                   
                ),
                'submit' => array(
                    'title' => 'Save',
                    'name' => 'submitAddLoxboxCarrier',
                    // We have to change our button id, otherwise some PS native
                    // JS script will hide it.
                    'id' => 'loxbox_add_carrier'
                ),
            ),
        ));
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit('submitAddLoxboxCarrier')) {
            $this->action = 'addNewLoxboxCarrier';
        }
    }


    protected function processAddNewLoxboxCarrier()
    {

        foreach ($this->fields_form_newMondialrelayCarrier[0]['form']['input'] as $field) {
            $value = trim(Tools::getValue($field['name']));
            
            if (!empty($field['required']) && empty($value) && (string)$value != '0') {
                $this->errors[] = $this->module->l('Field %field% is required.', 'AdminMondialrelayCarriersSettingsController', array('%field%' => $field['label']));
                continue;
            }
        }
        
        if (!empty($this->errors)) {
            return false;
        }
        
        $carrier = $this->addCarrier(Tools::getValue('name'));
        $this->addZones($carrier);
        $this->addGroups($carrier);
        $this->addRanges($carrier);

        $query ="INSERT INTO `"._DB_PREFIX_."loxbox_carrier` ( `id_carrier`, `delivery_mode`, `is_deleted`, `id_reference`) VALUES('".(int) $carrier->id."','24R','0','".(int) $carrier->id."')  ";
        
        return Db::getInstance()->execute($query);
    }

    
    
    public function renderList()
    {
        
        // Render form before list
       
        $helper = new HelperForm($this);
        $helper->name_controller = 'LoxboxExpedition';
        $this->setHelperDisplay($helper);
        $helper->fields_value = array('name' => '', 'delay' => '', 'delivery_mode' => '', 'insurance_level' => '');

        $this->content .= $helper->generateForm($this->fields_form_newMondialrelayCarrier);
        
        // Render list
        $list = parent::renderList();
        if (!empty($this->_list)) {
            $this->content .= $list;
            return;
        }
        $tpl = $this->createTemplate('empty_list.tpl');
        $tpl->assign(array(
            'title' => $this->helper->title,
            'message' => "Aucune méthode d'expédition disponible. Veuillez créer un nouveau transporteur en utilisant le formulaire ci-dessus.",
        ));
        $this->content .= $tpl->fetch();
        
        // If list is empty, we have a custom message
     
    }

    public function addCarrier($name)
    {
        $carrier = new Carrier();

        $carrier->name = $name;
        $carrier->is_module = false;
        $carrier->active = 1;
        $carrier->range_behavior = 1;
        $carrier->need_range = 1;
        $carrier->shipping_external = false;
        $carrier->range_behavior = 0;
        $carrier->external_module_name = 'loxbox';
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

        /**
     * Displays a "delete" link; we need it pointing to the AdminCarriers
     * controller.
     *
     * Most of this code is from AdminCarriers
     *
     * @param string $token
     * @param int $id
     * @param string $name
     *
     * @return string
     */
    public function displayDeleteLink($token, $id, $name)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');

        if (!array_key_exists('Delete', self::$cache_lang)) {
            self::$cache_lang['Delete'] = $this->l('Delete', 'Helper');
        }

        if (!array_key_exists('DeleteItem', self::$cache_lang)) {
            self::$cache_lang['DeleteItem'] = $this->l('Delete item?', 'Helper');
        }

        if (!array_key_exists('Name', self::$cache_lang)) {
            self::$cache_lang['Name'] = $this->l('Name:', 'Helper');
        }

        if (!is_null($name)) {
            $name = '\n\n'.self::$cache_lang['Name'].' '.$name;
        }

        $data = array(
            $this->identifier => $id,
            'href' => $this->context->link->getAdminLink('AdminCarriers')
                . '&id_carrier='.(int)$id
                . '&deletecarrier=1'
                . '&action_origin=AdminLoxboxExpeditionController',
            'action' => self::$cache_lang['Delete'],
        );

        if ($this->specificConfirmDelete !== false) {
            $data['confirm'] = !is_null($this->specificConfirmDelete) ? '\r'.$this->specificConfirmDelete : addcslashes(Tools::htmlentitiesDecodeUTF8(self::$cache_lang['DeleteItem'].$name), '\'');
        }

        $tpl->assign(array_merge($this->tpl_delete_link_vars, $data));

        return $tpl->fetch();
    }
 
 
}
