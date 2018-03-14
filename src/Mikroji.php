<?php
namespace OjiSatriani\Router;

use OjiSatriani\Router\RouterosApi;

class Mikroji extends RouterosApi
{
    private static $api;
    private static $interface;
    private $command;
    private $tersambung;
    
    public function __construct($ip = null, $login = null, $password = null, $interface = null)
    {
        try {
            self::$api = $this->connect($ip, $login, $password);
            if($this->konek)
            {
                $this->command      = self::$api->comm("/system/resource/print");
                $this->tersambung   = TRUE;
                if(!empty($interface))
                {
                    self::$api->connect($this->ip, $this->username, $this->password);
                    self::$api->debug = false;
                    self::$api->write("/interface/monitor-traffic",false);
                    self::$api->write("=interface=".$interface,false);  
                    self::$api->write("=once=",true);
                    $baca             = self::$api->read(false);
                    $hasil            = self::$api->parse_response($baca); //return array
                    if(count($hasil)>0) {  
                        self::$interface = $hasil;
                    } else {  
                        self::$interface = array();
                    } 
                }
                return TRUE;
            } else {
                $this->tersambung   = FALSE;
                return FALSE;
            }
        } catch (Exception $e) {
            $this->tersambung   = FALSE;
            return FALSE;
        }
    }

    public static function sambung($ip, $login, $password){
        return new static($ip, $login, $password);
    }

    public function putus()
    {
        self::$api->disconnect();
    }

    public function ram()
    {
        return number_format(($this->command['0']['free-memory']/1048576), 1, '.', '');
    }

    public function hdd()
    {
        return number_format(($this->command['0']['free-hdd-space']/1048576), 1, '.', '');
    }

    public function cpu()
    {
        return $this->command['0']['cpu-load'];
    }

    function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'Kb', 'Mb', 'Gb', 'Tb');   
        $hasil =  round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        return $hasil > 0 ? $hasil:0;
    } 

    function get_mb($size) {
        return sprintf("%4.2f", $size/1048576);
    }

    public function rx()
    {
        return $this->formatBytes(self::$interface[0]["rx-bits-per-second"]);
    }

    public function tx()
    {
        return $this->formatBytes(self::$interface[0]["tx-bits-per-second"]);
    }

    public function rxInMb()
    {
        return $this->get_mb(self::$interface[0]["rx-bits-per-second"]);
    }

    public function txInMb()
    {
        return $this->get_mb(self::$interface[0]["tx-bits-per-second"]);
    }

    public function getInterface(){
        $ARRAY      = self::$api->comm("/interface/print");
        $num        = count($ARRAY);
        $data       = array();
        for($i=0; $i<$num; $i++){
           // $data[] = $ARRAY[$i]['name'];
            $data[$ARRAY[$i]['name']] = $ARRAY[$i]['name'];
        }
        return $data;
    }

    public function defaultInterface(){
        return $this->getInterface[0];
    }

    public function __destruct()
    {
        $this->putus();
    }

}