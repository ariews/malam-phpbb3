<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

return array(
    'phpbb3' => array(
        'type'       => 'mysql',
        'connection' => array(
            'hostname'   => 'localhost',
            'database'   => 'phpbb3',
            'username'   => 'root',
            'password'   => 'password',
            'persistent' => FALSE,
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
        'caching'      => FALSE,
        'profiling'    => TRUE,
    ),
);