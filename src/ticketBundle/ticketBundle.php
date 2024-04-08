<?php

namespace App\ticketBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ticketBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}