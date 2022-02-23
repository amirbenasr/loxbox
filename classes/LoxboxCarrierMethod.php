<?php


class LoxboxCarrierMethod extends ObjectModel
{

    /** @var int $id_carrier The id of the associated Prestashop carrier */
    public $id_carrier;

    /** @var string $collection_mode See Webservice 'ModeLiv' field */
    public $delivery_mode;

    /** @var string $insurance_level See Webservice 'Assurance' field */
    public $insurance_level;

    /** @var int $id_carrier 0/1 : Was the carrier deleted ? We need to keep it
     * for history purposes.
     */
    public $is_deleted;

    /**
     * @var int $id_reference Id carrier reference
     */
    public $id_reference;

    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table'   => 'loxbox_carrier',
        'primary' => 'id_loxbox_carrier',
        'fields'  => array(
            'id_carrier'      => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'delivery_mode'   => array('type' => self::TYPE_STRING, 'values' => array('24R', 'DRI', 'LD1', 'LDS', 'HOM'), 'required' => true, 'size' => 3),
            'is_deleted'      => array('type' => self::TYPE_BOOL, 'default' => 0, 'validate' => 'isBool'),
            'id_reference'    => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add'        => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd'        => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );
}