#!/bin/bash

mkdir -p checks

# cs sniffer
rm -f checks/cs_report.txt
vendor/squizlabs/php_codesniffer/scripts/phpcs --standard=PSR2 --report-file=checks/cs_report.txt src/ tests/

# unittests
vendor/phpunit/phpunit/phpunit -v --colors --strict --coverage-html=checks/coverage tests