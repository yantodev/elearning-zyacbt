#!/bin/sh

set -eu

docker compose exec -T db sh -c \
	'mariadb -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' \
	< "$(dirname "$0")/upgrade-db-schema.sql"

echo "Database schema upgrade completed."
