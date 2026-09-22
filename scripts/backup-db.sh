#!/bin/sh

set -eu

output_dir="${1:-backups}"
timestamp="$(date +%Y%m%d-%H%M%S)"
mkdir -p "$output_dir"

docker compose exec -T db sh -c \
  'mariadb-dump -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' \
  > "$output_dir/zyacbt-$timestamp.sql"

echo "Backup dibuat: $output_dir/zyacbt-$timestamp.sql"
