<?php

namespace Utils;

class OptimizerHelper
{
    function getFinalPopulation($populations, $populationSize)
    {
        foreach ($populations as $key => $population) {
            if ($key < $populationSize) {
                $ret[] = $population;
            }
        }
        return $ret;
    }

    function getGlobalBest($bests)
    {
        $globalBest = min(array_column($bests, 'fitnessValue'));
        return $bests[array_search($globalBest, array_column($bests, 'fitnessValue'))];
    }

}