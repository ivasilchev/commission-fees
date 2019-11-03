<?php

require_once('vendor/autoload.php');

$app = new \Ivan\Application\CommissionFeeApplication(new \Ivan\Application\Configuration);

try {
    $outputArray = $app->runWithInputFile($argv[1]);

    foreach ($outputArray as $output) {
        printf("%s\n", $output);
    }
} catch (\Exception $e) {
    prtintf("Sorry an error has occured.\nError:%s\nPlease try again\n.", $e->getMessage());
}
