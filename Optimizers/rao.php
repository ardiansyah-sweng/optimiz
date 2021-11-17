<?php

namespace Optimizers;

require 'vendor/autoload.php';

use Functions\FunctionFactory;
use Utils\Randomizers;
use Utils\Stopper;
use Results\ResultStore;
use Utils\OptimizerHelper;

interface RaoInterface
{
    function executeRao($oldVariable, $xBest, $xWorst);
}

class Rao1 implements RaoInterface
{
    function __construct($r1)
    {
        $this->r1 = $r1;
    }

    function executeRao($oldVariable, $xBest, $xWorst)
    {
        return floatval($oldVariable) + $this->r1 + (floatval($xBest) - floatval($xWorst));
    }
}

class Rao2 implements RaoInterface
{
    function __construct($r1, $r2, $population, $particle, $particleNo, $key)
    {
        $this->r1 = $r1;
        $this->r2 = $r2;
        $this->population = $population;
        $this->fitnessValue = $particle['fitnessValue'];
        $this->variables = $particle['variables'];
        $this->particleNo = $particleNo;
        $this->key = $key;
    }

    function candidating()
    {
        ## ambil kandidat tiap variabel dari particles
        for ($i = 0; $i < count($this->population); $i++) {
            $candidates[] = [
                $this->population[$key0 = array_rand($this->population)]['fitnessValue'],
                $this->population[$key1 = array_rand($this->population)]['fitnessValue']
            ];
        }
        foreach ($candidates as $candidate){
            if ($this->fitnessValue > $candidate[0]){
                $selectedCandidate0 = ($this->population[$key0]['variables']);
            }
            if ($this->fitnessValue < $candidate[0] || $this->fitnessValue === $candidate[0]){
                $selectedCandidate0 = $this->variables;
            }
            if ($this->fitnessValue > $candidate[1]) {
                $selectedCandidate1 = ($this->population[$key1]['variables']);
            }
            if ($this->fitnessValue < $candidate[1] || $this->fitnessValue === $candidate[1]) {
                $selectedCandidate1 = $this->variables;
            }
            $ret[] = [
                $selectedCandidate0,
                $selectedCandidate1
            ];
        }
        return $ret;
    }

    function executeRao($oldVariable, $xBest, $xWorst)
    {
        $candidate0 = $this->candidating()[$this->particleNo][0][$this->key];
        $candidate1 = $this->candidating()[$this->particleNo][1][$this->key];

        return floatval($oldVariable) + ($this->r1 + (floatval($xBest) - floatval($xWorst)) + ($this->r2 * (abs($candidate0)) - abs($candidate1) ));
    }
}

class Rao3 extends Rao2 implements RaoInterface
{
    function __construct($r1, $r2, $population, $particle, $particleNo, $key)
    {
        $this->r1 = $r1;
        $this->r2 = $r2;
        $this->population = $population;
        $this->particle = $particle;
        $this->particleNo = $particleNo;
        $this->key = $key;
    }

    function executeRao($oldVariable, $xBest, $xWorst)
    {
        $candidate = new Rao2($this->r1, $this->r2, $this->population, $this->particle, $this->particleNo, $this->key);

        $candidate0 = $candidate->candidating()[$this->particleNo][0][$this->key];
        $candidate1 = $candidate->candidating()[$this->particleNo][1][$this->key];
        return floatval($oldVariable) + ($this->r1 + (floatval($xBest) - floatval(abs($xWorst))) + ($this->r2 * (abs($candidate0)) - abs($candidate1)));
    }
}

class RaoFactory
{
    function initializeRao($raoType, $r1, $r2, $particles, $particle, $particleNo, $key)
    {
        $raoTypes = [
            ['rao' => 'rao1', 'select' => new Rao1($r1)],
            ['rao' => 'rao2', 'select' => new Rao2($r1, $r2, $particles,$particle, $particleNo, $key)],
            ['rao' => 'rao3', 'select' => new Rao3($r1, $r2, $particles, $particle, $particleNo, $key)],
        ];
        $index = array_search($raoType, array_column($raoTypes, 'rao'));
        return $raoTypes[$index]['select'];
    }
}

class RaoOptimizer
{
    function updateParticle($oldParticles, $generalSetups, $r1, $r2, $xBest, $xWorst, $raoType, $particles, $particle, $particleNo)
    {
        foreach ($oldParticles as $key => $oldVariable) {

            $newVariable = (new RaoFactory())->initializeRao($raoType, $r1, $r2, $particles, $particle, $particleNo, $key)->executeRao($oldVariable, $xBest[$key], $xWorst[$key]);
            $vMax = $generalSetups['variableRanges']['upperBound'] - $generalSetups['variableRanges']['lowerBound'];
            if ($newVariable > $vMax) {
                $newVariable = $vMax;
            }
            $newVariables[] = $newVariable;
        }
        return $this->calcFunction($generalSetups['functionType'], $newVariables);
    }

    function getXBest($particles)
    {
        $min = min(array_column($particles, 'fitnessValue'));
        $index = array_search($min, array_column($particles, 'fitnessValue'));
        return $particles[$index];
    }

    function getXWorst($particles)
    {
        $min = max(array_column($particles, 'fitnessValue'));
        $index = array_search($min, array_column($particles, 'fitnessValue'));
        return $particles[$index];
    }

    function calcFunction($functionType, $variables)
    {
        $functionResult = (new FunctionFactory())->initializingFunction($functionType)->runFunction($variables, $functionType);
        return [
            'fitnessValue' => $functionResult,
            'variables' => $variables
        ];
    }

    function optimizing($parameters, $initialPopulation, $generalSetups, $raoType)
    {
        //Initializing
        $iter = 0;
        foreach ($initialPopulation as $particleNo => $variables) {
            $particles[$iter][$particleNo] = $this->calcFunction($generalSetups['functionType'], explode(",", $variables));
        }
        $xBest[$iter] = $this->getXBest($particles[$iter]);
        $xWorst[$iter] = $this->getXWorst($particles[$iter]);

        //Updating particles
        for ($iter = 1; $iter <= $parameters['maxIteration']; $iter++) {
            $r1 = (new Randomizers())->randomZeroToOneFraction();
            $r2 = (new Randomizers())->randomZeroToOneFraction();

            foreach ($particles[$iter - 1] as $particleNo => $particle) {
                $particles[$iter][$particleNo] = $this->updateParticle($particle['variables'], $generalSetups, $r1, $r2, $xBest[$iter - 1]['variables'], $xWorst[$iter - 1]['variables'], $raoType, $particles[$iter-1], $particle, $particleNo);
            }
            $xBest[$iter] = $this->getXBest($particles[$iter]);
            $xWorst[$iter] = $this->getXWorst($particles[$iter]);
            $listOfxBest[] = $xBest[$iter];

            $stopResults[] = $xBest[$iter]['fitnessValue'];
            $stopper = new Stopper;
            $stopper->numberOfLastResults = $generalSetups['stopEvaluation'];
            if ($stopper->stopResult($iter, $stopResults)) {
                break;
            }
        }
        return (new OptimizerHelper())->getGlobalBest($listOfxBest);
    }

    function runRao($parameters, $listOfPopulation, $generalSetups)
    {
        $raoTypes = ['rao1', 'rao2', 'rao3'];
        $raoType = $raoTypes[0];
        $pathToResult = 'Results/' . $raoType . '.txt';
        $generalSetups['pathToResults'] = $pathToResult;

        $resultStore = new ResultStore;
        $resultStore->data = array($generalSetups['optimizerType'], $generalSetups['datasetType'], $generalSetups['pathToSeedDataset']);
        $resultStore->saveToTXTFile($generalSetups['pathToResults']);

        foreach ($listOfPopulation as $key => $populations) {
            $startTimeInSeconds = time();
            for ($i = 0; $i < 30; $i++) {
                $optimizedResult = $this->optimizing($parameters, (new OptimizerHelper())->getFinalPopulation($populations, $parameters['populationSize']), $generalSetups, $raoType);
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

        $stringAverage = 'Average: ' . +$averageFitnessValue;

        echo "\n \n";

        $resultStore->data = $stringAverage;
        $resultStore->writeAverageResult($generalSetups['pathToResults']);
        $resultStore->writeLineBreak($generalSetups['pathToResults']);
    }
}