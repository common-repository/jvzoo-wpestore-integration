<?php

class Ss_Jvzoo_Wpestore_Config

{
    var $configs;
    static $_this;

    function loadConfig()
    {
        $this->configs = get_option('ss_jvzoo_wpestore_config_v2');
        if (empty($this->configs)) {
            $ss_jvzoo_wpestore_raw_configs = get_option('ss_jvzoo_wpestore_config');
            if (is_string($ss_jvzoo_wpestore_raw_configs)) {
                $this->configs = unserialize($ss_jvzoo_wpestore_raw_configs);
            } else {
                $this->configs = unserialize((string)$ss_jvzoo_wpestore_raw_configs);
            }
        }

        if (empty($this->configs)) {
            $this->configs = array();
        }//This a brand new install site with no config data so initilize with a new array
    }

    function getValue($key)
    {
        return isset($this->configs[$key]) ? $this->configs[$key] : '';
    }

    function setValue($key, $value)
    {
        $this->configs[$key] = $value;
    }

    function saveConfig()
    {
        update_option('ss_jvzoo_wpestore_config', serialize($this->configs));
        update_option('ss_jvzoo_wpestore_config_v2', $this->configs);
    }

    function addValue($key, $value)
    {
        if (array_key_exists($key, $this->configs)) {
            //Don't update the value for this key
        } else {
            //It is save to update the value for this key
            $this->configs[$key] = $value;
        }
    }

    static function getInstance()
    {
        if (empty(self::$_this)) {
            self::$_this = new Ss_Jvzoo_Wpestore_Config();
            self::$_this->loadConfig();
            return self::$_this;
        }
        return self::$_this;
    }
}


class Ss_Jvzoo_Wpestore_Config_Helper
{
    static function add_options_config_values()
    {
        $Ss_Jvzoo_Wpestore_Config = Ss_Jvzoo_Wpestore_Config::getInstance();
        $Ss_Jvzoo_Wpestore_Config->addValue('success_message', '<h2 style="text-align: center; " class ="thank-you">Thank you for your purchase!</h2>
<img class="success-image" style="border-color: #376a6e; margin: 0 auto;" title="checkout" src="https://www.wpdating.com/wp-content/uploads/2016/08/checkout.jpg" alt="checkout" />
<h2 style="text-align: center;" class="email-check">Please check your email for more details</h2>
<h2 style="text-align: center;" class="order"><em><strong>WAIT... Your order is not quite finished yet.</strong></em></h2>');
        $Ss_Jvzoo_Wpestore_Config->addValue('error_message', '<div class="payment-error">
<span class="oops" style="text-align: center;">Oops! </span>
<div class="some-problem">There seems to be some problem.</div><div class="send-email">We\'re working on it and we\'ll get it fixed as soon as possible.
Please send an email to contact@wpdating.com</div></div>');
        $Ss_Jvzoo_Wpestore_Config->saveConfig();
    }
}

