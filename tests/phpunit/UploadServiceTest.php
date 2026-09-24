<?php

use PHPUnit\Framework\TestCase;

class UploadServiceTest extends TestCase
{
	private $base;
	private $service;

	protected function setUp(): void
	{
		$this->base = sys_get_temp_dir().DIRECTORY_SEPARATOR.'zyacbt-phpunit-'.bin2hex(random_bytes(5));
		mkdir($this->base, 0775, true);
		$this->service = new Upload_service(array('base_path' => $this->base));
	}

	protected function tearDown(): void
	{
		if (is_dir($this->base)) {
			rmdir($this->base);
		}
	}

	public function testPathTraversalIsRejected(): void
	{
		$this->assertFalse($this->service->normalize_relative_path('../outside'));
		$this->assertFalse($this->service->normalize_filename('../config.php'));
		$this->assertFalse($this->service->remove('../', 'outside.txt'));
	}

	public function testSafeNamesAreNormalized(): void
	{
		$this->assertSame('topik_12'.DIRECTORY_SEPARATOR.'images', $this->service->normalize_relative_path('topik_12/images'));
		$this->assertSame('gambar_01.jpg', $this->service->normalize_filename('Gambar 01.jpg'));
	}
}
