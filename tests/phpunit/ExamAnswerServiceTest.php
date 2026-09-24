<?php

use PHPUnit\Framework\TestCase;

class ExamAnswerServiceTest extends TestCase
{
	private $service;

	protected function setUp(): void
	{
		$this->service = new Exam_answer_service();
	}

	public function testChoiceAnswerUsesRightOrWrongScore(): void
	{
		$this->assertSame(4.0, $this->service->score_choice(true, 4, -1));
		$this->assertSame(-1.0, $this->service->score_choice(false, 4, -1));
	}

	public function testKeywordComparisonIsCaseInsensitiveAndTrimmed(): void
	{
		$this->assertSame(3.0, $this->service->score_keyword('  JaWaBaN ', 'jawaban', 3, 0));
		$this->assertSame(0.0, $this->service->score_keyword('salah', 'jawaban', 3, 0));
	}
}
