#!/bin/bash -l
# set -euo pipefail

# pushd "$APP_HOME"
cd ${APP_HOME} # Which has been loaded by the env.
php artisan schedule:run >> /dev/null 2>&1
