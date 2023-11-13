<?php declare(strict_types=1);
namespace ExcelBDD;

use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

final class Behavior
{
    public static function getColumn($column, $step): string
    {
        return chr(ord($column) + $step);
    }

    public static function getExampleList(string $excelFile, string $sheetName = null, string $headerMatcher = null, string $headerUnmatcher = null): array
    {
        $spreadsheet = IOFactory::load($excelFile);
        if ($sheetName == null) {
            $sheet = $spreadsheet->getSheet(0); // read the first sheet
        } else {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (is_null($sheet)) {
                throw new Exception(
                    sprintf(
                        '"%s" sheet does not exist.',
                        $sheetName
                    )
                );
            }
        }

        $highestRow = $sheet->getHighestRow(); 
        $highestColumm = $sheet->getHighestColumn(); 

        $IsFound = False;
        $parameterRow = 0;
        $parameterCol = '1';
        for ($row = 1; $row <= $highestRow; $row++) 
        {
            for ($column = 'A'; $column <= $highestColumm; $column++) 
            {
                if (empty($sheet->getCell($column . $row)->getValue())) {
                    continue;
                }
                $cellValue = strval($sheet->getCell($column . $row)->getValue());
                if (substr($cellValue, 0, 5) == "Param") {
                    $parameterRow = $row;
                    $parameterCol = $column;
                    $IsFound = True;
                    break;
                }
            }
            if ($IsFound) {
                break;
            }
        }

        if (!$IsFound) {
            throw new Exception('Paramter Name grid is not found');
        }

        if ($sheet->getCell((chr(ord($parameterCol) + 3)) . $parameterRow)->getValue() == "Test Result") {
            $columnStep = 3;
            $actualParameterRow = $parameterRow - 1;
        } else {
            if ($sheet->getCell((chr(ord($parameterCol) + 2)) . $parameterRow)->getValue() == "Expected") {
                $columnStep = 2;
                $actualParameterRow = $parameterRow - 1;
            } else {
                $columnStep = 1;
                $actualParameterRow = $parameterRow;
            }
        }

        $parameterNames = "Parameter Names are below";
        for ($row = $parameterRow + 1; $row <= $highestRow; $row++) {
            $parameterName = $sheet->getCell($parameterCol . $row)->getValue();
            if (!(empty($parameterName) or $parameterName == "NA")) {
                $parameterNames = $parameterNames . ", $" . $parameterName;
                if ($columnStep > 1) {
                    $parameterNames = $parameterNames . ", $" . $parameterName . "Expected";
                    if ($columnStep == 3) {
                        $parameterNames = $parameterNames . ", $" . $parameterName . "TestResult";
                    }
                }
            }
        }
        echo $parameterNames, "\n";

        $testcaseSetList = array();
        for ($column = chr(ord($parameterCol) + 1); $column <= $highestColumm; $column = chr(ord($column) + $columnStep)) {
            $headerName = $sheet->getCell($column . $actualParameterRow)->getValue();
            echo $headerName, "\n";
            if (empty($headerName)) {
                continue;
            }
            // echo strstr($headerName, $headerMatcher) , "\n";
            if ($headerMatcher != null and !(strstr($headerName, $headerMatcher) != false)) {
                echo $headerName, " - not match\n";
                continue;
            }
            // echo strstr($headerName, $headerUnmatcher) , "\n";
            if ($headerUnmatcher != null and (strstr($headerName, $headerUnmatcher) != false)) {
                echo $headerName, " - excluded\n";
                continue;
            }
            $testcaseSet = array();
            for ($row = $parameterRow + 1; $row <= $highestRow; $row++) {
                $cellValue = $sheet->getCell($parameterCol . $row)->getValue();
                if (!(empty($cellValue) or $cellValue == "NA")) {
                    $testcaseSet[] = $sheet->getCell($column . $row)->getValue();
                    if ($columnStep > 1) {
                        $testcaseSet[] = $sheet->getCell(self::getColumn($column, 1) . $row)->getValue();
                        if ($columnStep == 3) {
                            $testcaseSet[] = $sheet->getCell(self::getColumn($column, 2) . $row)->getValue();
                        }
                    }
                }
            }
            $testcaseSetList[$sheet->getCell($column . $actualParameterRow)->getValue()] = $testcaseSet;
        }
        return $testcaseSetList;
    }

    public static function getExampleTable($excelFile, $sheetName = null, $headerRow = 1, $startColumn = '`'): array
    {
        if ($startColumn != '`') {
            $nStartCol = ord(strval($startColumn)) - 64;
            if ($nStartCol < 1 or $nStartCol > 26) {
                throw new Exception(
                    sprintf(
                        '"%s" is not in A~Z, Start Column must in A~Z',
                        $startColumn
                    )
                );
            }
            $actualStartColumn = $startColumn;
            $searchStartColumn = $actualStartColumn;
        } else {
            $searchStartColumn = 'A';
        }

        $spreadsheet = IOFactory::load($excelFile);
        if ($sheetName == null) {
            $sheet = $spreadsheet->getSheet(0); 
        } else {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (is_null($sheet)) {
                throw new Exception(
                    sprintf(
                        '"%s" sheet does not exist.',
                        $sheetName
                    )
                );
            }
        }

        $highestRow = $sheet->getHighestRow(); 
        $highestColumm = $sheet->getHighestColumn(); 

        $maxTableCol = 0;
        $parameterNames = "Table Parameter Names are below";

        $IsBeforeStartColumn = is_null($actualStartColumn);
        for ($column = $searchStartColumn; $column <= $highestColumm; $column++) {
            $header = $sheet->getCell($column . $headerRow)->getValue();
            $IsNullHeader = is_null($header);
            if ($IsNullHeader and $IsBeforeStartColumn) {
                continue;
            } elseif (!$IsNullHeader and $IsBeforeStartColumn) {
                $actualStartColumn = $column;
                $IsBeforeStartColumn = false;
                $parameterNames = $parameterNames . ", $" . strval($sheet->getCell($column . $headerRow)->getValue());
            } elseif ($IsNullHeader and !$IsBeforeStartColumn) {
                $maxTableCol = self::getColumn($column, -1);
                break;
            } else {
                $parameterNames = $parameterNames . ", $" . strval($sheet->getCell($column . $headerRow)->getValue());
            }
        }
        if ($maxTableCol == 0)
            $maxTableCol = $highestColumm;
        echo $parameterNames, ".\n";

        $testcaseSetList = array();
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            if (is_null($sheet->getCell($actualStartColumn . $row)->getValue()))
                break;
            $testcaseSet = array();
            for ($column = $actualStartColumn; $column <= $maxTableCol; $column++) {
                $testcaseSet[] = $sheet->getCell($column . $row)->getValue();
            }
            $testcaseSetList[] = $testcaseSet;
        }
        return $testcaseSetList;
    }
}