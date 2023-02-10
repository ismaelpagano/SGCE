<?php

    if (isset($_SERVER["REMOTE_ADDR"]) || isset($_SERVER["HTTP_USER_AGENT"]) || !isset($_SERVER["argv"])) {
    exit("Please run this script from command line");
    }

    $cycle = 10;

    while (true) {
    echo "It works!" . PHP_EOL;
    sleep($cycle);
    }

?>