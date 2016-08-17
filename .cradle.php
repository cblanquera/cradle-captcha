<?php //-->

use Cradle\Framework\Flow;
use Cradle\Framework\Captcha\Controller;

Flow::register('captcha', function() use ($cradle) {
    static $cache = null;

    if(is_null($cache)) {
        $cache = new Controller($cradle);
    }

    return $cache;
});
