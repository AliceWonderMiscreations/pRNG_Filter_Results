#!/usr/bin/env php
<?php

set_time_limit(0);

$outputdir = '/home/alice/rndTest/';

function shitty_random_32_bytes() {
    $rand = rand(0,1);
    if($rand === 0) {
        $return = '0000000000000000000000000000000000000000000000000000000000000000';
    } else {
        $return = 'ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff';
    }
    return hex2bin($return);
}

function crappy_random_32_bytes(int $whatever=0)
{
    // generates a very poor pattern obvious 32 byte number one byte at a time
    $return = '';
    static $seed = null;
    if(is_null($seed)) {
        if ($whatever === 0) {
            $whatever = random_int(0,52);
        }
        $seed = ($whatever % 53);
    } else {
        $seed = $seed + 7;
    }
    for($i=0; $i<32; $i++) {
        $seed = $seed + 67;
        if($seed >= 256) {
            $seed = $seed - 256;
        }
        $byte = dechex($seed);
        $byte = str_pad($byte, 2, '0', STR_PAD_LEFT);
        $return .= $byte;
    }
    return hex2bin($return);
}

function proper_random_32_bytes()
{
    // on Linux this comes from /dev/urandom which is a non-blocking CSPRNG
    return random_bytes(32);
}

function filter_results($rnd) {
    static $salt = null;
    static $nonce = null;
    static $counter = 0;
    if(is_null($salt)) {
        $salt = '';
    }
    if(is_null($nonce)) {
        $nonce = random_bytes(16);
    } else {
        sodium_increment($nonce);
    }
    $rawhash = hash('sha256', $salt . $nonce, true);
    $res = $rnd ^ $rawhash;
    $counter++;
    if($counter === 32) {
        $str = $res . $rnd . $nonce . $salt;
        $hash = hash('sha384', $str, true);
        $salt = base64_encode($hash);
        $counter = 0;
    }
    return $res;
}

$shitRandom = $outputdir . 'shitRandom.bin';
$shitRandomFiltered = $outputdir . 'shitRandomFiltered.bin';

$weakRandom = $outputdir . 'weakRandom.bin';
$weakRandomFiltered = $outputdir . 'weakRandomFiltered.bin';

$goodRandom = $outputdir . 'goodRandom.bin';
$goodRandomFiltered = $outputdir . 'goodRandomFiltered.bin';

for($q=0; $q<=4096; $q++) {

    $fshit = fopen($shitRandom, 'a');
    $fshitFiltered = fopen($shitRandomFiltered, 'a');
    $fweak = fopen($weakRandom, 'a');
    $fweakFiltered = fopen($weakRandomFiltered, 'a');
    $fgood = fopen($goodRandom, 'a');
    $fgoodFiltered = fopen($goodRandomFiltered, 'a');

    for($i=0; $i<=32768; $i++) {
        $rnd = shitty_random_32_bytes();
        fwrite($fshit, $rnd);
        $rnd = filter_results($rnd);
        fwrite($fshitFiltered, $rnd);
        $rnd = crappy_random_32_bytes();
        fwrite($fweak, $rnd);
        $rnd = filter_results($rnd);
        fwrite($fweakFiltered, $rnd);
        $rnd = proper_random_32_bytes();
        fwrite($fgood, $rnd);
        $rnd = filter_results($rnd);
        fwrite($fgoodFiltered, $rnd);
    }
    // probably not needed but it lets me watch the size grow.
    fclose($fshit);
    fclose($fshitFiltered);
    fclose($fweak);
    fclose($fweakFiltered);
    fclose($fgood);
    fclose($fgoodFiltered);    
    sleep(2);
}


?>