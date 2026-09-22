#!/bin/sh

set -eu

columns="$(docker compose exec -T db sh -c 'mariadb -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -N -B -e "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = '\''cbt_user'\'' AND column_name IN ('\''user_login'\'', '\''user_login_date'\'');"')"
columns="$(printf '%s' "$columns" | tr -d '[:space:]')"

if [ "$columns" != "2" ]; then
	echo "Schema logout gagal: cbt_user harus memiliki user_login dan user_login_date." >&2
	exit 1
fi

echo "Logout schema check passed."
