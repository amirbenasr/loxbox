<?php

class LoxboxTaskModuleFrontController extends ModuleFrontControllerCore
{

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
        $json ;
        $user_id = $this->context->cart->id_customer;
        if (Tools::getValue('ajax') && Tools::getValue("product_id")) {
            $carrier_id = (int)Tools::getValue("product_id");
            $carrier = new Carrier();
            $db = Db::getInstance();
            $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'carrier` WHERE id_carrier=' . (int)$carrier_id;

            $carrier->hydrate($db->getRow($query));

            $json = array(
                'status' => 'error',
                'message' => $carrier,
                'token' => Configuration::get('Loxbox'),
            );

        } elseif (Tools::getValue('address1') && Tools::getValue('ajax')) {
            $name = "Loxbox";
            $db = Db::getInstance();
            $sql = 'SELECT * from `' . _DB_PREFIX_ . 'customer` where id_customer=' . (int)$user_id;
            $row = $db->getRow($sql);
            $user = new Customer();

            $users = $user->getCustomersByEmail($row['email']);

            $user->hydrate($users[0]);

            $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'address` WHERE id_customer=' .(int) $user_id . ' and deleted=0 and other="' . $name . '" and id_address=(SELECT max(id_address) FROM `' . _DB_PREFIX_ . 'address`);';
            $count = $db->getValue($sql);

            if ($count != 0) {

                $sql = 'SELECT MAX(id_address) from `' . _DB_PREFIX_ . 'address` where id_customer=' . (int) $user_id  ;
                $last_address_id = $db->getValue($sql);
                Configuration::updateValue('loxboxRelayId', Tools::getValue('idRelay') ?? 15);
                $address = new Address($last_address_id);

                $db->update('address', array(
                    'alias' => Tools::getValue('Name'),
                    'address1' => Tools::getValue('address1'),
                    'city' => Tools::getValue('City'),
                    'postcode' => Tools::getValue('Zipcode'),
                    'id_country'=>(int) 208,
                    'phone_mobile'=>$address->phone_mobile ?? '',
                    'phone'=>$address->phone ?? '',
                    'date_upd'=>date("Y-m-d H:i:s")


                ), 'id_address=' . (int)$last_address_id) . '';
                $json = array('update'=>'success');

            } elseif ($count == 0) {
                $query ="SELECT id_address from `"._DB_PREFIX_."address` WHERE id_customer=" . (int) $user_id  ;
                $count = $db->getValue($query);

                $address = new Address($count);
                $db->insert('address', array(
                    'id_customer' => $user->id,
                    'dni' => '',
                    'alias' => Tools::getValue('Name'),
                    'address1' => Tools::getValue('address1'),
                    'address2' => $user->address2 ?? "",
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'city' => Tools::getValue('City'),
                    'id_country' => 208,
                    'id_state' => 0,
                    'other' => $name,
                    'postcode' => Tools::getValue('Zipcode'),
                    'company'=>'',
                    'phone_mobile'=>$address->phone_mobile ?? '',
                    'phone'=>$address->phone ?? '',
                    'vat_number'=>$user->vat_number ?? '',
                    'date_add'=>date("Y-m-d H:i:s")

                ));
                $json = array(
                    'status' => 'error',
                    'name' => $name,
                    'alias' => Tools::getValue('Name'),
                    'address1' => Tools::getValue('address1'),
                    'user' => $user,
                    'lastname' => $user->lastname,
                    'id_customer' => (int)$user_id,
                    'city' => Tools::getValue('City'),
                    'id_country' => 208,
                    'other' => $name,
                    'sql' => $sql,
                    'postcode' => Tools::getValue('Zipcode'),
                    'token' => Configuration::get('Loxbox'),
                );
            }

        }

        die(Tools::jsonEncode($json));
    }

}
