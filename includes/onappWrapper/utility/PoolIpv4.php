<?php

/* * ********************************************************************
 * onapp product developed. (2017-06-02)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */
namespace onappWrapper\utility;

/**
 * Description of PoolIpv4
 *
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @version 1.0.0
 */
class PoolIpv4 {

    private $address;
    private $networkMask;
    private $limit;
    private $startAddress;
    private $endAddress;
    private $usedHosts = [];
    private $hosts = [];

    public function __construct($address, $networkMask) {
        $this->address = $address;
        $this->networkMask = $networkMask;
    }

    public function getHosts() {
        return $this->hosts;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function usedHosts(array $usedHosts) {
        $this->usedHosts = $usedHosts;
        return $this;
    }

    public function compute() {
        $this->hosts = [];
        $mask = $this->networkMask;
        if($this->startAddress){
            $ip_enc = ip2long($this->startAddress);
        }else{
            $ip_enc = ip2long($this->address);
        }
        //convert last (32-$mask) bits to zeroes
        $curr_ip = $ip_enc | pow(2, (32 - $mask)) - pow(2, (32 - $mask));
        $ips = array();
        $ip_nmask = self::translateBitmaskToNetmask($this->networkMask);
        $ip_address_long = $ip_enc;
        $ip_nmask_long = ip2long($ip_nmask);
        //caculate network address
        $ip_net = $ip_address_long & $ip_nmask_long;
        //caculate first usable address
        $ip_host_first = ( (~$ip_nmask_long) & $ip_address_long);
        $ip_first = ($ip_address_long ^ $ip_host_first) + 1;
        //caculate last usable address
        $ip_broadcast_invert = ~$ip_nmask_long;
        $ip_last = ($ip_address_long | $ip_broadcast_invert) - 1;
        //caculate broadcast address
        $ip_last = ($ip_address_long | $ip_broadcast_invert) - 1;
        $ip_last_short = long2ip($ip_last);
        $i = 0;
        for ($pos = 0; $pos < pow(2, (32 - $mask)); ++$pos) {
            $ip = long2ip((float) $curr_ip + $pos);
            if($this->endAddress && $ip == $this->endAddress){
                break;
            }
            $this->hosts[] = $ip;
            if ($ip == $ip_last_short || ($this->limit && $this->limit == count($this->hosts)))
                break;
            
            $i++;
        }
        return $this;
    }
    public function getStartAddress() {
        return $this->startAddress;
    }

    public function getEndAddress() {
        return $this->endAddress;
    }

    public function setStartAddress($startAddress) {
        $this->startAddress = $startAddress;
        return $this;
    }

    public function setEndAddress($endAddress) {
        $this->endAddress = $endAddress;
        return $this;
    }

    
    private static function translateBitmaskToNetmask($bitmask) {
        $maskMap = array(
            0 => "0.0.0.0",
            1 => "128.0.0.0",
            2 => "192.0.0.0",
            3 => "224.0.0.0",
            4 => "240.0.0.0",
            5 => "248.0.0.0",
            6 => "252.0.0.0",
            7 => "254.0.0.0",
            8 => "255.0.0.0",
            9 => "255.128.0.0",
            10 => "255.192.0.0",
            11 => "255.224.0.0",
            12 => "255.240.0.0",
            13 => "255.248.0.0",
            14 => "255.252.0.0",
            15 => "255.254.0.0",
            16 => "255.255.0.0",
            17 => "255.255.128.0",
            18 => "255.255.192.0",
            19 => "255.255.224.0",
            20 => "255.255.240.0",
            21 => "255.255.248.0",
            22 => "255.255.252.0",
            23 => "255.255.254.0",
            24 => "255.255.255.0",
            25 => "255.255.255.128",
            26 => "255.255.255.192",
            27 => "255.255.255.224",
            28 => "255.255.255.240",
            29 => "255.255.255.248",
            30 => "255.255.255.252",
            31 => "255.255.255.254",
            32 => "255.255.255.255"
        );

        return isset($maskMap[$bitmask]) ? $maskMap[$bitmask] : $bitmask;
    }

}

/**
 * Example
 *  

$ipPool = new PoolIpv4('10.74.0.0', '24');
$hosts = $ipPool->usedHosts(['10.74.0.3','10.74.0.4','10.74.0.6','10.74.0.2','10.74.0.7'])->limit(2)->compute()->getHosts();
print_r($hosts);
 */

