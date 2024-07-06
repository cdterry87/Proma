#!/bin/sh

set -e

vendor/bin/sail artisan test --coverage-html tests/reports/coverage
