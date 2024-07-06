#!/bin/sh

set -e

./test-coverage-report.sh

(git push) || true

git checkout production

git merge master -m "Deploy to production"

git push origin production

git checkout master
