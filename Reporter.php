<?php

namespace Netpeak;

class Reporter
{
    private $domain;

    function __construct(string $url)
    {
        $this->domain = parse_url($url,PHP_URL_HOST);
    }

    public function report(): string
    {
        if (($handle = fopen("{$this->domain}.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    echo $data[$c] . PHP_EOL;
                }
            }
            fclose($handle);
            return "End of report for domain : {$this->domain} " . PHP_EOL;
        } else {
            return "Can't find report for domain: {$this->domain}. Please use 'parse' command for prepare report." . PHP_EOL;
        }
    }
}