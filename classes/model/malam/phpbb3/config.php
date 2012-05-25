<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

class Model_Malam_Phpbb3_Config extends ORM
{
    /**
     * Database config group
     * @var String
     */
    protected $_db_group        = 'phpbb3';

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
        $this->_table_name  = CONFIG_TABLE;
        parent::__construct($id);
    }
}