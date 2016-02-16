#!/bin/bash

mkdir -p test/coverage
phpunit --configuration phpunit.xml ./test
