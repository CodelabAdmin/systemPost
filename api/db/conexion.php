<?php
class conn extends mysqli
{
    function __construct()
    {
        // Evitamos warnings de conexión
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
        
        parent::__construct(
            "junction.proxy.rlwy.net",
            "root", 
            "MhXMiQRTcctebVLvEEgthZpRgdSriHKQ",
            "railway", 
            25354
        );

        if ($this->connect_error) {
            die(json_encode(['error' => 'Error de conexión: ' . $this->connect_error]));
        }

        $this->set_charset("utf8");
    }
}

$conn = new conn();
?>