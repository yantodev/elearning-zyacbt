#!/bin/sh

set -eu
umask 0002

# Bind mount source code dapat menimpa permission saat image dibuat.
# Atur ulang folder yang harus ditulis Apache setiap container dimulai.
UPLOAD_DIRECTORIES="/var/www/html/uploads /var/www/html/public/uploads /var/www/html/application/cache /var/www/html/application/logs"

for directory in $UPLOAD_DIRECTORIES; do
    mkdir -p "$directory"

    if [ "$(id -u)" = "0" ]; then
        # Jangan mengubah ownership bind mount agar tetap aman digunakan XAMPP.
        chmod -R a+rwX "$directory" 2>/dev/null || true
    fi
done

exec "$@"
