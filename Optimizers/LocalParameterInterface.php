<?php
namespace Optimizers;

interface LocalParameterInterface
{
    function getLocalParameter();
}

class GAParameter implements LocalParameterInterface
{
    function getLocalParameter()
    {
        return [
            'populationSize' => 30,
            'cr'=>0.9,
            'mr'=>0.01
        ];
    }
}

class PSOParameter implements LocalParameterInterface
{
    function getLocalParameter()
    {
        $popInitializations = [
            'random' => 'random',
            'uniform' => 'uniform'
        ];
        return [
            'maxIteration' => 1000,
            'populationSize' => 100,
            'c1' => 2,
            'c2' => 2,
            'inertiaMax' => 0.9,
            'inertiaMin' => 0.4,
            'popInitialization' => $popInitializations['random']
        ];
    }
}

class CPSOParameter implements LocalParameterInterface
{
    function getLocalParameter()
    {
        $popInitializations = [
            'random' => 'random',
            'uniform' => 'uniform'
        ];
        return [
            'maxIteration' => 1000,
            'populationSize' => 100,
            'c1' => 2,
            'c2' => 2,
            'inertiaMax' => 0.9,
            'inertiaMin' => 0.4,
            'popInitialization' => $popInitializations['random']
        ];
    }
}

class RaoParameter implements LocalParameterInterface
{
    function getLocalParameter()
    {
        $popInitializations = [
            'random' => 'random',
            'uniform' => 'uniform'
        ];
        return [
            'maxIteration' => 1000,
            'populationSize' => 100,
            'popInitialization' => $popInitializations['random']
        ];
    }
}

class LocalParameterFactory
{
    function initializingLocalParameter($optimizerType)
    {
        $optimizerTypes = [
            ['optimizer' => 'ga', 'select' => new GAParameter],
            ['optimizer' => 'pso', 'select' => new PSOParameter],
            ['optimizer' => 'cpso', 'select' => new CPSOParameter],
            ['optimizer' => 'rao', 'select' => new RaoParameter]
        ];
        $index = array_search($optimizerType, array_column($optimizerTypes, 'optimizer'));
        return $optimizerTypes[$index]['select'];
    }
}