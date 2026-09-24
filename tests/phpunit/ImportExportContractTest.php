<?php

use PHPUnit\Framework\TestCase;

class ImportExportContractTest extends TestCase
{
	public function testSupportedSpreadsheetAndArchiveTypesRemainDeclared(): void
	{
		$spreadsheet_import = file_get_contents(dirname(__DIR__, 2).'/application/controllers/manager/Modul_import.php');
		$archive_import = file_get_contents(dirname(__DIR__, 2).'/application/controllers/manager/Tool_exportimport_soal.php');

		$this->assertStringContainsString("array('xlsx')", $spreadsheet_import);
		$this->assertStringContainsString("array('zip')", $archive_import);
		$this->assertStringContainsString("\$this->zip->download('zyacbt-soal.zip')", $archive_import);
	}
}
