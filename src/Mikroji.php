<?php
namespace OjiSatriani\MikrotikApi;

/*****************************
 *
 * Mikrotik API - Mikroji
 * Author: Oji Satriani - @ojisatriani
 * http://www.mikrotik.com
 * http://wiki.mikrotik.com/wiki/API_PHP_class
 *
 ******************************/

use OjiSatriani\MikrotikApi\RouterosApi;

class Mikroji extends RouterosApi
{
    private $api;
    private $interface;
    private $command;
    private $tersambung;
    
    public function __construct($ip = null, $login = null, $password = null)
    {
        try {
            $this->api = $this->_connect($ip, $login, $password);
            if($this->api)
            {
                $this->command      = $this->comm("/system/resource/print");
                $this->tersambung   = TRUE;
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

    public static function connect($ip, $login, $password){
        return new static($ip, $login, $password);
    }

    public function putus()
    {
        $this->disconnect();
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

    public function rx($interface)
    {
        return $this->formatBytes($this->setInterface($interface)["rx-bits-per-second"]);
    }

    public function tx($interface)
    {
        return $this->formatBytes($this->setInterface($interface)["tx-bits-per-second"]);
    }

    public function rxInMb($interface)
    {
        return $this->get_mb($this->setInterface($interface)["rx-bits-per-second"]);
    }

    public function txInMb($interface)
    {
        return $this->get_mb($this->setInterface($interface)["tx-bits-per-second"]);
    }

    public function setInterface($interface){
        $this->debug        = false;
        $this->write("/interface/monitor-traffic",false);
        $this->write("=interface=".$interface,false);  
        $this->write("=once=",true);
        $baca               = $this->read(false);
        $hasil              = $this->parseResponse($baca); //return array
        if(count($hasil)>0) {  
            return $hasil[0];
        } else {  
            $this->interface = array();
        } 
    }

    public function getInterface(){
        $ARRAY      = $this->comm("/interface/print");
        $num        = count($ARRAY);
        $data       = array();
        for($i=0; $i<$num; $i++){
        //    $data[] = $ARRAY[$i]['name'];
            $data[$ARRAY[$i]['name']] = $ARRAY[$i]['name'];
        }
        return $data;
    }

    public function __destruct()
    {
        $this->putus();
    }

}