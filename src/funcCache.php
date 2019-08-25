<?php
namespace x51\functions;

class funcCache{

    public static function calcCacheKey(...$param) {
        if (!empty($param)) {
            return md5(serialize($param));
        }
        return false;
    }
    
} // end class