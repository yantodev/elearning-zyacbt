<?php

define('BASEPATH', dirname(__DIR__).'/system/');
define('FCPATH', dirname(__DIR__).DIRECTORY_SEPARATOR);
require dirname(__DIR__).'/application/libraries/Upload_service.php';

$base = sys_get_temp_dir().DIRECTORY_SEPARATOR.'zyacbt-upload-'.bin2hex(random_bytes(6));
mkdir($base, 0775, true);
$service = new Upload_service(array('base_path' => $base));
$outside = dirname($base).DIRECTORY_SEPARATOR.'zyacbt-outside.txt';
file_put_contents($outside, 'do not remove');

$assertions = array(
    $service->normalize_relative_path('../outside') === false,
    $service->normalize_relative_path('folder/../../outside') === false,
    $service->normalize_relative_path('/absolute/path') === false,
    $service->normalize_filename('../config.php') === false,
    $service->normalize_relative_path('topik_12/images') === 'topik_12'.DIRECTORY_SEPARATOR.'images',
    $service->normalize_filename('Gambar 01.jpg') === 'gambar_01.jpg',
    $service->remove('../', 'zyacbt-outside.txt') === false,
    file_exists($outside),
);

unlink($outside);
rmdir($base);

if (in_array(false, $assertions, true)) {
    fwrite(STDERR, "Upload service security check gagal.\n");
    exit(1);
}

echo "Upload service security check passed.\n";
