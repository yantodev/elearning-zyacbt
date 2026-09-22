#!/bin/sh

set -eu

source_image="${SOURCE_IMAGE:-mariadb:10.4}"
target_image="${TARGET_IMAGE:-mariadb:10.11}"
database_name="${DB_DATABASE:-zyacbt}"
root_password="${DB_ROOT_PASSWORD:-upgrade-test-root}"
source_container="zyacbt-mariadb-source-$$"
target_container="zyacbt-mariadb-target-$$"
work_dir="$(mktemp -d /tmp/zyacbt-mariadb-upgrade.XXXXXX)"

cleanup(){
	docker rm -f "$source_container" "$target_container" >/dev/null 2>&1 || true
	docker run --rm --entrypoint sh -v "$work_dir:/cleanup" "$source_image" \
		-c 'rm -rf /cleanup/* /cleanup/.[!.]*' >/dev/null 2>&1 || true
	rmdir "$work_dir/source" "$work_dir/target" "$work_dir" 2>/dev/null || true
}
trap cleanup EXIT

wait_for_database(){
	container="$1"
	attempt=0
	while ! docker exec "$container" mariadb -uroot -p"$root_password" -e 'SELECT 1' >/dev/null 2>&1; do
		attempt=$((attempt + 1))
		if [ "$attempt" -ge 60 ]; then
			echo "MariaDB tidak siap: $container" >&2
			exit 1
		fi
		sleep 2
	done
}

mkdir -p "$work_dir/source" "$work_dir/target"
docker run --detach --name "$source_container" \
	-e MARIADB_DATABASE="$database_name" \
	-e MARIADB_ROOT_PASSWORD="$root_password" \
	-e MYSQL_DATABASE="$database_name" \
	-e MYSQL_ROOT_PASSWORD="$root_password" \
	-v "$work_dir/source:/var/lib/mysql" "$source_image" >/dev/null

wait_for_database "$source_container"

if [ -f "zyacbt-public-2024-05-05-tanpa-database.sql" ]; then
	docker exec -i "$source_container" mariadb -uroot -p"$root_password" "$database_name" < zyacbt-public-2024-05-05-tanpa-database.sql
fi

docker exec "$source_container" mariadb-dump -uroot -p"$root_password" \
	--single-transaction --routines --triggers "$database_name" > "$work_dir/upgrade.sql"

docker run --detach --name "$target_container" \
	-e MARIADB_DATABASE="$database_name" \
	-e MARIADB_ROOT_PASSWORD="$root_password" \
	-e MYSQL_DATABASE="$database_name" \
	-e MYSQL_ROOT_PASSWORD="$root_password" \
	-v "$work_dir/target:/var/lib/mysql" "$target_image" >/dev/null

wait_for_database "$target_container"

docker exec -i "$target_container" mariadb -uroot -p"$root_password" "$database_name" < "$work_dir/upgrade.sql"
docker exec "$target_container" mariadb-check -uroot -p"$root_password" --all-databases --check-upgrade
echo "MariaDB upgrade test passed: $source_image -> $target_image"
