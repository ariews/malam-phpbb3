<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

class Model_Malam_Phpbb3_User_Group extends ORM
{
    /**
     * Database config group
     * @var String
     */
    protected $_db_group        = 'phpbb3';

    /**
     * "Belongs To" relationships
     * @var array
     */
    protected $_belongs_to      = array(
        'phpbb3_user'       => array(
            'model'         => 'phpbb3_user',
            'foreign_key'   => 'user_id',
        ),
        'phpbb3_group'      => array(
            'model'         => 'phpbb3_user',
            'foreign_key'   => 'group_id',
        )
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
        $this->_table_name  = USER_GROUP_TABLE;
        parent::__construct($id);
    }
}