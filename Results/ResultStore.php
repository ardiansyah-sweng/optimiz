<?php

namespace Results;

class ResultStore
{
    public $data;

    function saveToTXTFile($pathToResults)
    {
        $fp = fopen($pathToResults, 'a');
        fputcsv($fp, $this->data);
        fclose($fp);
    }

    function writeAverageResult($pathToWrite)
    {
        $fp = fopen($pathToWrite, 'a');
        fwrite($fp, $this->data);
        fclose($fp);
    }

    function writeLineBreak($pathToWrite)
    {
        $stringData = "\n \n";
        $fp = fopen($pathToWrite, 'a');
        fwrite($fp, $stringData);
        fclose($fp);
    }
}
