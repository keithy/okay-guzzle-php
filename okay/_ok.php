
<?php

# _ok.php turns a directory of *.inc scripts into a test suite.
# doubles as the one-time setup script
# first time
if (require(__DIR__ . '/../vendor/okay/okay/_okay.php')) return;

# second time - one-time setup code for this directory
require_once __DIR__ . '/../vendor/autoload.php';
 