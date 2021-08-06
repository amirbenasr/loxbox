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
        $user_id = $this->context->cart->id_customer;
        if(Tools::getValue('ajax') && Tools::getValue("product_id")){
            
            $carrier_id = (int)Tools::getValue("product_id");
            $carrier = Carrier::getCarrierByReference($carrier_id);
          

          
          
          
            // $id_customer=2;
            // $sql1 = "SELECT * FROM ps_address WHERE id_customer=$id_customer and  id_address=(SELECT max(id_address) FROM ps_address);";
            // $sql = "SELECT COUNT(*) FROM ps_address WHERE id_customer=$id_customer and  id_address=(SELECT max(id_address) FROM ps_address);";

            // $last_address = $db->getRow($sql1);
            // $new_address = new Address();
            // $new_address->hydrate($last_address);
            // $addresses = $db->getVAlue($sql);
            // var_dump($db->getValue($sql3));
            // die;
            // if($addresses!=0)
            // {

            // $sql3 = "UPDATE `ps_address` SET `alias` = \'loxlox`', `address1` = 'azdazd', `city` = 'zz', `other` = 'bb' WHERE `ps_address`.`id_address` = 6;";

               
            // }
            $json = array(
                'status' => 'error',
                'message' => $carrier,
                'token'=>Configuration::get('Loxbox')
            );
            
        }
        else if (Tools::getValue('address1') && Tools::getValue('ajax'))
        {
           $name="Loxbox";
            $db = Db::getInstance();
            $sql = "SELECT * from ps_customer where id_customer=$user_id";
            $row = $db->getRow($sql);
            $user = new Customer();
            $user->getCustomersByEmail($row->email);
            $sql = "SELECT COUNT(*) FROM ps_address WHERE id_customer=$user_id and other=$name and id_address=(SELECT max(id_address) FROM ps_address);";
            $count = $db->getValue($sql);

            if( $count !=0 )
            {
                $db->update('address',array(
                
                    'alias'=>Tools::getValue('Name'),
                    'address1'=>Tools::getValue('address1'),
                    'city'=>Tools::getValue('City'),
                    'postcode'=>Tools::getValue('Zipcode')
                ),'id_customer='.(int)$user_id).' other='.$name.'';

            }
            else if($count==0) {
                $db->insert('address',array(
                    'alias'=>Tools::getValue('Name'),
                    'address1'=>Tools::getValue('address1'),
                    'firstname'=>$user->firstname,
                    'lastname'=>$user->lastname,
                    'firstname'=>$user->lastname,
                    'city'=>Tools::getValue('City'),
                    'id_country'=>210,
                    'other'=>$name,
                    'postcode'=>Tools::getValue('Zipcode')
                ),'id_customer='.(int)$user_id);
            }
            
        }
        
        

     
         die(Tools::jsonEncode($json));
    }


}