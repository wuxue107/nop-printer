<?php
namespace App\Helpers;
/**
 * Created by PhpStorm.
 * User: nop
 * Date: 2020-07-30
 * Time: 11:03
 */

class CsvFile
{
    /**
     * @param        $csvFile
     * @param bool   $includeHeader
     * @param string $delimiter
     *
     * @return \Generator
     */
    public static function getCsvFileLineIterator($csvFile,bool $includeHeader = false,$delimiter = ','){
        $handle = fopen($csvFile, 'r');

        if($handle !== false){
            // 字段处理包含BOM的csv文件
            $bomHex = bin2hex(fread($handle,3));
            if(!in_array($bomHex,['efbbbf'])){
                rewind($handle);
            }

            if($includeHeader){
                $titles = fgetcsv($handle,0,$delimiter);
                $rowTpl = array_fill_keys(array_map('trim',$titles),null);
                if($titles !== false){
                    while (($data = fgetcsv($handle,0,$delimiter)) !== false) {
                        $row = $rowTpl;
                        foreach ($titles as $index => $title){
                            $row[$title] = $data[$index]??null;
                        }
                        yield $row;
                    }
                }
            }else{
                while (($data = fgetcsv($handle,0,$delimiter)) !== false) {
                    yield $data;
                }
            }

            fclose($handle);
        }
    }
}
