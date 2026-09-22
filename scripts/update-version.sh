#!/bin/sh

set -eu

version_file="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)/VERSION"
dry_run=0

usage(){
	cat <<'EOF'
Usage: scripts/update-version.sh [major|minor|patch|X.Y.Z] [--dry-run]

Tanpa argumen, versi patch akan dinaikkan.
Contoh:
  scripts/update-version.sh
  scripts/update-version.sh minor
  scripts/update-version.sh 1.2.3
  scripts/update-version.sh patch --dry-run
EOF
}

is_semver(){
	printf '%s\n' "$1" | awk -F. '
		NF == 3 && $1 ~ /^(0|[1-9][0-9]*)$/ &&
		$2 ~ /^(0|[1-9][0-9]*)$/ && $3 ~ /^(0|[1-9][0-9]*)$/ { valid = 1 }
		END { exit(valid ? 0 : 1) }
	'
}

if [ "$#" -gt 2 ]; then
	usage >&2
	exit 1
fi

bump_type="patch"
for argument in "$@"; do
	case "$argument" in
		--dry-run)
			dry_run=1
			;;
		major|minor|patch|[0-9]*.[0-9]*.[0-9]*)
			bump_type="$argument"
			;;
		-h|--help)
			usage
			exit 0
			;;
		*)
			echo "Argumen tidak valid: $argument" >&2
			usage >&2
			exit 1
			;;
	esac
done

if [ ! -r "$version_file" ]; then
	echo "File VERSION tidak ditemukan: $version_file" >&2
	exit 1
fi

current_version="$(tr -d '\r\n' < "$version_file")"
if ! is_semver "$current_version"; then
		echo "Format VERSION tidak valid: $current_version" >&2
		exit 1
fi

if [ "$bump_type" = "major" ] || [ "$bump_type" = "minor" ] || [ "$bump_type" = "patch" ]; then
	major="$(printf '%s\n' "$current_version" | awk -F. '{ print $1 }')"
	minor="$(printf '%s\n' "$current_version" | awk -F. '{ print $2 }')"
	patch="$(printf '%s\n' "$current_version" | awk -F. '{ print $3 }')"

	case "$bump_type" in
		major) new_version="$((major + 1)).0.0" ;;
		minor) new_version="$major.$((minor + 1)).0" ;;
		patch) new_version="$major.$minor.$((patch + 1))" ;;
	esac
else
	new_version="$bump_type"
fi

if ! is_semver "$new_version"; then
		echo "Versi baru tidak valid: $new_version" >&2
		exit 1
fi

if [ "$new_version" = "$current_version" ]; then
	echo "Versi baru sama dengan versi saat ini: $current_version" >&2
	exit 1
fi

if [ "$dry_run" -eq 1 ]; then
	echo "$current_version -> $new_version (dry-run)"
	exit 0
fi

printf '%s\n' "$new_version" > "$version_file"
echo "Versi diperbarui: $current_version -> $new_version"
