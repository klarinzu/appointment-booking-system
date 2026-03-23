#!/bin/sh

set -eu

/bin/sh /var/www/html/scripts/docker-fix-permissions.sh

exec apache2-foreground
