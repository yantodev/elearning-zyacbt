<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Response JSON seragam untuk endpoint AJAX dan API internal.
 */
class Api_response {
	public function payload($status, $message = '', array $data = array()){
		$payload = array(
			'status' => (int) $status,
			'pesan' => (string) $message,
		);

		foreach ($data as $key => $value) {
			$payload[$key] = $value;
		}

		return $payload;
	}

	public function validation_error($message){
		return $this->payload(0, $message);
	}

	public function send(array $payload, $http_status = 200){
		$CI =& get_instance();
		$CI->output
			->set_status_header((int) $http_status)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}
}
