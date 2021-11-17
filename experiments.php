<?php
require 'vendor/autoload.php';

use Utils\Configuration;
use Dataset\Data;
use Optimizers\LocalParameterFactory;
use Optimizers\OptimizerFactory;


interface MainExecutor
{
    function executionMain($types, $goal);
}

class RunAll extends Configuration implements MainExecutor
{
    function executionMain($types, $goal)
    {
        foreach ($this->getIterables() as $iterable) {
            foreach ($this->getOptimizerTypes() as $optimizerType) {
                $generalSetups = [
                    'optimizerType' => $optimizerType['optimizer'],
                    'datasetType' => $iterable['functions'],
                    'pathToSeedDataset' => $iterable['pathToSeedDataset'],
                    'variableRanges' => $iterable['ranges'],
                    'functionType' => $iterable['functions'],
                    'stoppingValue' => 0,
                    'fitnessType' => 'min',
                    'pathToResults' => $optimizerType['pathToResult'],
                    'stopEvaluation' => 20
                ];
            }
            //(new MainFactory())->initializingMain($mainTypes['seeds'], $experimentTypes['effortEstimation'], $generalSetups)->runMain($mainTypes['seeds']);
        }
    }
}

class RunOneByOne extends Configuration implements MainExecutor
{
    function findIterableFunction($function)
    {
        $index = array_search($function, array_column($this->getIterables(), 'functions'));
        return $this->getIterables()[$index];
    }

    function findOptimizer($optimizer)
    {
        $index = array_search($optimizer, array_column($this->getOptimizerTypes(), 'optimizer'));
        return $this->getOptimizerTypes()[$index];
    }

    function createDataseeds($seedsFileNames)
    {
        foreach ($seedsFileNames as $seedsFileName) {
            foreach (file($seedsFileName) as $val) {
                $dataInEachFile[] = $val;
            }
            foreach ($dataInEachFile as $val) {
                $seedsDataInFile[] = $val;
            }
            $ret[] = $seedsDataInFile;
            $seedsDataInFile = [];
            $dataInEachFile = [];
        }
        return $ret;
    }

    function initializingPopulation($generalSetups)
    {
        $dataSeeds = new Data;
        $dataSeeds->path = $generalSetups['pathToSeedDataset'];
        $dataseeds = $dataSeeds->getFileNames();
        return $this->createDataseeds($dataseeds);
    }

    function executionMain($types, $goal)
    {
        $functionSetup = $this->findIterableFunction($types['function']);
        $optimizerSetup = $this->findOptimizer($types['optimizer']);
        $generalSetups = array_merge($functionSetup, $optimizerSetup);
        $generalSetups['pathToResults'] = 'Results/'.$types['optimizer'].'.txt';

        $initialPopulation = $this->initializingPopulation($generalSetups);
        $parameters = (new LocalParameterFactory())->initializingLocalParameter($generalSetups['optimizer'])->getLocalParameter();
        (new OptimizerFactory())->initializingOptimizer($generalSetups['optimizer'])->runOptimizer($parameters, $initialPopulation, $generalSetups, $goal);
    }
}

class RunOneOptimizerAllFunctions extends Configuration implements MainExecutor
{
    function executionMain($types, $goal)
    {
        echo 'One Optimizer all functions';
    }
}

class RunOneFunctionAllOptimizer extends Configuration implements MainExecutor
{
    function executionMain($types, $goal)
    {
        echo 'One function all optimizer';
    }
}

class MainProcessorFactory
{
    function initializeMainProcessor($executionType)
    {
        $executionTypes = [
            ['type' => 1, 'select' => new RunAll],
            ['type' => 2, 'select' => new RunOneByOne],
            ['type' => 3, 'select' => new RunOneOptimizerAllFunctions],
            ['type' => 4, 'select' => new RunOneFunctionAllOptimizer],
        ];
        $index = array_search($executionType[0], array_column($executionTypes, 'type'));
        return $executionTypes[$index]['select'];
    }
}

