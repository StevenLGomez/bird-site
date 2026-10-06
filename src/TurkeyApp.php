<?php

declare(strict_types=1);

require_once __DIR__ . '/BirdApp.php';

final class TurkeyApp extends BirdApp
{
    public function __construct()
    {
        parent::__construct('Turkey');
    }
}

