<?php
/**
 * File: Sanitarize.php
 * User: mofasa
 * Date: 26/02/15 14:26
 */

namespace Ipedis;


class Sanitarize{
    public static function string($string){
        return strip_tags(trim($string));
    }
}