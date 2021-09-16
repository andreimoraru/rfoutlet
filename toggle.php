<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Edit these codes for each outlet
$codes = array(
    "1" => array(
        "on" => 5330227,
        "off" => 5330236
    ),
    "2" => array(
        "on" => 5330371,
        "off" => 5330380
    ),
    "3" => array(
        "on" => 5330691,
        "off" => 5330700
    ),
    "4" => array(
        "on" => 5332227,
        "off" => 5332236
    ),
    "5" => array(
        "on" => 5338371,
        "off" => 5338380
    ),
);

// Path to the codesend binary (current directory is the default)
$codeSendPath = './codesend';

// This PIN is not the first PIN on the Raspberry Pi GPIO header!
// Consult https://projects.drogon.net/raspberry-pi/wiringpi/pins/
// for more information.
$codeSendPIN = "0";

// Pulse length depends on the RF outlets you are using. Use RFSniffer to see what pulse length your device uses.
$codeSendPulseLength = "185";

if (!file_exists($codeSendPath)) {
    error_log("$codeSendPath is missing, please edit the script", 0);
    die(json_encode(array('success' => false)));
}

$outletLight = $_POST['outletId'];
$outletStatus = $_POST['outletStatus'];

if ($outletLight == "6") {
    // 6 is all 5 outlets combined
    if (function_exists('array_column')) {
        // PHP >= 5.5
        $codesToToggle = array_column($codes, $outletStatus);
    } else {
        $codesToToggle = array();
        foreach ($codes as $outletCodes) {
            array_push($codesToToggle, $outletCodes[$outletStatus]);
        }
    }
} else {
    // One
    $codesToToggle = array($codes[$outletLight][$outletStatus]);
}

foreach ($codesToToggle as $codeSendCode) {
    shell_exec($codeSendPath . ' ' . $codeSendCode . ' -p ' . $codeSendPIN . ' -l ' . $codeSendPulseLength);
    //sleep(1);
}

die(json_encode(array('success' => true)));
?>
