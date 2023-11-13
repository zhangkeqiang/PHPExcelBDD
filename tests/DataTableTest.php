<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use ExcelBDD\Behavior;

final class DataTableTest extends TestCase
{
    public static function DataTableExampleListProvider(): array
    {
        $excelFile = "./BDDExcel/DataTableBDD.xlsx";
        $sheetName = "DataTableBDD";
        return Behavior::getExampleList($excelFile, $sheetName);
    }

    /** @dataProvider DataTableExampleListProvider */
    public function testDataTable($ExcelFileName, $SheetName, $HeaderRow, $StartColumn, $TestSetCount, $FirstGridValue, $LastGridValue, $ColumnCount, $Header03InThirdSet)
    {
        $excelFile = "./BDDExcel/" . $ExcelFileName;
        echo $ExcelFileName, $SheetName, $HeaderRow, $StartColumn, $TestSetCount, $FirstGridValue, $LastGridValue, $ColumnCount, $Header03InThirdSet, "\n";
        $testcaseSetList = Behavior::getExampleTable($excelFile, $SheetName, $HeaderRow, $StartColumn);
        print_r($testcaseSetList);
        $this->assertSame($TestSetCount, count($testcaseSetList));
        $this->assertSame($ColumnCount, count($testcaseSetList[0]));
        $this->assertSame($FirstGridValue, $testcaseSetList[0][0]);
        $this->assertSame($LastGridValue, $testcaseSetList[5][7]);
        $this->assertSame($Header03InThirdSet, $testcaseSetList[2][2]);
    }

    public static function DataTableExampleListProvider2(): array
    {
        $excelFile = "./BDDExcel/DataTableBDD.xlsx";
        $sheetName = "DataTableBDD";
        return Behavior::getExampleList($excelFile, $sheetName, "FirstRow");
    }
    /** @dataProvider DataTableExampleListProvider2 */
    public function testDataTable2($ExcelFileName, $SheetName, $HeaderRow, $StartColumn, $TestSetCount, $FirstGridValue, $LastGridValue, $ColumnCount, $Header03InThirdSet)
    {
        $excelFile = "./BDDExcel/" . $ExcelFileName;
        echo $ExcelFileName, $SheetName, $HeaderRow, $StartColumn, $TestSetCount, $FirstGridValue, $LastGridValue, $ColumnCount, $Header03InThirdSet, "\n";
        $testcaseSetList = Behavior::getExampleTable($excelFile, $SheetName);
        print_r($testcaseSetList);
        $this->assertSame($TestSetCount, count($testcaseSetList));
        $this->assertSame($ColumnCount, count($testcaseSetList[0]));
        $this->assertSame($FirstGridValue, $testcaseSetList[0][0]);
        $this->assertSame($LastGridValue, $testcaseSetList[5][7]);
        $this->assertSame($Header03InThirdSet, $testcaseSetList[2][2]);
    }

    public static function DataTableExceptionistProvider(): array
    {
        $excelFile = "./BDDExcel/DataTableBDD.xlsx";
        $sheetName = "Exception";
        return Behavior::getExampleList($excelFile, $sheetName);
    }

    /** @dataProvider DataTableExceptionistProvider */
    public function testDataTableException($ExcelFileName, $SheetName, $HeaderRow, $StartColumn, $ExpectedMessage)
    {
        if ($ExpectedMessage == "No such file")
            $ExpectedMessage = "does not exist";
        $excelFile = "./BDDExcel/" . $ExcelFileName;
        try {
            $list = Behavior::getExampleTable($excelFile, $SheetName, $HeaderRow, $StartColumn);
        } catch (Exception $e) {
            $ExceptionMessage = $e->getMessage();
        }
        $this->assertStringContainsString($ExpectedMessage, $ExceptionMessage);
    }

    public function testDataTableDefaultSheet()
    {
        $excelFile = "./BDDExcel/DataTableSample.xlsx";
        $testcaseSetList = Behavior::getExampleTable($excelFile);
        print_r($testcaseSetList);
        $this->assertSame(6, count($testcaseSetList));
        $this->assertSame(8, count($testcaseSetList[0]));
        $this->assertSame("Value1.1", $testcaseSetList[0][0]);
        $this->assertSame("Value8.6", $testcaseSetList[5][7]);
        $this->assertSame("Value3.3", $testcaseSetList[2][2]);
    }

}