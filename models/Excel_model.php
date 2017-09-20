<?php             
/*
file: Excel_model.php 匯出報表專用
*/                   
class Excel_model extends CI_Model 
{         
	function __construct()
	{
		parent::__construct(); 
		$this->load->database(); 
		
		$this->now_str = date('Y-m-d H:i:s'); 
		
		ini_set('max_execution_time','300');
		ini_set('memory_limit','512M');
    }   
     
	public function init($vars)
	{
		$this->vars = $vars;
    } 
	
	// 會員名單報表
	public function export_members()
	{
		trigger_error(EXPORT_LOG_TITLE. '..start..' . __FUNCTION__);
		
		// 讀入廠站資料
		$sql = "
					select
						members.member_name as member_name,
						members.lpr as lpr,
						members.contract_no as contract_no,
						members.start_date as start_date,
						members.end_date as end_date,
						members.amt as amt,
						members.update_time as update_time,
						members.member_attr,
						members.fee_period,
						members.mobile_no,
						members.deposit,
						members.suspended,
						members.locked,
						members.valid_time
					from members
					ORDER BY update_time DESC
				";
		
		$results = $this->db->query($sql)->result_array();
		
		if(empty($results))
		{
			trigger_error(EXPORT_LOG_TITLE.'..no data..' . $this->db->last_query());
			return false;
		}
		
		//$total_count = $this->db->query($sql)->num_rows();
		
		// 產生 Excel
		$this->load->library('excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$col_A_mapping = array('col_name' => 'A', 'col_title' => '會員名稱', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_B_mapping = array('col_name' => 'B', 'col_title' => '車牌號碼', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_C_mapping = array('col_name' => 'C', 'col_title' => '合約代碼', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_D_mapping = array('col_name' => 'D', 'col_title' => '開始時間', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_E_mapping = array('col_name' => 'E', 'col_title' => '結束時間', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_F_mapping = array('col_name' => 'F', 'col_title' => '租金', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_G_mapping = array('col_name' => 'G', 'col_title' => '最後更新時間', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_H_mapping = array('col_name' => 'H', 'col_title' => '身份', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_I_mapping = array('col_name' => 'I', 'col_title' => '繳期', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_J_mapping = array('col_name' => 'J', 'col_title' => '電話', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_K_mapping = array('col_name' => 'K', 'col_title' => '押金', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_L_mapping = array('col_name' => 'L', 'col_title' => '停權 (營管操作)', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_M_mapping = array('col_name' => 'M', 'col_title' => '鎖車 (會員操作)', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_N_mapping = array('col_name' => 'N', 'col_title' => '有效期限 (審核後更新)', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);

		$raw_index = 1;
		$objPHPExcel->getActiveSheet()->setTitle('下載');
		$objPHPExcel->getActiveSheet()->setCellValue($col_A_mapping['col_name'].$raw_index, $col_A_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_B_mapping['col_name'].$raw_index, $col_B_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_C_mapping['col_name'].$raw_index, $col_C_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_D_mapping['col_name'].$raw_index, $col_D_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_E_mapping['col_name'].$raw_index, $col_E_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_F_mapping['col_name'].$raw_index, $col_F_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_G_mapping['col_name'].$raw_index, $col_G_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_H_mapping['col_name'].$raw_index, $col_H_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_I_mapping['col_name'].$raw_index, $col_I_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_J_mapping['col_name'].$raw_index, $col_J_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_K_mapping['col_name'].$raw_index, $col_K_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_L_mapping['col_name'].$raw_index, $col_L_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_M_mapping['col_name'].$raw_index, $col_M_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_N_mapping['col_name'].$raw_index, $col_N_mapping['col_title']);
		
		$warning_style = array(
			//'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
			'font'  => array(
		        'bold'  => true,
				'color' => array('rgb' => 'FF0000'),
		        'size'  => 16,
		        'name'  => 'Verdana'
		    )
		);
		
		$hq_info = $this->vars['mcache']->get('info'); 

		$count = 0;
		foreach($results as $rows)
		{
			$raw_index += 1;

			$member_name = $rows['member_name'];
			$lpr = $rows['lpr'];
			$contract_no = $rows['contract_no'] ? $rows['contract_no'] : '';
			$start_date = $rows['start_date'];
			$end_date = $rows['end_date'];
			$amt = $rows['amt'] ? $rows['amt'] : '0';
			$update_time = $rows['update_time'];
			$member_attr = ( empty($hq_info['member_attr']) || empty($rows['member_attr']) || empty($hq_info['member_attr'][$rows['member_attr']]) ) ? '無' : $hq_info['member_attr'][$rows['member_attr']];
			$fee_period = ( empty($hq_info['period_name']) || empty($rows['fee_period']) || empty($hq_info['period_name'][$rows['fee_period']]) ) ? '無' : $hq_info['period_name'][$rows['fee_period']];
			$mobile_no = $rows['mobile_no'];
			$deposit = $rows['deposit'];
			$suspended = (empty($rows['suspended'])) ? '無' : '已停權';
			$locked = (empty($rows['locked'])) ? '無' : '已鎖車';
			$valid_time = $rows['valid_time'];

			$count++;

			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_A_mapping['col_name'].$raw_index, $member_name, $col_A_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_B_mapping['col_name'].$raw_index, $lpr, $col_B_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_C_mapping['col_name'].$raw_index, $contract_no, $col_C_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_D_mapping['col_name'].$raw_index, $start_date, $col_D_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_E_mapping['col_name'].$raw_index, $end_date, $col_E_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_F_mapping['col_name'].$raw_index, $amt, $col_F_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_G_mapping['col_name'].$raw_index, $update_time, $col_G_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_H_mapping['col_name'].$raw_index, $member_attr, $col_H_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_I_mapping['col_name'].$raw_index, $fee_period, $col_I_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_J_mapping['col_name'].$raw_index, $mobile_no, $col_J_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_K_mapping['col_name'].$raw_index, $deposit, $col_K_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_L_mapping['col_name'].$raw_index, $suspended, $col_L_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_M_mapping['col_name'].$raw_index, $locked, $col_M_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_N_mapping['col_name'].$raw_index, $valid_time, $col_N_mapping['col_type']);
			
			// 設定 style
			if($valid_time != $end_date)
			{
				$objPHPExcel->getActiveSheet()->getStyle($col_N_mapping['col_name'].$raw_index)->applyFromArray($warning_style);	
			}
		}
		
		// 網站下載
		$filename_prefix = iconv('UTF-8', 'Big5', '會員資料 - '. STATION_NAME);
		$filename_postfix = iconv('UTF-8', 'Big5', '(現況)');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename_prefix. ' - ' . $filename_postfix . '.xlsx');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
		trigger_error(EXPORT_LOG_TITLE . '..completed..' . __FUNCTION__ . '|count:' . $count);
		
		return true;
	}
	
    
	// 進出記錄報表
	public function export_cario_data($query_year, $query_month)
	{
		ini_set('max_execution_time','300');
		ini_set('memory_limit','512M');
		
		trigger_error(EXPORT_LOG_TITLE. '..start..' . __FUNCTION__ . "|{$query_year},{$query_month}");
		
		// 讀入廠站資料
		$sql = "
				SELECT
					cario.obj_id AS plate_no,
					cario.in_time as in_time,
					cario.out_time as out_time,
					members.member_name as member_name,
					CONCAT( FLOOR(HOUR(TIMEDIFF(cario.in_time, cario.out_time)) / 24), ' 日 ',
						MOD(HOUR(TIMEDIFF(cario.in_time, cario.out_time)), 24), ' 時 ',
						MINUTE(TIMEDIFF(cario.in_time, cario.out_time)), ' 分') as time_period
				FROM cario
					left join members on cario.member_no = members.member_no
				WHERE cario.err = 0 and cario.obj_id != 'NONE'
					and YEAR(cario.in_time) = {$query_year} and MONTH(cario.in_time) = {$query_month}
					and cario.out_time is not null
				ORDER BY cario.in_time ASC
			";
		
		$results = $this->db->query($sql)->result_array();
		
		if(empty($results))
		{
			trigger_error(EXPORT_LOG_TITLE.'..no data..' . $this->db->last_query());
			return false;
		}
		
		//$total_count = $this->db->query($sql)->num_rows();
		
		// 產生 Excel
		$this->load->library('excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$col_A_mapping = array('col_name' => 'A', 'col_title' => '車牌號碼', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_B_mapping = array('col_name' => 'B', 'col_title' => '進場時間', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_C_mapping = array('col_name' => 'C', 'col_title' => '離場日期', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_D_mapping = array('col_name' => 'D', 'col_title' => '停車時數', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_E_mapping = array('col_name' => 'E', 'col_title' => '場站名稱', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$col_F_mapping = array('col_name' => 'F', 'col_title' => '會員名稱', 'col_type' => PHPExcel_Cell_DataType::TYPE_STRING);
		$raw_index = 1;
		$objPHPExcel->getActiveSheet()->setTitle('下載');
		$objPHPExcel->getActiveSheet()->setCellValue($col_A_mapping['col_name'].$raw_index, $col_A_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_B_mapping['col_name'].$raw_index, $col_B_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_C_mapping['col_name'].$raw_index, $col_C_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_D_mapping['col_name'].$raw_index, $col_D_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_E_mapping['col_name'].$raw_index, $col_E_mapping['col_title']);
		$objPHPExcel->getActiveSheet()->setCellValue($col_F_mapping['col_name'].$raw_index, $col_F_mapping['col_title']);

		$count = 0;
		foreach($results as $rows)
		{
			$raw_index += 1;

			$plate_no = $rows['plate_no'];
			$in_time = $rows['in_time'];
			$out_time = $rows['out_time'];
			$time_period = $rows['time_period'];
			$member_name = $rows['member_name'] ? $rows['member_name'] : '臨停';

			$count++;

			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_A_mapping['col_name'].$raw_index, $plate_no, $col_A_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_B_mapping['col_name'].$raw_index, $in_time, $col_B_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_C_mapping['col_name'].$raw_index, $out_time, $col_C_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_D_mapping['col_name'].$raw_index, $time_period, $col_D_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_E_mapping['col_name'].$raw_index, STATION_NAME, $col_E_mapping['col_type']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($col_F_mapping['col_name'].$raw_index, $member_name, $col_F_mapping['col_type']); 
		}

		// 儲存檔案
		/*
		$filename_prefix = iconv('UTF-8', 'Big5', '車牌號碼進出記錄 - '. STATION_NAME);
		$filename_postfix = iconv('UTF-8', 'Big5', $query_year . '年' .$query_month.'月份');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save(EXPORT_BASE.$filename_prefix.' - '.$filename_postfix.'.xlsx');
		*/
		
		// 網站下載
		$filename_prefix = iconv('UTF-8', 'Big5', '車牌號碼進出記錄 - '. STATION_NAME);
		$filename_postfix = iconv('UTF-8', 'Big5', $query_year . '年' .$query_month.'月份');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename_prefix. ' - ' . $filename_postfix . '.xlsx');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
		trigger_error(EXPORT_LOG_TITLE . '..completed..' . __FUNCTION__ . '|count:' . $count);
		
		return true;
	}
	
	
}
