<?php

class Helper 
{   
    public static function sortLink($col, $init = 0) 
    {
        $param = self::parseLink();
        
        if (isset($param['sortBy']) && $param['sortBy'] === $col) {
            $param['dir'] = mb_strtolower($param['dir']) === 'asc' ? 'desc' : 'asc';
        } else {            
            $param['sortBy'] = $col;
            $param['dir']    = 'asc';
        }
                
        if ($init) {
            $param['sortBy'] = 'sumUSE';
            $param['dir']    = 'desc';
        }
        
        return http_build_query($param, null, '&', PHP_QUERY_RFC3986);
    }
    
    public static function paginatorLink($page)
    {
        $param = self::parseLink();
        $param['page'] = $page;    
        
        return http_build_query($param);
    }
    
    public static function parseLink()
    {
        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);            
        $keys  = explode("&", $query);
        $param = [];
        
        if ($query) {
            foreach ($keys as $value) {
                $temp = explode("=", $value);
                $param[$temp[0]] = $temp[1];
            }            
        }
        return $param;
    }
    
    public static function mb_str_replace($find, $replace, $str) 
    { 
        $offset = 0;
        while (true) {
            $i = mb_stripos($str, $find, $offset);
            if ($i === false) {return $str;}
            $str = mb_substr($str, 0, $i)
                  . $replace
                  . mb_substr($str, $i + mb_strlen($find)); 
            $offset = mb_strlen(mb_substr($str, 0, $i)) 
                    + mb_strlen($replace);        
        }
    }
}
