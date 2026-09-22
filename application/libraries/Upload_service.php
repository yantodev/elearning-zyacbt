<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Operasi file upload yang dibatasi pada direktori uploads aplikasi.
 */
class Upload_service {
	private $base_path;
	private $error = '';

	public function __construct($params = array()){
		if (isset($params['base_path'])) {
			$this->base_path = rtrim($params['base_path'], '/\\\\');
			if (!is_dir($this->base_path) && !@mkdir($this->base_path, 0775, true)) {
				$this->error = 'Folder upload tidak dapat dibuat.';
			}
			return;
		}

		$CI =& get_instance();
		$configured_path = (string) $CI->config->item('upload_path');

		if ($configured_path === '' || preg_match('#(^|[\\\\/])\.\.?([\\\\/]|$)#', $configured_path) || preg_match('#^(?:[A-Za-z]:)?[\\\\/]#', $configured_path)) {
			$this->error = 'Konfigurasi folder upload tidak valid.';
			return;
		}

		$this->base_path = rtrim(FCPATH, '/\\\\').DIRECTORY_SEPARATOR.trim($configured_path, '/\\\\');
		if (!is_dir($this->base_path) && !@mkdir($this->base_path, 0775, true)) {
			$this->error = 'Folder upload tidak dapat dibuat.';
		}
	}

	public function get_error(){
		return $this->error;
	}

	public function normalize_relative_path($path){
		$path = str_replace('\\', '/', trim((string) $path));
		if (preg_match('#^/#', $path) || preg_match('#^[A-Za-z]:/#', $path)) {
			return false;
		}
		$path = trim($path, '/');
		if ($path === '') {
			return '';
		}

		$parts = explode('/', $path);
		$normalized = array();
		foreach ($parts as $part) {
			if ($part === '' || $part === '.' || $part === '..' || preg_match('/[\x00-\x1F\x7F]/', $part) || !preg_match('/^[A-Za-z0-9][A-Za-z0-9 ._-]{0,127}$/', $part)) {
				return false;
			}
			$normalized[] = $part;
		}

		return implode(DIRECTORY_SEPARATOR, $normalized);
	}

	public function normalize_filename($filename){
		$filename = trim((string) $filename);
		$basename = basename(str_replace('\\', '/', $filename));
		if ($filename === '' || $basename !== $filename || $basename === '.' || $basename === '..' || preg_match('/^[.]/', $basename)) {
			return false;
		}

		$basename = preg_replace('/[^A-Za-z0-9._-]/', '_', $basename);
		if ($basename === '' || !preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,190}$/', $basename)) {
			return false;
		}

		return strtolower($basename);
	}

	public function directory($relative = '', $create = false){
		$relative = $this->normalize_relative_path($relative);
		if ($relative === false || empty($this->base_path)) {
			return false;
		}

		$directory = $this->base_path.($relative === '' ? '' : DIRECTORY_SEPARATOR.$relative);
		$current = $this->base_path;
		if (is_link($current)) {
			return false;
		}
		if ($relative !== '') {
			foreach (explode(DIRECTORY_SEPARATOR, $relative) as $part) {
				$current .= DIRECTORY_SEPARATOR.$part;
				if (is_link($current) || (file_exists($current) && !is_dir($current))) {
					return false;
				}
			}
		}
		if ($create && !is_dir($directory) && !@mkdir($directory, 0775, true)) {
			return false;
		}

		return is_dir($directory) ? $directory : false;
	}

	public function file($relative_directory, $filename){
		$directory = $this->directory($relative_directory);
		$filename = $this->normalize_filename($filename);
		if ($directory === false || $filename === false) {
			return false;
		}

		return $directory.DIRECTORY_SEPARATOR.$filename;
	}

	public function upload($field_name, $relative_directory, array $allowed_extensions, $max_bytes = 0, $overwrite = false){
		if (empty($_FILES[$field_name]) || !isset($_FILES[$field_name]['error'])) {
			$this->error = 'Pilih terlebih dahulu file yang akan di upload.';
			return false;
		}

		$file = $_FILES[$field_name];
		if ((int) $file['error'] !== UPLOAD_ERR_OK) {
			$this->error = 'Upload file gagal (kode '.$file['error'].').';
			return false;
		}

		if (!is_uploaded_file($file['tmp_name'])) {
			$this->error = 'Sumber upload tidak valid.';
			return false;
		}

		$filename = $this->normalize_filename($file['name']);
		$extension = strtolower((string) pathinfo((string) $filename, PATHINFO_EXTENSION));
		$allowed_extensions = array_map('strtolower', $allowed_extensions);
		if ($filename === false || $extension === '' || !in_array($extension, $allowed_extensions, true)) {
			$this->error = 'Ekstensi file tidak diizinkan.';
			return false;
		}

		$size = (int) $file['size'];
		if ($size < 1 || ($max_bytes > 0 && $size > $max_bytes)) {
			$this->error = 'Ukuran file melebihi batas yang diizinkan.';
			return false;
		}

		if (!$this->valid_mime($file['tmp_name'], $extension)) {
			$this->error = 'Jenis isi file tidak sesuai dengan ekstensinya.';
			return false;
		}

		$directory = $this->directory($relative_directory, true);
		$destination = $this->file($relative_directory, $filename);
		if ($directory === false || $destination === false) {
			$this->error = 'Direktori upload tidak valid atau tidak dapat ditulis.';
			return false;
		}
		if (file_exists($destination) && !$overwrite) {
			$this->error = 'Nama file sudah terdapat pada direktori, silahkan ubah nama file yang akan di upload.';
			return false;
		}

		if (!@move_uploaded_file($file['tmp_name'], $destination)) {
			$this->error = 'File tidak dapat disimpan pada direktori upload.';
			return false;
		}

		return array(
			'file_name' => $filename,
			'full_path' => $destination,
			'relative_path' => ($relative_directory === '' ? '' : trim(str_replace(DIRECTORY_SEPARATOR, '/', $relative_directory), '/').'/').$filename,
		);
	}

	public function remove($relative_directory, $name){
		$relative_directory = $this->normalize_relative_path($relative_directory);
		$name = $this->normalize_relative_path($name);
		if ($relative_directory === false || $name === false || $name === '') {
			$this->error = 'Nama file atau direktori tidak valid.';
			return false;
		}

		$target_relative = ($relative_directory === '' ? '' : $relative_directory.'/').$name;
		$target = $this->directory($target_relative);
		if ($target === false) {
			$target_parts = explode(DIRECTORY_SEPARATOR, $target_relative);
			$target_name = array_pop($target_parts);
			$target = $this->file(implode(DIRECTORY_SEPARATOR, $target_parts), $target_name);
		}
		if ($target === false || !file_exists($target) || is_link($target)) {
			$this->error = 'File atau direktori tidak ditemukan.';
			return false;
		}

		if (is_dir($target)) {
			$this->delete_directory($target);
		} elseif (!@unlink($target)) {
			$this->error = 'File tidak dapat dihapus.';
			return false;
		}

		return true;
	}

	private function delete_directory($directory){
		$items = scandir($directory);
		if ($items === false) {
			$this->error = 'Direktori tidak dapat dibaca.';
			return false;
		}
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}
			$path = $directory.DIRECTORY_SEPARATOR.$item;
			if (is_link($path) ? !@unlink($path) : (is_dir($path) ? !$this->delete_directory($path) : !@unlink($path))) {
				return false;
			}
		}

		if (!@rmdir($directory)) {
			$this->error = 'Direktori tidak dapat dihapus.';
			return false;
		}
		return true;
	}

	private function valid_mime($path, $extension){
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = $finfo ? finfo_file($finfo, $path) : false;
		if ($finfo) {
			finfo_close($finfo);
		}

		if (in_array($extension, array('jpg', 'jpeg', 'png', 'gif'), true)) {
			$allowed_mime = array(
				'jpg' => array('image/jpeg'),
				'jpeg' => array('image/jpeg'),
				'png' => array('image/png'),
				'gif' => array('image/gif'),
			);
			return @getimagesize($path) !== false && in_array($mime, $allowed_mime[$extension], true);
		}
		if ($extension === 'mp3') {
			return in_array($mime, array('audio/mpeg', 'audio/mp3'), true);
		}
		if (in_array($extension, array('xlsx', 'zip'), true)) {
			$zip = class_exists('ZipArchive') ? new ZipArchive() : false;
			if (!$zip || !in_array($mime, array('application/zip', 'application/x-zip-compressed', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), true)) {
				return false;
			}
			$opened = $zip->open($path);
			if ($opened !== true) {
				return false;
			}
			$is_xlsx = $extension === 'xlsx' ? $zip->locateName('[Content_Types].xml') !== false : true;
			$zip->close();
			return $is_xlsx;
		}
		return false;
	}
}
