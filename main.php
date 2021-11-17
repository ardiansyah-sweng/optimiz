<?php
require 'vendor/autoload.php';

$optimizer = 'pso';
$function = 'ucp';
$goal = 'min';
$executionTypes = [
    [1],
    [2, 'optimizer' => $optimizer, 'function' => $function],
    [3, 'optimizer' => $optimizer],
    [4, 'function' => $function],
];

$mainProcessor = new MainProcessorFactory;
$type = 2;
$mainExecution = $mainProcessor->initializeMainProcessor($executionTypes[$type - 1]);
$mainExecution->executionMain($executionTypes[$type - 1], $goal);

## evaluation, convergence
## goal: min, max