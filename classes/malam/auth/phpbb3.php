<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

class Malam_Auth_Phpbb3 extends Auth
{
    public function login($username, $password, $remember = FALSE)
    {
        if (empty($password))
            return FALSE;

        return $this->_login($username, $password, $remember);
    }

    protected function _login($user, $password, $remember)
    {
        $return = FALSE;

        if (! is_object($user))
        {
            $username = UTF8::strtolower(UTF8::clean($user));

            /* @var $user Model_Phpbb3_User */
            $user = ORM::factory('phpbb3_user');

            /* @var $user Model_Phpbb3_User */
            $user = $user
                        ->where($user->unique_key($username), '=', $username)
                        ->where('user_type', 'NOT IN', array(USER_INACTIVE, USER_IGNORE))
                        ->find();
        }

        if ($user->loaded())
        {
            if (Phpbb3_Helper::phpbb_check_hash($password, $user->user_password))
            {
                $return = TRUE;

                // Check for old password hash...
                if (strlen($user->user_password) == 32)
                {
                    $hash = Phpbb3_Helper::phpbb_hash($password);
                    $user->set('user_password', $hash);
                    $user->save();
                }

                $this->complete_login($user);
            }
        }

        return $return;
    }

    public function password($username)
    {}

    public function check_password($password)
    {
        $user = $this->get_user();
        return (bool) Phpbb3_Helper::phpbb_check_hash($password, $user->user_password);
    }
}