<?php
namespace Optimizers\Pso;
require 'vendor/autoload.php';

use Functions\FunctionFactory;
use Utils\Randomizers;
use Utils\Stopper;
use Utils\OptimizerHelper;
use Results\ResultStore;

class ParticleSwarmOptimizer
{
    function solutionIsFound($stoppingValue, $functionResult)
    {
        if ($functionResult <= $stoppingValue) {
            return true;
        }
    }

    function createInitialVelocities($initialPopulation, $initialVelocity)
    {
        $results = [];
        foreach ($initialPopulation as $variable) {
            $results[] = floatval($variable) * $initialVelocity;
        }
        return $results;
    }

    function inertiaWeighting($iter, $parameters)
    {
        return $parameters['inertiaMax'] - ((($parameters['inertiaMax'] - $parameters['inertiaMin']) * $iter) / $parameters['maxIteration']);
    }

    function calcNewVariable($variables, $velocities)
    {
        foreach ($variables as $key => $variable) {
            $newVariables[] = floatval($variable) + $velocities[$key];
        }
        return $newVariables;
    }

    function updateParticles($oldParticles, $velocities, $generalSetups, $iter)
    {
        $newVariables = [];
        foreach ($oldParticles as $key => $oldVariable) {
            if ($iter === 0) {
                $newVariables[] = floatval($oldVariable) + $velocities[$key];
            }
            if ($iter > 0) {
                $newVariables[] = $oldVariable + $velocities[$key];
            }
        }
        $functionResult = (new FunctionFactory())->initializingFunction($generalSetups['functions'])->runFunction($newVariables, $generalSetups['functions']);
        return [
            'fitnessValue' => $functionResult,
            'variables' => $newVariables
        ];
    }

    function getGbest($pbests, $fitnessType)
    {
        if ($fitnessType === 'min') {
            $min = min(array_column($pbests, 'fitnessValue'));
            $index = array_search($min, array_column($pbests, 'fitnessValue'));
        }
        if ($fitnessType === 'max') {
            $max = max(array_column($pbests, 'fitnessValue'));
            $index = array_search($max, array_column($pbests, 'fitnessValue'));
        }
        return $pbests[$index];
    }

    function calcVelocity($w, $velocities, $r1, $r2, $parameters, $pbests, $gbests, $particles, $variableRanges)
    {
        $c1_x_r1 = $parameters['c1'] * $r1;
        $c2_x_r2 = $parameters['c2'] * $r2;

        foreach ($velocities as $key => $velocity) {
            $w_x_velocity = $w * $velocity;
            $pbest_particle = floatval($pbests[$key]) - floatval($particles[$key]);
            $gbest_particle = floatval($gbests[$key]) - floatval($particles[$key]);
            $newVelocity = $w_x_velocity + (($c1_x_r1 * $pbest_particle) + ($c2_x_r2 * ($gbest_particle)));

            ## Refactor: different treatment to testfunction and estimation range
            $vMax = $variableRanges['upperBound'] - ($variableRanges['lowerBound']);
            if ($newVelocity > $vMax) {
                $newVelocity = $vMax;
            }

            $ret[] = $newVelocity;
        }
        return $ret;
    }

    function personalBest($pbests, $currentParticles)
    {
        if ($pbests['fitnessValue'] > $currentParticles['fitnessValue']) {
            return $currentParticles;
        } else {
            return $pbests;
        }
    }

    function getBestOfGbests($gbests)
    {
        $globalBest = min(array_column($gbests, 'fitnessValue'));
        return $gbests[array_search($globalBest, array_column($gbests, 'fitnessValue'))];
    }

    function optimizing($parameters, $initialPopulation,$generalSetups, $goal)
    {
        // Initializing
        $initialVelocity = (new Randomizers())->randomZeroToOneFraction();
        $iter = 0;
        foreach ($initialPopulation as $particleNo => $variables) {
            $velocities[$iter][$particleNo] = $this->createInitialVelocities(explode(",", $variables), $initialVelocity);

            $particles[$iter][$particleNo] = $this->updateParticles(explode(",", $variables), $velocities[$iter][$particleNo], $generalSetups, $iter);

            $pbests[$iter][$particleNo] = $particles[$iter][$particleNo];
        }
        $gbests[$iter] = $this->getGbest($pbests[$iter], $goal);

        // Updating particles
        for ($iter = 1; $iter <= $parameters['maxIteration']; $iter++) {
            $r1 = (new Randomizers())->randomZeroToOneFraction();
            $r2 = (new Randomizers())->randomZeroToOneFraction();
            $w = $this->inertiaWeighting($iter, $parameters);

            foreach ($particles[$iter - 1] as $particleNo => $particle) {
                $velocities[$iter][$particleNo] = $this->calcVelocity($w, $velocities[$iter - 1][$particleNo], $r1, $r2, $parameters, $pbests[$iter - 1][$particleNo]['variables'], $gbests[$iter - 1]['variables'], $particle['variables'], $generalSetups['ranges']);

                $particles[$iter][$particleNo] = $this->updateParticles($particle['variables'], $velocities[$iter][$particleNo], $generalSetups, $iter);

                $pbests[$iter][$particleNo] = $this->personalBest($pbests[$iter - 1][$particleNo], $particles[$iter][$particleNo]);
            }
            $gbests[$iter] = $this->getGbest($pbests[$iter], $goal);
            $listOfGbests[] = $gbests[$iter];

            $stopResults[] = $gbests[$iter]['fitnessValue'];
            if ((new Stopper())->stopResult($iter, $stopResults)){
                break;
            }
        }
        return $this->getBestOfGbests($listOfGbests);
    }

    function runPSO($parameters, $listOfPopulation, $generalSetups, $goal)
    {
        $resultStore = new ResultStore;
        $labelDataToStore = array($generalSetups['optimizer'], $generalSetups['functions'], $generalSetups['pathToSeedDataset'], $goal);
        if(array_key_exists('pathToTestData', $generalSetups)){
            $labelDataToStore[] = $generalSetups['pathToTestData'];
        }

        $resultStore->data = $labelDataToStore;
        $resultStore->saveToTXTFile($generalSetups['pathToResults']);

        foreach ($listOfPopulation as $key => $populations) {
            $startTimeInSeconds = time();
            for ($i = 0; $i < 30; $i++) {
                $optimizedResult = $this->optimizing($parameters, (new OptimizerHelper())->getFinalPopulation($populations, $parameters['populationSize']), $generalSetups, $goal);
                $optimizedResults[] = $optimizedResult;
                $optimizedFitnessValues[] = $optimizedResult['fitnessValue'];
                echo $optimizedResult['fitnessValue'];
                echo "\n";
            }

            $endTimeInSeconds = time();
            $processingTimeInSeconds = $endTimeInSeconds - $startTimeInSeconds;

            $minFitnessValue = min($optimizedFitnessValues);
            $minFitnessValues[] = $minFitnessValue;
            echo 'Pop:'. $key . ' ' . $minFitnessValue;
            echo "\n";

            $resultStore->data = array($minFitnessValue, $processingTimeInSeconds);
            $resultStore->saveToTXTFile($generalSetups['pathToResults']);

            $optimizedFitnessValues = [];
            $startTimeInSeconds = 0;
            $endTimeInSeconds = 0;
        }
        $averageFitnessValue = array_sum($minFitnessValues) / count($minFitnessValues);

        $stringAverage = 'Average: ' . +$averageFitnessValue;

        echo "\n \n";

        $resultStore->data = $stringAverage;
        $resultStore->writeAverageResult($generalSetups['pathToResults']);
        $resultStore->writeLineBreak($generalSetups['pathToResults']);
    }
}