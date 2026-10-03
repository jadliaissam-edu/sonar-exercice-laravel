#!/bin/sh
# Runs the test suite with coverage and rewrites absolute paths in the reports
# so that sonar-scanner can match them with the source files.
# Works locally (PHP + PCOV/Xdebug installed) and inside the Docker image
# (see docker/Dockerfile and README.md).
set -e

cd "$(dirname "$0")/.."

XDEBUG_MODE=coverage vendor/bin/phpunit \
    --coverage-clover coverage.xml \
    --log-junit phpunit-report.xml "$@"

ROOT="$(pwd)/"
for report in coverage.xml phpunit-report.xml; do
    sed -i.bak "s#${ROOT}##g" "$report"
    rm -f "$report.bak"
done

echo "Reports written: coverage.xml, phpunit-report.xml (project-relative paths)"
