<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

class Model_Malam_Phpbb3_Group extends ORM
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
    protected $_primary_key     = 'group_id';

    /**
     * "Has many" relationships
     * @var array
     */
    protected $_has_many        = array(
        'users'            => array(
            'model'         => 'phpbb3_user',
            'through'       => 'user_group',
            'foreign_key'   => 'group_id',
        ),
        'user_groups'       => array(
            'model'         => 'phpbb3_user_group',
            'foreign_key'   => 'group_id'
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
        $this->_table_name  = GROUPS_TABLE;
        parent::__construct($id);
    }
}