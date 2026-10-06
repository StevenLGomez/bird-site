
<?php

declare(strict_types=1);

require_once __DIR__ . '/BirdApp.php';

final class DuckApp extends BirdApp
{
    public function __construct()
    {
        parent::__construct('Duck');
    }
}

