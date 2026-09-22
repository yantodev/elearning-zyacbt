#!/bin/sh

set -eu

base_url="${BASE_URL:-http://localhost:8080}"
username="${SMOKE_USERNAME:-lutfi}"
password="${SMOKE_PASSWORD:-lutfi}"
cookie_file="$(mktemp /tmp/zyacbt-logout-cookie.XXXXXX)"
page_file="$(mktemp /tmp/zyacbt-logout-page.XXXXXX)"
login_body="$(mktemp /tmp/zyacbt-logout-login.XXXXXX)"
logout_headers="$(mktemp /tmp/zyacbt-logout-headers.XXXXXX)"
trap 'rm -f "$cookie_file" "$page_file" "$login_body" "$logout_headers"' EXIT

curl --fail --silent --show-error --cookie-jar "$cookie_file" "$base_url/index.php/welcome" > "$page_file"
token_name="$(sed -n 's/.*csrf-token-name" content="\([^"]*\).*/\1/p' "$page_file" | head -1)"
token_value="$(sed -n 's/.*csrf-token" content="\([^"]*\).*/\1/p' "$page_file" | head -1)"
test -n "$token_name"
test -n "$token_value"

login_status="$(curl --silent --show-error --output "$login_body" --write-out '%{http_code}' \
	--cookie "$cookie_file" --cookie-jar "$cookie_file" -X POST \
	--data-urlencode "username=$username" \
	--data-urlencode "password=$password" \
	--data-urlencode "$token_name=$token_value" \
	"$base_url/index.php/welcome/login")"
test "$login_status" = "200"
grep -q '"status":1' "$login_body"

logout_status="$(curl --silent --show-error --output /dev/null --dump-header "$logout_headers" \
	--write-out '%{http_code}' --cookie "$cookie_file" --cookie-jar "$cookie_file" \
	"$base_url/index.php/welcome/logout")"
case "$logout_status" in
	301|302|303|307|308) ;;
	*) echo "Logout seharusnya redirect 3xx, hasil: $logout_status" >&2; exit 1 ;;
esac
grep -qi '^Location:.*welcome' "$logout_headers"
echo "HTTP logout regression check passed."
