<?php

namespace Utils;

class Configuration
{
    function getIterables()
    {
        return [
            ['functions' => 'f1', 'ranges' => ['lowerBound' => -100, 'upperBound' => 100], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range100/'],
            ['functions' => 'f2', 'ranges' => ['lowerBound' => -10, 'upperBound' => 10], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range10/'],
            ['functions' => 'f3', 'ranges' => ['lowerBound' => -100, 'upperBound' => 100], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range100/'],
            ['functions' => 'f4', 'ranges' => ['lowerBound' => -100, 'upperBound' => 100], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range100/'],
            ['functions' => 'f5', 'ranges' => ['lowerBound' => -30, 'upperBound' => 30], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range30/'],
            ['functions' => 'f6', 'ranges' => ['lowerBound' => -100, 'upperBound' => 100], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range100/'],
            ['functions' => 'f7', 'ranges' => ['lowerBound' => -1.28, 'upperBound' => 1.28], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range1Koma28/'],
            ['functions' => 'f8', 'ranges' => ['lowerBound' => -500, 'upperBound' => 500], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range500/'],
            ['functions' => 'f9', 'ranges' => ['lowerBound' => -5.12, 'upperBound' => 5.12], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range5Koma12/'],
            ['functions' => 'f10', 'ranges' => ['lowerBound' => -32, 'upperBound' => 32], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range32/'],
            ['functions' => 'f11', 'ranges' => ['lowerBound' => -600, 'upperBound' => 600], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range600/'],
            ['functions' => 'f12', 'ranges' => ['lowerBound' => -50, 'upperBound' => 50], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range50/'],
            ['functions' => 'f13', 'ranges' => ['lowerBound' => -50, 'upperBound' => 50], 'pathToSeedDataset' => 'Dataset/TestFunctions/Range50/'],
            [
                'functions' => 'ucp', 'ranges' => [
                    ['lowerBound' => 5, 'upperBound' => 7.49],
                    ['lowerBound' => 7.5, 'upperBound' => 12.49],
                    ['lowerBound' => 12.5, 'upperBound' => 15]
                ], 'pathToTestData' => 'Dataset/EffortEstimation/Public/ucp_silhavy.txt', 'pathToSeedDataset' => 'Dataset/EffortEstimation/Seeds/ucp/'
            ],
            [
                'functions' => 'cocomo', 'ranges' => [
                    ['lowerBound' => 0, 'upperBound' => 10],
                    ['lowerBound' => 0.3, 'upperBound' => 2]
                ], 'pathToTestData' => 'Dataset/EffortEstimation/Public/cocomo_nasa93.txt', 'pathToSeedDataset' => 'Dataset/EffortEstimation/Seeds/cocomo/'
            ],
            [
                'functions' => 'agile', 'ranges' => [
                    ['lowerBound' => 0.91, 'upperBound' => 1],
                    ['lowerBound' => 0.89, 'upperBound' => 1],
                    ['lowerBound' => 0.96, 'upperBound' => 1],
                    ['lowerBound' => 0.85, 'upperBound' => 1],
                    ['lowerBound' => 0.91, 'upperBound' => 1],
                    ['lowerBound' => 0.96, 'upperBound' => 1],
                    ['lowerBound' => 0.9, 'upperBound' => 1],
                    ['lowerBound' => 0.98, 'upperBound' => 1],
                    ['lowerBound' => 0.98, 'upperBound' => 1],
                    ['lowerBound' => 0.96, 'upperBound' => 1],
                    ['lowerBound' => 0.95, 'upperBound' => 1],
                    ['lowerBound' => 0.97, 'upperBound' => 1],
                    ['lowerBound' => 0.98, 'upperBound' => 1]
                ], 'pathToTestData' => 'Dataset/EffortEstimation/Public/agile_ziauddin.txt', 'pathToSeedDataset' => 'Dataset/EffortEstimation/Seeds/agile/'
            ]
        ];
    }
    
    function getFunctionTypes()
    {
        return [
            'unimodal' => ['f1' => 'f1', 'f2' => 'f2', 'f3' => 'f3', 'f4' => 'f4', 'f5' => 'f5', 'f6' => 'f6', 'f7' => 'f7', 'stoppingValue' => 0, 'fitnessType' => 'min'],
            'multimodal' => ['f8' => 'f8', 'f9' => 'f9', 'f10' => 'f10', 'f11' => 'f11', 'f12' => 'f12', 'f13' => 'f13', 'stoppingValue' => 0, 'fitnessType' => 'min'],
            'effortEstimation' => ['ucp' => 'ucp', 'cocomo' => 'cocomo', 'agile' => 'agile', 'stoppingValue' => 0, 'fitnessType' => 'min']
        ];
    }

    function getOptimizerTypes()
    {
        return [
            ['optimizer'=>'pso', 'pathToResult' => 'Results/pso.txt'], 
            ['optimizer'=> 'cpso', 'pathToResult' => 'Results/cpso.txt'],
            ['optimizer' => 'rao1', 'pathToResult' => 'Results/rao1.txt'],
            ['optimizer' => 'rao2', 'pathToResult' => 'Results/rao2.txt'],
            ['optimizer' => 'rao3', 'pathToResult' => 'Results/rao3.txt']
        ];
    }

    function getDataSet()
    {
        return
            [
                'f1' => 'f1', 'f2' => 'f2', 'f3' => 'f3', 'f4' => 'f4', 'f5' => 'f5', 'f6' => 'f6', 'f7' => 'f7', 'f8' => 'f8', 'f9' => 'f9', 'f10' => 'f10', 'f11' => 'f11', 'f12' => 'f12', 'f13' => 'f13', 'ucp' => 'silhavy', 'cocomo' => 'nasa93', 'agile' => 'ziauddin'
            ];
    }

    function getPathsToDataset()
    {
        return [
            'testFunctions' => [
                '1.28' => [
                    'path' => 'Dataset/TestFunctions/Range1Koma28/',
                    'lowerBound' => -1.28,
                    'upperBound' => 1.28
                ],
                '5.12' => [
                    'path' => 'Dataset/TestFunctions/Range5Koma12/',
                    'lowerBound' => -5.12,
                    'upperBound' => 5.12
                ],
                '10' => [
                    'path' => 'Dataset/TestFunctions/Range10/',
                    'lowerBound' => -10,
                    'upperBound' => 10
                ],
                '30' => [
                    'path' => 'Dataset/TestFunctions/Range30/',
                    'lowerBound' => -30,
                    'upperBound' => 30

                ],
                '32' => [
                    'path' => 'Dataset/TestFunctions/Range32/',
                    'lowerBound' => -32,
                    'upperBound' => 32
                ],
                '50' => [
                    'path' => 'Dataset/TestFunctions/Range50/',
                    'lowerBound' => -50,
                    'upperBound' => 50
                ],
                '100' => [
                    'path' => 'Dataset/TestFunctions/Range100/',
                    'lowerBound' => -100,
                    'upperBound' => 100
                ],
                '500' => [
                    'path' => 'Dataset/TestFunctions/Range500/',
                    'lowerBound' => -500,
                    'upperBound' => 500
                ],
                '600' => [
                    'path' => 'Dataset/TestFunctions/Range600/',
                    'lowerBound' => -600,
                    'upperBound' => 600
                ],
                'ucp' => [
                    'seed' => 'Dataset/EffortEstimation/Seeds/ucp/', 'public' => 'Dataset/EffortEstimation/Public/ucp_silhavy.txt'
                ],
                'cocomo' => [
                    'seed' => 'Dataset/EffortEstimation/Seeds/cocomo/',
                    'public' => 'Dataset/EffortEstimation/Public/cocomo_nasa93.txt'
                ],
                'agile' => [
                    'seed' => 'Dataset/EffortEstimation/Seeds/agile/',
                    'public' => 'Dataset/EffortEstimation/Public/agile_ziauddin.txt'
                ],
            ],
            'pathToResults' => [
                'testFunctions' => 'Results/pso.txt',
                'realFunctions' => 'Results'
            ]
        ];
    }
}
