<?php

use PHPUnit\Framework\TestCase;

class ApiResponseTest extends TestCase
{
	public function testResponseKeepsLegacyStatusFieldsAndAddsMessage(): void
	{
		$response = new Api_response();
		$payload = $response->payload(1, 'Berhasil', array('nomor_soal' => 3));

		$this->assertSame(1, $payload['status']);
		$this->assertSame('Berhasil', $payload['pesan']);
		$this->assertSame(3, $payload['nomor_soal']);
	}

	public function testValidationErrorUsesFailureStatus(): void
	{
		$response = new Api_response();
		$this->assertSame(array('status' => 0, 'pesan' => 'Input tidak valid'), $response->validation_error('Input tidak valid'));
	}
}
