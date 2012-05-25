<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

class Model_Malam_Phpbb3_User extends ORM
{
    /**
     * Database config group
     * @var String
     */
    protected $_db_group        = 'phpbb3';

    /**
     * Table primary key
     * @var string
     */
    protected $_primary_key     = 'user_id';

    /**
     * "Has many" relationships
     * @var array
     */
    protected $_has_many        = array(
        'groups'            => array(
            'model'         => 'phpbb3_group',
            'through'       => 'user_group',
            'foreign_key'   => 'user_id',
        ),
        'user_groups'       => array(
            'model'         => 'phpbb3_user_group',
            'foreign_key'   => 'user_id'
        ),
    );

    /**
     * "Has one" relationships
     * @var array
     */
    protected $_has_one         = array(
        'user'              => array(
            'model'         => 'user',
            'foreign_key'   => 'bb_user_id'
        ),
    );

    /**
     * Constructs a new model and loads a record if given
     *
     * +mod: dynamyc $_db_group
     *
     * @param   mixed $id Parameter for find or object to load
     * @return  void
     */
    public function __construct($id = NULL)
    {
        $this->_table_name  = USERS_TABLE;
        parent::__construct($id);
    }

    /**
     * Allows a model use both email and username as unique identifiers for login
     *
     * @param   string  unique value
     * @return  string  field name
     */
    public function unique_key($value)
    {
        return Valid::email($value) ? 'user_email' : 'username_clean';
    }
}