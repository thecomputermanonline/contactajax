<?php
/**
 * File: Translator.php
 * User: mofasa
 * Date: 26/02/15 14:27
 */

namespace Ipedis;

class Translator{
    const TRANSLATEPATH = "translate/";
    protected static $_instance = null;
    public static function getInstance(){
        if(empty(self::$_instance)){
            $singletonClass = get_called_class(); //Determine children class !
            self::$_instance = new $singletonClass();
        }
        return self::$_instance;
    }

    protected $_translates = null;
    public function translate($key, $lang = "en"){
        if(!$this->hasTranslates())
            $this->getTranslates($lang);
        if(empty($this->_translates[$key]))
            throw new RouterException("key : $key is not translate.",404);
        return $this->_translates[$key];
    }

    protected function hasTranslates(){
        return !empty($this->_translates);
    }

    protected function getTranslates($lang){
        if(!is_file(self::TRANSLATEPATH.$lang.".php"))
            throw new \Ipedis\RouterException("This lang is not managed",409);
        $this->_translates = include(self::TRANSLATEPATH.$lang.".php");
    }
}