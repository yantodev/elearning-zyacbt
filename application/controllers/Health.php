<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Endpoint sederhana untuk health check Docker dan monitoring eksternal.
 */
class Health extends CI_Controller {
	public function index(){
		$database_ok = false;
		try {
			$database_ok = $this->db->conn_id !== false;
			if ($database_ok) {
				$this->db->query('SELECT 1');
			}
		} catch (Throwable $exception) {
			log_message('error', 'Health check database gagal: '.$exception->getMessage());
		}

		$status = $database_ok ? 200 : 503;
		$this->output
			->set_status_header($status)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode(array(
				'status' => $database_ok ? 'ok' : 'degraded',
				'database' => $database_ok ? 'ok' : 'error',
				'version' => (string) config_item('site_version'),
			), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}
}
