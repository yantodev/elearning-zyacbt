<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Aturan penilaian jawaban yang dapat diuji tanpa database atau controller.
 */
class Exam_answer_service {
	public function score_choice($is_correct, $right_score, $wrong_score){
		return $is_correct ? (float) $right_score : (float) $wrong_score;
	}

	public function score_keyword($answer, $key, $right_score, $wrong_score){
		$answer = trim((string) $answer);
		$key = trim((string) $key);

		return strcasecmp($answer, $key) === 0
			? (float) $right_score
			: (float) $wrong_score;
	}
}
