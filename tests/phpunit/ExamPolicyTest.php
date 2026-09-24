<?php

use PHPUnit\Framework\TestCase;

class ExamPolicyTest extends TestCase
{
	private $policy;

	protected function setUp(): void
	{
		$this->policy = new Exam_policy();
	}

	public function testAuthenticationOutcomesAreStable(): void
	{
		$this->assertSame(0, $this->policy->login_result(false, false));
		$this->assertSame(2, $this->policy->login_result(true, false));
		$this->assertSame(1, $this->policy->login_result(true, true));
	}

	public function testAuthorizationRequiresLoginAndPermission(): void
	{
		$this->assertFalse($this->policy->authorized(false, 1));
		$this->assertFalse($this->policy->authorized(true, 0));
		$this->assertTrue($this->policy->authorized(true, 1));
	}

	public function testTokenMustMatchExamAndBeWithinLifetime(): void
	{
		$this->assertTrue($this->policy->valid_token(0, 5, 1, 0));
		$this->assertTrue($this->policy->valid_token(5, 5, 30, 1));
		$this->assertFalse($this->policy->valid_token(6, 5, 1, 1));
		$this->assertFalse($this->policy->valid_token(5, 5, 30, 0));
	}

	public function testAnswerSubmissionRequiresActiveExam(): void
	{
		$this->assertFalse($this->policy->can_submit(0));
		$this->assertTrue($this->policy->can_submit(1));
	}
}
