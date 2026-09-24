<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Publica eventos operasional ke log aplikasi dan collector webhook opsional.
 */
class Monitoring_service {
	public function event($name, array $context = array()){
		$payload = array(
			'event' => (string) $name,
			'timestamp' => gmdate('c'),
			'context' => $context,
		);
		log_message('info', 'MONITORING '.json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

		$url = trim((string) config_item('monitoring_webhook_url'));
		if ($url === '' || !function_exists('curl_init')) {
			return false;
		}

		$curl = curl_init($url);
		curl_setopt_array($curl, array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($payload),
			CURLOPT_HTTPHEADER => array_filter(array(
				'Content-Type: application/json',
				config_item('monitoring_webhook_token') !== ''
					? 'Authorization: Bearer '.config_item('monitoring_webhook_token')
					: null,
			)),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 1,
			CURLOPT_TIMEOUT => 2,
		));
		curl_exec($curl);
		$success = curl_errno($curl) === 0 && (int) curl_getinfo($curl, CURLINFO_HTTP_CODE) < 400;
		curl_close($curl);

		return $success;
	}
}
