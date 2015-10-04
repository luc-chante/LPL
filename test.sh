#!/bin/bash

mkdir -p test/coverage
phpunit --bootstrap test/bootstrap.php --coverage-html test/coverage --colors --debug --verbose test/
