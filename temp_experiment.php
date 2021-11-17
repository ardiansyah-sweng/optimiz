<?php

use Optimizers\OptimizerFactory;
use Optimizers\LocalParameterFactory;
use Dataset\Data;
use DataProcessor\DataprocessorFactory;

interface ExperimentInterface
{
    function conductExperiment($generalSetups, $mainType);
}


class Individual implements ExperimentInterface
{
    function isSingle($generalSetups)
    {
        if (is_array($generalSetups['optimizerType'])) {
            return true;
        }
        if (is_array($generalSetups['datasetType'])) {
            return true;
        }
        if (is_array($generalSetups['pathToSeedDataset'])) {
            return true;
        }
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

    function initializingPopulation($generalSetups, $mainType)
    {
        if ($mainType === 'random') {
            //call and return random population initializagion
        }
        if ($mainType === 'seeds') {
            $dataSeeds = new Data;
            $dataSeeds->path = $generalSetups['pathToSeedDataset'];
            $dataseeds = $dataSeeds->getFileNames();
            return $this->createDataseeds($dataseeds);
        }
    }

    function conductExperiment($generalSetups, $mainType)
    {
        if ($this->isSingle($generalSetups) === true) {
            die('This is for individual experiment. Select functions instead!');
        }
        $initialPopulation = $this->initializingPopulation($generalSetups, $mainType);
        $parameters = (new LocalParameterFactory())->initializingLocalParameter($generalSetups['optimizerType'])->getLocalParameter();

        (new OptimizerFactory())->initializingOptimizer($generalSetups['optimizerType'])->runOptimizer($parameters, $initialPopulation, $generalSetups);
    }
}

class EffortEstimation extends Individual implements ExperimentInterface
{
    function conductExperiment($generalSetups, $mainType)
    {
        $parameters = (new LocalParameterFactory())->initializingLocalParameter($generalSetups['optimizerType'])->getLocalParameter();
        $testDataset = (new DataprocessorFactory())->initializeDataprocessor($generalSetups['datasetType'], $parameters['populationSize'])->processingData($generalSetups['pathToTestDataset']);
        $initialPopulation = $this->initializingPopulation($generalSetups, $mainType);
        print_r($testDataset);
        exit;

        (new OptimizerFactory())->initializingOptimizer($generalSetups['optimizerType'])->runOptimizer($parameters, $initialPopulation, $generalSetups);
    }
}

class ExperimentFactory
{
    public function initializingExperiment($experimentType)
    {
        $experimentTypes = [
            ['experiment' => 'effortEstimation', 'select' => new EffortEstimation],
            ['experiment' => 'individual', 'select' => new Individual]
        ];
        $index = array_search($experimentType, array_column($experimentTypes, 'experiment'));
        return $experimentTypes[$index]['select'];
    }
}
