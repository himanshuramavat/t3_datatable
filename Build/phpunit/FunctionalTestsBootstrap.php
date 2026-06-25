<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/.Build/vendor/autoload.php';

$testbase = new \TYPO3\TestingFramework\Core\Testbase();
$testbase->defineSitePath();
$testbase->defineOriginalRootPath();
