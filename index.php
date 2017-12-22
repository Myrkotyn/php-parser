<?php

use App\Parser;

require __DIR__ . '/vendor/autoload.php';

if (count($argv) <= 2) {
    print "You should enter url address!";

    return;
}
switch ($argv[1]) {
    case "parse":
        $parser = new Parser($argv[2]);
        $parser->start();
        break;
    case "help":
        echo "This is CLI PHP parser. You can use next commands:" . "\n";
        echo " - parse    " . "\n";
        echo "            --- Example: php index.php parse www.example.com" . "\n";
        echo " - report " . "\n";
        echo "            --- Example: php index.php report www.example.com" . "\n";
        echo " - help " . "\n";
        break;
    case "report":
        $parser = new Parser($argv[2]);
        $parser->report();
        break;
    default:
        echo "You can look at list with this command - `php index.php help`";
}
