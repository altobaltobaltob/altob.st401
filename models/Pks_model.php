<?php
/*
file: pks_model.php 	車位在席資料庫處理模組
*/

class Pks_model extends CI_Model
{
    var $vars = array();

	function __construct()
	{
		parent::__construct();
		$this->load->database();
    }

	public function init($vars)
	{
		$this->vars = $vars;
    }


    // 車輛進出傳入車牌號碼
    public function pksio($parms)
	{
    	switch($parms['io'])
        {
          	case 'KL':	// 車輛入席車辨(lpr)及圖檔
            	if ($parms['lpr'] == 'NONE')	// 在席車辨失敗, 不處理
                {
                  	trigger_error('在席車辨失敗' . print_r($parms, true));
                    return false;
                }

            	// 讀取在席資料(pks)
                $rows_pks = $this->db
        					->select('cario_no, lpr, status, confirms')
        					->from('pks')
                			->where(array('pksno' => $parms['pksno'], 'station_no' => $this->vars['station_no']))
                  			->limit(1)
                			->get()
                			->row_array();

                trigger_error('KL read pks:'.print_r($rows_pks, true));

                // 如果已經人工確認或之前已比對有入場資料者, 則重覆再送來的車辨不予理會
				if ($rows_pks['confirms'] == 1 || $rows_pks['lpr'] == $parms['lpr'])
                {
                	trigger_error('KL ignored:'.$rows_pks['lpr']);
                	return false;
                }
				/*
                if ($rows_pks['cario_no']  != 0 || $rows_pks['confirms'] == 1 || $rows_pks['lpr'] == $parms['lpr'])
                {
                	trigger_error('人工已確認或車號相同不更新pks:'.$rows_pks['lpr']);
                	return false;
                }
				*/

            	// 讀取進場時間, 如讀不到資料, 以目前時間取代(add by TZUSS 2016-02-23)
            	$rows_cario = $this->db
        					->select('cario_no, in_time')
        					->from('cario')
                			->where(array('in_out' => 'CI', 'obj_id' => $parms['lpr'], 'finished' => 0, 'err' => 0, 'station_no' => $this->vars['station_no']))
                  			->order_by('cario_no', 'desc')
                  			->limit(1)
                			->get()
                			->row_array();
                if (!empty($rows_cario['cario_no']))		// 有入場資料
                {
                 	$cario_no = $rows_cario['cario_no'];	// 入場序號
                 	$in_time = $rows_cario['in_time'];
                    // 在席與入場資料相符, 分別在cario與pks記錄之
                    $data_cario = array
                    (
                    	'pksno' => $parms['pksno'],
                        'pks_time' => date('Y-m-d H:i:s')
                    );
                    $this->db->update('cario', $data_cario, array('cario_no' => $cario_no, 'station_no' => $this->vars['station_no']));
                }
                else	// 查無入場資料, 即時通知
                {
                	$cario_no = 0;
                 	$in_time = date('Y-m-d H:i:s');
                    $jdata = json_encode(array
                    (
                    	'pksno' => $parms['pksno'],
                        'lpr' => $parms['lpr'],
                        'in_time' => $in_time,
                        'pic_name' => $parms['pic_name']
                    ), JSON_UNESCAPED_UNICODE);
                    // $this->vars['mqtt']-lish('PKS_WITHOUT_IN', "{$jdata}", 0);	// 待web完成 ???
                    trigger_error('在席無進場資料:'. print_r($parms, true));
                }

                // 車入格後的車牌辨識(lpr), 傅送圖檔
        		array_map('unlink', glob(PKS_PIC."pks-{$parms['pksno']}-*.jpg"));	// 刪除舊照片
        		$config['upload_path'] = PKS_PIC;
        		$config['allowed_types'] = 'gif|jpg|png';
        		// ex. pks-2016-1625AB-1-2015080526.jpg -> pks-車位編號-車號-設備編號-時間.jpg
	        	$config['file_name'] = "pks-{$parms['pksno']}-{$parms['lpr']}-{$parms['ivsno']}-{$this->vars['time_num']}.jpg";
        		$this->load->library('upload', $config);

            	$parms['pic_name'] = $config['file_name'];
        		if($this->upload->do_upload('cars'))
            	{
            		// 若無錯誤，則上傳檔案
            		$file = $this->upload->data('cars');
        		}
        		else
        		{
            		trigger_error('入席傳檔錯誤:'. print_r($parms, true));
        		}

        		$data = array
            	(
                    'cario_no' => $cario_no,
              		'lpr' => $parms['lpr'],
                	'status' => 'LR',	// 車格佔用並有車號
                    'confirms' => 0,    // 預設人工未確認
                	'pic_name' => $parms['pic_name'],
                    'in_time' => $in_time
            	);
            	// 車號及照片檔名填入資料庫內
            	$this->db->update('pks', $data, array('pksno' => $parms['pksno'], 'station_no' => $this->vars['station_no']));
            	break;

          	case 'KI':	// 車輛入席, 各區空車位與佔位各加減1
    			$rows = $this->db->select('status')
        			->from('pks')
                    ->where(array('pksno' => $parms['pksno'], 'station_no' => $this->vars['station_no']))
                    ->get()
                    ->row_array();
                // if (!empty($rows['status']) && $rows['status'] == 'LR')	break;	// 仍有車在席, 不應再有KI, ignore
                if (!empty($rows['status']) && $rows['status'] == 'LR')	return true;	// 仍有車在席, 不應再有KI, ignore

        		$data = array
            	(
                	'cario_no' => 0,
              		'lpr' => '',
                	'status' => 'OC',	// 車格佔用但尚無車號
                    'confirms' => 0,
                	'pic_name' => '',
                    'in_time' => null
            	);
            	$this->db->update('pks', $data, array('pksno' => $parms['pksno'], 'station_no' => $this->vars['station_no']));
            	break;

          	case 'KO':	// 車輛離席, 各區空車位與佔位各加減1
        		$data = array
            	(
                	'cario_no' => 0,
              		'lpr' => '',
                	'status' => 'VA',	// 車格佔用但尚無車號
                    'confirms' => 0,
                	'pic_name' => '',
                    'in_time' => null
            	);
            	$this->db->update('pks', $data, array('pksno' => $parms['pksno'], 'station_no' => $this->vars['station_no']));
            	break;
        }

		/*
        // 找出與與此車位相關的群組
    	$sql = "select group_id, tot, renum
        		from pks_groups
				where group_id in
        		(select group_id from pks_group_member where station_no = {$this->vars['station_no']} and pksno = {$parms['pksno']})";

        $retults = $this->db->query($sql)->result_array();

        foreach ($retults as $rows)
        {
        	// 計算群組異動後的空車位數, 先讀出已停車位數
        	$sql = "select count(*) as parked from pks where status != 'VA' and pksno in (select pksno from pks_group_member where group_id = '{$rows['group_id']}')";
            $row_group = $this->db->query($sql)->row_array();
            $group_va = $rows['tot'] + $rows['renum'] -  $row_group['parked'];	// 群組空車位數
            $this->db->update('pks_groups', array('parked' => $row_group['parked'], 'availables' => $group_va), array('group_id' => $rows['group_id']));

            get_headers("http://192.168.51.15/set_num.php?group_id={$rows['group_id']}&num={$group_va}");
            // $this->vars['mqtt']->publish("VA-{$rows['group_id']}", "{$group_va}", 0);			// 送出剩餘車位數給字幕機
            // 總車位數暫無需處理
        }
		*/
		// 找出與與此車位相關的群組
    	$sql = "select group_id, tot, renum, availables
        		from pks_groups
				where group_id in
        		(select group_id from pks_group_member where station_no = {$this->vars['station_no']} and pksno = {$parms['pksno']})";

        $retults = $this->db->query($sql)->result_array();

        foreach ($retults as $rows)
        {
        	// 計算群組異動後的空車位數, 先讀出已停車位數
        	$sql = "select count(*) as parked from pks where status != 'VA' and pksno in (select pksno from pks_group_member where group_id = '{$rows['group_id']}')";
            $row_group = $this->db->query($sql)->row_array();
            $group_va = $rows['tot'] + $rows['renum'] -  $row_group['parked'];	// 群組空車位數

			// 有變動才處理更新
			if($rows['availables'] != $group_va) 
			{
				// 防止負值
				if($group_va < 0){
					$group_va = 0;
				}
				
				$group_va_pad = str_pad($group_va, 3, '0', STR_PAD_LEFT); // 補零
				$this->db->update('pks_groups', array('parked' => $row_group['parked'], 'availables' => $group_va), array('group_id' => $rows['group_id']));

				$this->vars['mqtt']->publish(MQ_TOPIC_SUBLEVEL, "{$rows['group_id']},{$group_va_pad}", 0);			// 送出剩餘車位數給字幕機
				// 總車位數暫無需處理

				// 七樓無在席, 手動或用猜的
				/*
				$f7_total = 74;
				$sql = "select renum from pks_groups where group_id = 'F7'";
				$row_group = $this->db->query($sql)->row_array();
				$f7_renum = $row_group['renum'];
				$total_parked_sql = "select count(cario_no) as parked 
					from cario where 
					cario.in_time > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 5 DAY) 
					and cario.finished = 0 
					and cario.err = 0 
					and cario.in_out = 'CI' 
					and cario.out_time is null";
				$total_parked_row_group = $this->db->query($total_parked_sql)->row_array();
				$sql = "select count(pksno) as parked from pks where status != 'VA'";
				$row_group = $this->db->query($sql)->row_array();
				$f7_mqtt = $total_parked_row_group['parked'] - $row_group['parked'];
				if($f7_mqtt > $f7_total){
				  $f7_mqtt = $f7_total;
				}else if($f7_mqtt <= 0){
				  $f7_mqtt = 0;
				}
				$this->db->update('pks_groups', array('parked' => $f7_mqtt, 'availables' => $f7_total - $f7_mqtt + $f7_renum), array('group_id' => 'F7'));
				$f7_mqtt_pad = str_pad($f7_total - $f7_mqtt + $f7_renum, 3, '0', STR_PAD_LEFT);
				
				$this->vars['mqtt']->publish(MQ_TOPIC_SUBLEVEL, "F7,{$f7_mqtt_pad}", 0);			// F7 MQTT
				*/
			}
        }
    }


    // 重新計算
    public function reculc()
	{
        // 找出與與此車位相關的群組
    	$sql = "select group_id, tot, renum
        		from pks_groups";

        $retults = $this->db->query($sql)->result_array();

        foreach ($retults as $rows)
        {
        	// 計算群組異動後的空車位數, 先讀出已停車位數
        	$sql = "select count(*) as parked from pks where status != 'VA' and pksno in (select pksno from pks_group_member where group_id = '{$rows['group_id']}')";
            $row_group = $this->db->query($sql)->row_array();
            $group_va = $rows['tot'] + $rows['renum'] -  $row_group['parked'];	// 群組空車位數
            $this->db->update('pks_groups', array('parked' => $row_group['parked'], 'availables' => $group_va), array('group_id' => $rows['group_id']));

            // $this->vars['mqtt']->publish("VA-{$rows['group_id']}", "{$group_va}", 0);			// 送出剩餘車位數給字幕機

            get_headers("http://192.168.51.15/set_num.php?group_id={$rows['group_id']}&num={$group_va}");

            echo "group_id:{$rows['group_id']}, tot:{$rows['tot']}, availables:{$group_va}, parked:{$row_group['parked']}, renum:{$rows['renum']}<br />";
        }
    }

	// 取得所有車位使用狀態
	public function query_station_status($station_no)
	{
		/* 沒有group_id, pks不能直接用, 要多撈兩張表

		$sql = "select pksno, posx, posy, in_time
        		FROM pks
				WHERE station_no = '".$station_no."' and lpr != ''";
		*/
		$sql = "SELECT 	pks.pksno AS pksno, pks.posx AS posx, pks.posy AS posy, pks.in_time AS in_time,
						pks_groups.group_id AS group_id
        		FROM pks
				LEFT JOIN pks_group_member ON pks.pksno = pks_group_member.pksno AND pks.station_no = pks_group_member.station_no
				LEFT JOIN pks_groups ON pks_group_member.group_id = pks_groups.group_id
				WHERE pks.lpr != '' AND pks.station_no = '".$station_no."' AND pks_groups.group_type = '1' ";

        $retults = $this->db->query($sql)->result_array();

		$currentTime = new DateTime("now");
		foreach ($retults as $idx => $rows)
        {
			$startTime = new DateTime($rows['in_time']); // 進場時間
			$interval = $startTime->diff($currentTime);
			$status = $this->gen_pks_s($interval); // 一般:0, 隔日:1, 超過3日:3, 隔週:7, 隔20日:20

			$data['result'][$idx] = array
            (
				'g'=> $rows['group_id'],
				'id'=> $rows['pksno'],
            	'x' => $rows['posx'],
            	'y' => $rows['posy'],
				's' => $status
            );
		}
		return $data;
	}

	// 取得車位狀態
	private function gen_pks_s($interval)
	{
		$status = 0; // 一般:0, 隔日:1, 超過3日:3, 隔週:7, 隔20日:20
		if($interval->y > 0 || $interval->m > 0 || $interval->d >= 20){
			$status = 20;
		}else if($interval->d >= 7){
			$status = 7;
		}else if($interval->d >= 3){
			$status = 3;
		}else if($interval->d >= 1){
			$status = 1;
		}
		return $status;
	}

	// 取得指定車位使用狀態
	public function query_station_pks($station_no, $pksno)
	{
		$sql = "SELECT 	pks.pksno AS pksno, pks.lpr AS lpr, pks.in_time AS in_time, pks.station_no AS station_no,
						pks_groups.group_id AS group_id, pks_groups.group_name AS group_name, pks_groups.group_type AS type
        		FROM pks
				LEFT JOIN pks_group_member ON pks.pksno = pks_group_member.pksno AND pks.station_no = pks_group_member.station_no
				LEFT JOIN pks_groups ON pks_group_member.group_id = pks_groups.group_id
				WHERE pks.pksno = '".$pksno."' AND pks.station_no = '".$station_no."' AND pks_groups.group_type = '1' ";

        $retults = $this->db->query($sql)->result_array();

		$currentTime = new DateTime("now");
		foreach ($retults as $idx => $rows)
        {
			$startTime = new DateTime($rows['in_time']); // 進場時間
			$interval = $startTime->diff($currentTime);
			$status = $this->gen_pks_s($interval); // 一般:0, 隔日:1, 超過3日:3, 隔週:7, 隔20日:20

			$data['result'][$idx] = array
            (
				'pksno'=> $rows['pksno'],
				'lpr' => $rows['lpr'],
				'time' => $rows['in_time'],
				'station_no' => $rows['station_no'],
				'group_id' => $rows['group_id'],
				'group_name' => $rows['group_name'],
				'status' => $status
            );
		}
		return $data;
	}

}
