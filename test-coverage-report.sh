#!/bin/sh

set -e

date

vendor/bin/sail artisan test --parallel --drop-databases --coverage-html tests/reports/coverage

date