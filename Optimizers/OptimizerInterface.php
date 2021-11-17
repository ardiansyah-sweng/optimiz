<?php

namespace Optimizers\Pso;

namespace Optimizers;

use Optimizers\Pso\ParticleSwarmOptimizer;
use Optimizers\Pso\ChaoticPSO;
use Optimizers\RaoOptimizer;

interface OptimizerInterface
{
    function runOptimizer($parameters, $initialPopulation, $generalSetups, $goal);
}

class PSO implements OptimizerInterface
{
    function runOptimizer($parameters, $listOfPopulation, $generalSetups, $goal)
    {
        (new ParticleSwarmOptimizer())->runPSO($parameters, $listOfPopulation, $generalSetups, $goal);
    }
}

class CPSO implements OptimizerInterface
{
    function runOptimizer($parameters, $listOfPopulation,$generalSetups, $goal)
    {
        (new ChaoticPSO())->runPSO($parameters, $listOfPopulation, $generalSetups);
    }
}

class Rao implements OptimizerInterface
{
    function runOptimizer($parameters, $listOfPopulation,$generalSetups, $goal)
    {
        (new RaoOptimizer())->runRao($parameters, $listOfPopulation, $generalSetups);
    }
}

class UCPSO implements OptimizerInterface
{
    function runOptimizer($parameters, $initialPopulation,$generalSetups, $goal)
    {
        echo 'hai UCPSO';
    }
}

class GA implements OptimizerInterface
{
    function runOptimizer($parameters, $initialPopulation,$generalSetups, $goal)
    {
        echo 'hai GA';
    }
}

class ES implements OptimizerInterface
{
    function runOptimizer($parameters, $initialPopulation,$generalSetups, $goal)
    {
        echo 'hai ES';
    }
}

class Firefly implements OptimizerInterface
{
    function runOptimizer($parameters, $initialPopulation,$generalSetups, $goal)
    {
        echo 'hai Firefly';
    }
}

class OptimizerFactory
{
    public function initializingOptimizer($optimizerType)
    {
        $optimizerTypes = [
            ['experiment' => 'ga', 'select' => new GA],
            ['experiment' => 'pso', 'select' => new PSO],
            ['experiment' => 'cpso', 'select' => new CPSO],
            ['experiment' => 'ucpso', 'select' => new CPSO],
            ['experiment' => 'rao', 'select' => new Rao],
        ];
        $index = array_search($optimizerType, array_column($optimizerTypes, 'experiment'));
        return $optimizerTypes[$index]['select'];
    }
}
