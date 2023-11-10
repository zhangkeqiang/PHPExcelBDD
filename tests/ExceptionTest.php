<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use ExcelBDD\ExcelBDD\Behavior;

final class ExceptionTest extends TestCase
{
    /** @dataProvider ExceptionExampleListProvider */
    public function testExcelbddException($ExcelFileName, $SheetName, $Message)
    {
        $excelFile = "./BDDExcel/" . $ExcelFileName;
        $this->expectException(Exception::class);
        $list = Behavior::getExampleList($excelFile, $SheetName);
    }

    public static function ExceptionExampleListProvider(): array
    {
        $excelFile = "./BDDExcel/ExcelBDD.xlsx";
        $sheetName = "Exception";
        return Behavior::getExampleList($excelFile, $sheetName);
    }
}

?>