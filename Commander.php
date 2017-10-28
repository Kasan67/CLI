#!/usr/bin/php
<?php
/**
 * Created by PhpStorm.
 * User: sanya
 * Date: 28.10.17
 * Time: 11:07
 */
namespace Netpeak;



class Commander
{

    function __construct(int $argc, array $argv)
    {
        if ($argc != 2) $this->help($argv[0]);
        switch ($argv[1])
        {
            case "parse":
                require_once "Parser.php";
                $parser = new Parser($this->getUrl());
                echo $parser->parse();
                break;

            case "report":
                require_once "Reporter.php";
                $reporter = new Reporter($this->getUrl());
                echo $reporter->report();
                break;

            case "help":
            default:
                echo $this->help($argv[0]);
        }

    }

    protected function help($script): string
    {
        $message = "Неизвестная команда. \n\n"
            ."Использование: {$script} <command> \n\n"
            ."<command> обязательный параметр.\n\n"
            ."Команда parse - запускает парсер, принимает обязательный параметр url (как с протоколом, так и без).\n"
            ."Команда report - выводит в консоль результаты анализа для домена, "
            ."принимает обязательный параметр domain (как с протоколом, так и без).\n"
            ."Команда help - выводит текущую справочную информацию.\n";

        return $message;
    }


    private function getUrl(): string
    {
        return readline('Пожалуйста, укажите url : ');
    }
}

new Commander($argc, $argv);