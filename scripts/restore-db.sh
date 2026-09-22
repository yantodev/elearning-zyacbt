#!/bin/sh

set -eu

backup_file="${1:?Gunakan: scripts/restore-db.sh path/backup.sql}"
test -f "$backup_file"

echo "PERINGATAN: restore akan menimpa data database ${DB_DATABASE:-zyacbt}."
printf "Ketik RESTORE untuk melanjutkan: "
read confirmation
test "$confirmation" = "RESTORE"

docker compose exec -T db sh -c \
  'mariadb -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' \
  < "$backup_file"

echo "Restore selesai: $backup_file"
