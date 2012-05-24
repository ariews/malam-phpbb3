<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * user password tools, steal from phpbb3
 * @see phpBB3/includes/functions.php
 */

class Phpbb3_Helper
{
    private static $_config = array();

    /**
     * Check for correct password
     *
     * @param string $password The password in plain text
     * @param string $hash The stored password hash
     *
     * @return bool Returns true if the password is correct, false if not.
     */
    public static function phpbb_check_hash($password, $hash)
    {
        $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        if (strlen($hash) == 34)
        {
            return (self::_hash_crypt_private($password, $hash, $itoa64) === $hash) ? true : false;
        }

        return (md5($password) === $hash) ? true : false;
    }

    /**
     * The crypt function/replacement
     */
    private static function _hash_crypt_private($password, $setting, &$itoa64)
    {
        $output = '*';

        // Check for correct hash
        if (substr($setting, 0, 3) != '$H$' && substr($setting, 0, 3) != '$P$')
        {
            return $output;
        }

        $count_log2 = strpos($itoa64, $setting[3]);

        if ($count_log2 < 7 || $count_log2 > 30)
        {
            return $output;
        }

        $count = 1 << $count_log2;
        $salt = substr($setting, 4, 8);

        if (strlen($salt) != 8)
        {
            return $output;
        }

        if (PHP_VERSION >= 5)
        {
            $hash = md5($salt . $password, true);
            do
            {
                $hash = md5($hash . $password, true);
            }
            while (--$count);
        }
        else
        {
            $hash = pack('H*', md5($salt . $password));
            do
            {
                $hash = pack('H*', md5($hash . $password));
            }
            while (--$count);
        }

        $output = substr($setting, 0, 12);
        $output .= self::_hash_encode64($hash, 16, $itoa64);

        return $output;
    }

    /**
     * Encode hash
     */
    private static function _hash_encode64($input, $count, &$itoa64)
    {
        $output = '';
        $i = 0;

        do
        {
            $value = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3f];

            if ($i < $count)
            {
                $value |= ord($input[$i]) << 8;
            }

            $output .= $itoa64[($value >> 6) & 0x3f];

            if ($i++ >= $count)
            {
                break;
            }

            if ($i < $count)
            {
                $value |= ord($input[$i]) << 16;
            }

            $output .= $itoa64[($value >> 12) & 0x3f];

            if ($i++ >= $count)
            {
                break;
            }

            $output .= $itoa64[($value >> 18) & 0x3f];
        }
        while ($i < $count);

        return $output;
    }

    /**
     *
     * @version Version 0.1 / slightly modified for phpBB 3.0.x (using $H$ as hash type identifier)
     *
     * Portable PHP password hashing framework.
     *
     * Written by Solar Designer <solar at openwall.com> in 2004-2006 and placed in
     * the public domain.
     *
     * There's absolutely no warranty.
     *
     * The homepage URL for this framework is:
     *
     *    http://www.openwall.com/phpass/
     *
     * Please be sure to update the Version line if you edit this file in any way.
     * It is suggested that you leave the main version number intact, but indicate
     * your project name (after the slash) and add your own revision information.
     *
     * Please do not change the "private" password hashing method implemented in
     * here, thereby making your hashes incompatible.  However, if you must, please
     * change the hash type identifier (the "$P$") to something different.
     *
     * Obviously, since this code is in the public domain, the above are not
     * requirements (there can be none), but merely suggestions.
     *
     *
     * Hash the password
     */
    public static function phpbb_hash($password)
    {
        $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        $random_state   = self::unique_id();
        $random         = '';
        $count          = 6;

        if (($fh = @fopen('/dev/urandom', 'rb')))
        {
            $random = fread($fh, $count);
            fclose($fh);
        }

        if (strlen($random) < $count)
        {
            $random = '';
            for ($i = 0; $i < $count; $i += 16)
            {
                $random_state = md5(self::unique_id() . $random_state);
                $random .= pack('H*', md5($random_state));
            }
            $random = substr($random, 0, $count);
        }

        $hash = self::_hash_crypt_private($password, self::_hash_gensalt_private($random, $itoa64), $itoa64);

        if (strlen($hash) == 34)
        {
            return $hash;
        }

        return md5($password);
    }

    /**
     * Generate salt for hash generation
     */
    private static function _hash_gensalt_private($input, &$itoa64, $iteration_count_log2 = 6)
    {
        if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31)
        {
            $iteration_count_log2 = 8;
        }

        $output = '$H$';
        $output .= $itoa64[min($iteration_count_log2 + ((PHP_VERSION >= 5) ? 5 : 3), 30)];
        $output .= self::_hash_encode64($input, 6, $itoa64);

        return $output;
    }

    /**
     * Return unique id
     * @param string $extra additional entropy
     */
    public static function unique_id($extra = 'c')
    {
        static $dss_seeded = FALSE;

        $rand_seed = self::get_config('rand_seed');
        if (NULL === $rand_seed)
        {
            $rand_seed = md5(microtime());
            self::set_config('rand_seed', $rand_seed, TRUE);
            self::set_config('rand_seed_last_update', time(), TRUE);
        }

        $val = $rand_seed . microtime();
        $val = md5($val);
        self::$_config['rand_seed'] = md5($rand_seed . $val . $extra);

        $rand_seed_last_update = self::get_config('rand_seed_last_update');

        if ($dss_seeded !== TRUE && ($rand_seed_last_update < time() - rand(1,10)))
        {
            self::set_config('rand_seed', self::$_config['rand_seed'], TRUE);
            self::set_config('rand_seed_last_update', time(), TRUE);
            $dss_seeded = TRUE;
        }

        return substr($val, 4, 16);
    }

    /**
     * Set config value. Creates missing config entry.
     */
    public static function set_config($config_name, $config_value, $is_dynamic = false)
    {
        $cfg = ORM::factory('phpbb3_config')
                ->where('config_name', '=', $config_name)
                ->find();

        if (! $cfg->loaded())
        {
            $cfg = ORM::factory('phpbb3_config');
        }

        $cfg->values(array(
            'config_name'   => $config_name,
            'config_value'  => $config_value,
            'is_dynamic'    => $is_dynamic
        ));

        try {
            $cfg->save();
            self::$_config[$config_name] = $config_value;
        }
        catch (Validate_Exception $e)
        {}
        catch (Database_Exception $e)
        {}
    }

    /**
     * Get configuration from database
     */
    public static function get_config($config_name, $default_return = NULL)
    {
        if (! isset(self::$_config[$config_name]))
        {
            return $default_return;
        }
        return self::$_config[$config_name];
    }
}