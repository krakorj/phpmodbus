<?php

require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// Received bytes interpreting 3 REAL values (6 words)
$data = array( // Ahoj svete!
    0x41,
    0x68,
    0x6F,
    0x6A,
    0x20,
    0x73,
    0x76,
    0x65,
    0x74,
    0x65,
    0x21,
    0x00,
    0x61,
    0x61,
    0x61
);

// Print string interpretation of the values
echo PhpType::bytes2string($data) . "<br>";
echo PhpType::bytes2string($data, true) . "<br>";

?>
