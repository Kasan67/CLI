<?php

namespace Netpeak;
/**
 * Interface ReporterInterface
 */
interface ReporterInterface
{
    /**
     * @return mixed
     */
    public function checkFile();

    /**
     * @return string
     */
    public function report(): string;
}