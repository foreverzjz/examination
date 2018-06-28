<?php
$autoLoadDir = new stdClass();
$autoLoadDir->namespace = [
    'Core' => APP_PATH . "Core" . DS,
    'Controllers' => APP_PATH . 'Controllers' . DS,
    'DataMeta' => APP_PATH . 'DataMeta' . DS,
    'Wrappers' => APP_PATH . 'Wrappers' . DS,
    'Plugins' => APP_PATH . 'Plugins' . DS,
    'Interfaces' => APP_PATH . 'Interfaces' . DS,
    'Library' => APP_PATH . 'Library' . DS,
    'Models' => APP_PATH . 'Models' . DS,
    'Business' => APP_PATH . 'Business' . DS,
    'Tasks' => APP_PATH . 'Tasks' . DS,
    'Rpcs' => APP_PATH . 'Rpcs' . DS,
    'Traits' => APP_PATH . 'traits' . DS,
    'Exceptions' => APP_PATH . 'exceptions' . DS,
    'Helpers' => APP_PATH . 'helpers' . DS,
    'Services' => APP_PATH . 'services' . DS,
    'Tools' => APP_PATH . 'Core/Tools' . DS,
    'Enumerations' => APP_PATH . 'Enumerations' . DS,
    'Network' => TOOLS_PATH . 'Network' . DS,
    'Thrift' => TOOLS_PATH . 'thrift' . DS,    //待清理
    'Queue' => TOOLS_PATH . 'queue' . DS,
    'Tests' => APP_PATH . 'tests' . DS,
];
//$autoLoadDir->dir = [
//    APP_PATH . 'Controllers' . DS,
//];
return $autoLoadDir;
