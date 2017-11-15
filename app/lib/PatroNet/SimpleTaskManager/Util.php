<?php

namespace PatroNet\SimpleTaskManager;


class Util
{
    
    public static function yaml($file)
    {
        if (function_exists("yaml_parse_file")) {
            return \yaml_parse_file($file);
        } else if (function_exists("spyc_load_file")) {
            return \spyc_load_file($file);
        } else {
            throw new \RuntimeException("No driver found for parsing YAML files");
        }
    }
    
}