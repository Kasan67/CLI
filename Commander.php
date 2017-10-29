#!/usr/bin/php
<?php

namespace Netpeak;

require_once('./vendor/autoload.php');

/**
 * Class Commander
 * @package Netpeak
 */
class Commander
{

    /**
     * Commander constructor.
     * @param array $options
     */
    function __construct(array $options)
    {
        switch (key($options)) {
            case "parse":
                $parser = new Parser($this->checkUrl($options['parse']));
                echo $parser->parse();
                break;

            case "report":
                $reporter = new Reporter($this->checkUrl($options['report']));
                echo $reporter->report();
                break;

            case "help" || "h":
            default:
                echo $this->help();
        }

    }

    /**
     * @return string
     */
    protected function help(): string
    {
        $message = "Неизвестная команда. " . PHP_EOL
            . "Использование: ./Commander.php <command> " . PHP_EOL
            . "<command> обязательный параметр. " . PHP_EOL
            . "Команда --parse - запускает парсер, принимает обязательный параметр url (как с протоколом, так и без)." . PHP_EOL
            . "Команда --report - выводит в консоль результаты анализа для домена, "
            . "принимает обязательный параметр domain (как с протоколом, так и без)." . PHP_EOL
            . "Команда --help || -h - выводит текущую справочную информацию." . PHP_EOL;

        return $message;
    }


    /**
     * @param $url
     * @return string
     */
    private function checkUrl($url): string
    {
        return strpos($url, "http") === false ? "http://{$url}" : $url;
    }
}

$options = getopt("p:r:h", ["parse:", "report:", "help"]);

new Commander($options);