#!/bin/sh

set -eu

base_url="${BASE_URL:-http://localhost:8080}"
cookie_file="$(mktemp /tmp/zyacbt-smoke-cookie.XXXXXX)"
trap 'rm -f "$cookie_file"' EXIT

curl --fail --silent --show-error --cookie-jar "$cookie_file" "$base_url/index.php/welcome" >/tmp/zyacbt-smoke-page.html
grep -q 'csrf-token' /tmp/zyacbt-smoke-page.html

csrf_status="$(curl --silent --output /dev/null --write-out '%{http_code}' \
	--cookie "$cookie_file" --cookie-jar "$cookie_file" \
	-X POST -d 'username=smoke&password=smoke' \
	"$base_url/index.php/welcome/login")"
if [ "$csrf_status" != "403" ]; then
	echo "CSRF request tanpa token seharusnya 403, hasil: $csrf_status" >&2
	exit 1
fi

if [ "$(curl --silent --output /dev/null --write-out '%{http_code}' "$base_url/uploads/")" != "403" ]; then
	echo "Directory listing uploads tidak tertutup." >&2
	exit 1
fi

docker compose exec -T app sh -c 'test -w /var/www/html/uploads && test -w /var/www/html/public/uploads'
echo "Docker smoke test passed."
