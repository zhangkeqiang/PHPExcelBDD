<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ExcelBDD\ExcelBDD\Behavior;

final class BehaviorTest extends TestCase
{
    public function testgetExampleList()
    {
        $excelFile = "./BDDExcel/ExcelBDD.xlsx";
        $sheetName = "Sheet1";
        $arrayExample = Behavior::getExampleList($excelFile, $sheetName);
        $this->assertSame(5, count($arrayExample));
    }

    public static function BehaviorgetExampleListProvider(): array
    {
        $excelFile = "./BDDExcel/ExcelBDD.xlsx";
        $sheetName = "Sheet1";
        return Behavior::getExampleList($excelFile, $sheetName);
    }

    /** @dataProvider BehaviorgetExampleListProvider */
    public function testBehaviorgetExampleList($ParamName1, $ParamName2, $ParamName3, $ParamName4)
    {
        echo $ParamName1, $ParamName2, $ParamName3, $ParamName4, "\n";
        $this->assertSame(strpos($ParamName1, "V1"), 0);
        $this->assertSame(strpos($ParamName2, "V2"), 0);
    }

    public function testgetExampleListWithMatchers()
    {
        $excelFile = "./BDDExcel/ExcelBDD.xlsx";
        $sheetName = "SmartBDD";
        $testcaseSetList = Behavior::getExampleList($excelFile, $sheetName, "easy", "V1.1");
        print_r($testcaseSetList);
        $this->assertSame(1, count($testcaseSetList));

        $testcaseSetList2 = Behavior::getExampleList($excelFile);
        print_r($testcaseSetList2);
        $this->assertSame(8, count($testcaseSetList2));
    }

    public static function SmartBDDgetExampleListProvider(): array
    {
        $excelFile = "./BDDExcel/ExcelBDD.xlsx";
        $sheetName = "SmartBDD";
        return Behavior::getExampleList($excelFile, $sheetName);
    }

    /** @dataProvider SmartBDDgetExampleListProvider */
    public function testSmartBDDgetExampleList($ExcelFileName, $SheetName, $HeaderMatcher, $HeaderUnmatcher, $Header1Name, $FirstGridValue, $LastGridValue, $ParamName1InSet2Value, $ParamName2InSet2Value, $ParamName3Value, $MaxBlankThreshold, $ParameterCount, $TestDataSetCount)
    {
        echo $ExcelFileName, $SheetName, $HeaderMatcher, $HeaderUnmatcher, $Header1Name, $FirstGridValue, $LastGridValue, $ParamName1InSet2Value, $ParamName2InSet2Value, $ParamName3Value, $MaxBlankThreshold, $ParameterCount, $TestDataSetCount, "\n";
        $testcaseSetList = Behavior::getExampleList("./BDDExcel/" . $ExcelFileName, $SheetName, $HeaderMatcher, $HeaderUnmatcher);
        print_r($testcaseSetList);
        $this->assertSame($TestDataSetCount, count($testcaseSetList));
        $paramLen = count($testcaseSetList["Scenario1"]);
        echo $paramLen, "\n";
        $step = $paramLen / 4;
        echo $step, "\n";
        $this->assertSame($testcaseSetList["Scenario1"][0], $FirstGridValue);
        $this->assertSame($testcaseSetList["Scenario2"][0], "V1.2");
        $this->assertSame($testcaseSetList["Scenario3"][0], "V1.3");
        $this->assertSame($testcaseSetList["Scenario4"][0], "V1.4");

        $this->assertSame($testcaseSetList["Scenario1"][$step * 1], "V2.1");
        $this->assertSame($testcaseSetList["Scenario2"][$step * 1], "V2.2");
        $this->assertSame($testcaseSetList["Scenario3"][$step * 1], "V2.3");
        $this->assertSame($testcaseSetList["Scenario4"][$step * 1], "V2.4");

        $this->assertSame($testcaseSetList["Scenario1"][$step * 2], null);
        $this->assertSame($testcaseSetList["Scenario2"][$step * 2], null);
        $this->assertSame($testcaseSetList["Scenario3"][$step * 2], null);
        $this->assertSame($testcaseSetList["Scenario4"][$step * 2], null);

        $this->assertSame($testcaseSetList["Scenario1"][$step * 3], "2021/4/30");
        $this->assertSame($testcaseSetList["Scenario2"][$step * 3], False);
        $this->assertSame($testcaseSetList["Scenario3"][$step * 3], True);
        $this->assertSame($testcaseSetList["Scenario4"][$step * 3], $LastGridValue);
    }

    public function testgetExampleListOnSBTFormat()
    {
        $excelFile = "./BDDExcel/ExcelBDD.xlsx";
        $sheetName = "SBTSheet1";
        $testcaseSetList = Behavior::getExampleList($excelFile, $sheetName);
        print_r($testcaseSetList);
        $this->assertSame(5, count($testcaseSetList));
    }

    public function testgetExampleListOnExpectedFormat()
    {
        $excelFile = "./BDDExcel/ExcelBDD.xlsx";
        $sheetName = "Expected1";
        $testcaseSetList = Behavior::getExampleList($excelFile, $sheetName);
        print_r($testcaseSetList);
        $this->assertSame(4, count($testcaseSetList));
    }

    public function testgetColumn()
    {
        $this->assertSame('E', Behavior::getColumn('A', 4));
    }
}

?>