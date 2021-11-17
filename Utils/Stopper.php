<?php

namespace Utils;

class Stopper
{   
    function stopResult($iter, $results)
    {
        $numberOfLastResults = 10;
        if ($iter >= ($numberOfLastResults-1) ){
            $residual = count($results) - $numberOfLastResults;
            if ($residual === 0 && count(array_unique($results)) === 1 ){
                return true;
            }
            if ($residual > 0){
                for ($i = 0; $i < $residual; $i++) {
                    array_shift($results);
                }
                if (count(array_unique($results)) === 1) {
                    return true;
                }
            }
        }
    }
}