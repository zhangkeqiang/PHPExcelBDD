ExcelBDD
==========
Use Excel file as BDD feature file, get example data from excel files, support automation tests


[[Online Guideline](https://dev.azure.com/simplopen/ExcelBDD/_wiki/wikis/ExcelBDD.wiki/81/ExcelBDD-PHP-Guideline)]


Features
--------

The main features provided by this library are:

 * Read test data according to BDD from excel files.
 * Read test data according to Table from excel files.

Quick Start
-----------

Install the library using [composer](https://getcomposer.org):

    composer require excelbdd/excelbdd
    or
    php composer.phar require excelbdd/excelbdd
    

Read BDD test data:

```php
<?php
use PHPUnit\Framework\TestCase;
use ExcelBDD\Behavior;

final class BehaviorTest extends TestCase
{
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
}
```

Read table data:
```php
<?php
use PHPUnit\Framework\TestCase;
use ExcelBDD\Behavior;
use PHPUnit\Framework\Attributes\DataProvider;

final class BDDTest extends TestCase
{
    public static function TableDataProvider(): array
    {
        $excelFile = "../Java/src/test/resources/excelbdd/DataTable.xlsx";
        $sheetName = "DataTable2";
        return Behavior::getExampleTable($excelFile, $sheetName);
    }

    #[DataProvider('TableDataProvider')]
    public function testTableData($Header01, $Header02, $Header03, $Header04, $Header05, $Header06, $Header07, $Header08)
    {
        echo $Header01, $Header02, $Header03, $Header04, $Header05, $Header06, $Header07, $Header08, "\n";
        $this->assertStringContainsString("Value1", $Header01);
    }
}
```

For a more comprehensive introduction, please see [[Online Guideline](https://dev.azure.com/simplopen/ExcelBDD/_wiki/wikis/ExcelBDD.wiki/81/ExcelBDD-PHP-Guideline)].
