<?php use app\models\Options;

if (isset($exportdata) && $exportdata == "exportData") 
            {

            	$filename = "ProjectsbyClientCase_" . date('m_d_Y', time()) . ".xls";
				
				$objPHPExcel     = new \PHPExcel();
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.'2','Project By Client/Case');

				$objPHPExcel->getActiveSheet()->SetCellValue('B'.'2','Projects Submitted Start Date');
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.'2','Projects Submitted End Date');
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.'2','Projects Status');
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.'3',' ');

				$objPHPExcel->getActiveSheet()->SetCellValue('B'.'3',date('m/d/Y',strtotime($start_date)));
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.'3',date('m/d/Y',strtotime($end_date)));
				$sts="";
				foreach ($task_status as $st){
					 if($sts=="")$sts=$projstatus[$st]; 
					 else $sts.=",".$projstatus[$st];}
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.'3',$sts);
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.'5','Client');
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.'5','Case');
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.'5','Project#');
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.'5','Project Submitted');
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.'5','Project Status');
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.'5','Project Completed');
				$rowcount = 6;
				
				foreach($exceldata as $excel => $value){
				$completed_date = "";
				$created_date = "";
				$created_date = (new Options)->ConvertOneTzToAnotherTz($value['Created'], 'UTC', $_SESSION['usrTZ'], "requestdate");
				if($value['Completed'] != "" && $value['Completed'] != '0000-00-00 00:00:00'){
					$completed_date=(new Options)->ConvertOneTzToAnotherTz($value['Completed'], 'UTC', $_SESSION['usrTZ'], "requestdate");
				}
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowcount,$value['client']);
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowcount,$value['case']);
					$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowcount,$value['taskId']);
					$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowcount,$created_date);
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowcount,$value['task_status']);
					$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowcount,$completed_date);
					$rowcount++;
				}
							
				 header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
				 header('Content-Disposition: attachment;filename="'.$filename.'"');
				 header('Cache-Control: max-age=0');

				 $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				 $objWriter->save('php://output');
				 exit(); 
            } 

?>

