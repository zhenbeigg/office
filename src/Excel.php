<?php
/*
 * @author: 布尔
 * @name:  excel处理类
 * @desc:  excel文件导入导出
 * @Date: 2020-04-20 10:29:00
 */

namespace Eykj\Office;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel 
{  
    /**
     * @author: 布尔
     * @name: 解析excel
     * @param {array} $param 文件信息
     * @param {array} $key_arr 表头
     * @param {int} $key 从第几行开始
     * @return {array}
     */     
    public function importExcel(array $param,array $key_arr,int $key=3):array
    {
        $filename = $param['file']->getpathName();
        $excel = IOFactory::load($filename);
        $sheet = $excel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $import_data = array();
        for($i = $key;$i <= $highestRow;$i++) {
            foreach($key_arr as  $k=>$v){
                $data[$v] = trim($sheet->getCellByColumnAndRow($k, $i)->getValue());
            }
            array_push($import_data, $data);
        }
        return $import_data;
    }

    /**
     * @author: 布尔
     * @name: 方法名
     * @param {array} $data 数据信息
     * @param {string} $filename 文件名称
     * @param {string} $format 文件后缀
     */    
    public function exportExcel(array $data,string $filename= '',string $format='Xls') 
    {
        /* 英文A-Z数组*/
        $earr = range('A','Z');
        /* 创建一个新的excel文档 */
        $newExcel = new Spreadsheet(); 
        /*  获取当前操作sheet的对象*/
        $objSheet = $newExcel->getActiveSheet(); 
        /* 设置当前sheet的标题 */
        $objSheet->setTitle($filename);  
        
        /*设置宽度为true,不然太窄了*/
        foreach($earr as $k=>$v){
        if($k+1>=count($data[0])){break;/*跳出循环 */}
            $objSheet->getColumnDimension($v)->setAutoSize(true);
        }
        /* 设置第一行标题 */
        $objSheet->mergeCells('A1:'.$v.'1');      //合并单元格
        $objSheet->setCellValue("A1",$filename.'  '.date('Y-m-d H:i:s'));

        $i = 0;
        /* 设置第二栏的标题 */
        foreach($data[0] as $k1=>$v1){
            $objSheet->setCellValue($earr[$i].'2',$k1);
            $i++;
        }
        /*  第三行起,插入数据。*/
        foreach ($data as $k2 => $v2) {
            $k3 = $k2 + 3;
            $i = 0;
            foreach($v2 as $v4){
                $objSheet->setCellValue($earr[$i].$k3,$v4.' ');
                $i++;
            }
        }
    
        return $this->downloadExcel($newExcel,$filename,$format);
    }
    
    /**
     * @author: 布尔
     * @name: 传入xls并下载
     * @param {object} $newExcel excel类
     * @param {string} $filename 文件名称
     * @param {string} $format 文件后缀
     */    
    public function downloadExcel(object $newExcel,string $filename,$format)
    {
        // if ($format == 'Xlsx') {
        //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // } elseif ($format == 'Xls') {
        //     header('Content-Type: application/vnd.ms-excel');
        // }
        // header("Content-Disposition: attachment;filename=".$filename.'.' . strtolower($format));
        // header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($newExcel,$format);
        // $objWriter->save('php://output');
        /* 判断路径是否存在 */
        $dir = BASE_PATH.'/public/upload';
        if(!is_dir($dir)){
            mkdir($dir,0755,true);
        }
        $filename =  $dir.'/'.$filename. '.' . strtolower($format);
        $objWriter->save($filename);
        return $filename;
    }
}
