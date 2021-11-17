<?php

/**
 * Tharwat A, Elhoseny M, Hassanien AE, Gabel T, Kumar A. Intelligent Bézier curve-based path planning model using Chaotic Particle Swarm Optimization algorithm. Cluster Comput [Internet]. 2019 Mar 12;22(S2):4745–66. Available from: https://doi.org/10.1007/s10586-018-2360-3
 */


namespace Optimizers\Pso;

// require_once "Functions/Functions.php";
// require_once "Utils/randomizer.php";
// require_once "Utils/Stopper.php";
// require_once "Utils/ChaoticMaps.php";
// require_once "Utils/OptimizerHelper.php";
// require_once "Results/ResultStore.php";
// require_once "Optimizers/Pso/pso.php";

use Optimizers\Pso\ParticleSwarmOptimizer;
use Utils\Randomizers;
use Utils\Stopper;
use Utils\ChaoticFactory;
use Utils\OptimizerHelper;
use Results\ResultStore;

class ChaoticPSO extends ParticleSwarmOptimizer
{
    function optimizing($parameters, $initialPopulation, $generalSetups)
    {
        // Initializing
        $initialVelocity = (new Randomizers())->randomZeroToOneFraction();
        $iter = 0;
        foreach ($initialPopulation as $particleNo => $variables) {
            $velocities[$iter][$particleNo] = $this->createInitialVelocities(explode(",", $variables), $initialVelocity);

            $particles[$iter][$particleNo] = $this->updateParticles(explode(",", $variables), $velocities[$iter][$particleNo], $generalSetups, $iter);

            $pbests[$iter][$particleNo] = $particles[$iter][$particleNo];
        }
        $gbests[$iter] = $this->getGbest($pbests[$iter], $generalSetups['fitnessType']);

        // Updating particles
        $chaosTypeR1 = 'singer';
        $chaosTypeR2 = 'sine';
        $r1[$iter] = 0.7;
        $r2[$iter] = 0.7;

        for ($iter = 1; $iter <= $parameters['maxIteration']; $iter++) {
            $r1[$iter] = (new ChaoticFactory())->initializeChaotic($chaosTypeR1, $iter, 4)->chaotic($r1[$iter - 1]);
            $r2[$iter] = (new ChaoticFactory())->initializeChaotic($chaosTypeR2, $iter, 4)->chaotic($r2[$iter - 1]);
            $w = $this->inertiaWeighting($iter, $parameters);

            foreach ($particles[$iter - 1] as $particleNo => $particle) {
                $velocities[$iter][$particleNo] = $this->calcVelocity($w, $velocities[$iter - 1][$particleNo], $r1[$iter], $r2[$iter], $parameters, $pbests[$iter - 1][$particleNo]['variables'], $gbests[$iter - 1]['variables'], $particle['variables'], $generalSetups['variableRanges']);

                $particles[$iter][$particleNo] = $this->updateParticles($particle['variables'], $velocities[$iter][$particleNo], $generalSetups, $iter);

                $pbests[$iter][$particleNo] = $this->personalBest($pbests[$iter - 1][$particleNo], $particles[$iter][$particleNo]);
            }
            $gbests[$iter] = $this->getGbest($pbests[$iter], $generalSetups['fitnessType']);
            $listOfGbests[] = $gbests[$iter];

            $stopResults[] = $gbests[$iter]['fitnessValue'];
            $stopper = new Stopper;
            $stopper->numberOfLastResults = $generalSetups['stopEvaluation'];
            if ($stopper->stopResult($iter, $stopResults)) {
                break;
            }
        }
        return $this->getBestOfGbests($listOfGbests);
    }

    function runPSO($parameters, $listOfPopulation, $generalSetups)
    {
        $resultStore = new ResultStore;
        $resultStore->data = array($generalSetups['optimizerType'], $generalSetups['datasetType'], $generalSetups['pathToSeedDataset']);
        $resultStore->saveToTXTFile($generalSetups['pathToResults']);

        foreach ($listOfPopulation as $key => $populations) {
            $startTimeInSeconds = time();
            for ($i = 0; $i < 30; $i++) {
                $optimizedResult = $this->optimizing($parameters, (new OptimizerHelper())->getFinalPopulation($populations, $parameters['populationSize']), $generalSetups);
                $optimizedResults[] = $optimizedResult;
                $optimizedFitnessValues[] = $optimizedResult['fitnessValue'];
                echo $optimizedResult['fitnessValue'];
                echo "\n";
            }

            $endTimeInSeconds = time();
            $processingTimeInSeconds = $endTimeInSeconds - $startTimeInSeconds;

            $minFitnessValue = min($optimizedFitnessValues);
            $minFitnessValues[] = $minFitnessValue;
            echo 'Pop:' . $key . ' ' . $minFitnessValue;
            echo "\n";

            $resultStore->data = array($minFitnessValue, $processingTimeInSeconds);
            $resultStore->saveToTXTFile($generalSetups['pathToResults']);

            $optimizedFitnessValues = [];
            $startTimeInSeconds = 0;
            $endTimeInSeconds = 0;
        }
        $averageFitnessValue = array_sum($minFitnessValues) / count($minFitnessValues);
        if (is_infinite($averageFitnessValue) || $averageFitnessValue == 0) {
            $averageFitnessValue = 0.00000001;
        }
        $stringAverage = "Average: " . +$averageFitnessValue;

        echo "\n \n";

        $resultStore->data = $stringAverage;
        $resultStore->writeAverageResult($generalSetups['pathToResults']);
        $resultStore->writeLineBreak($generalSetups['pathToResults']);
    }
}
