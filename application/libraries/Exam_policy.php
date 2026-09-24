<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Aturan akses dan status ujian yang sebelumnya tersebar di controller.
 */
class Exam_policy {
	public function login_result($user_found, $password_matches){
		if (!$user_found) {
			return 0;
		}

		return $password_matches ? 1 : 2;
	}

	public function can_access($permission_count){
		return (int) $permission_count > 0;
	}

	public function authorized($logged_in, $permission_count){
		return (bool) $logged_in && $this->can_access($permission_count);
	}

	public function valid_token($token_test_id, $test_id, $active_minutes, $usage_count){
		if ((int) $token_test_id !== 0 && (string) $token_test_id !== (string) $test_id) {
			return false;
		}

		return (int) $active_minutes === 1 || (int) $usage_count > 0;
	}

	public function can_submit($active_test_count){
		return (int) $active_test_count > 0;
	}
}
