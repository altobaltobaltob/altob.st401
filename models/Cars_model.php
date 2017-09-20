<?php
/*
file: cars_model.php
*/
require_once(ALTOB_SYNC_FILE) ;

define('CARS_TMP_LOG', 'cars_tmp_log');	// 暫存進出車號

class Cars_model extends CI_Model
{
    var $vars = array();

    var $io_name = array('I' => '車入', 'O' => '車出', 'MI' => '機入', 'MO' => '機出', 'FI' => '樓入', 'FO' => '樓出');
    var $now_str;

	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->now_str = date('Y-m-d H:i:s');
    }

	public function init($vars)
	{
    	$this->vars = $vars;
    }

	// 車輛進出傳入車牌號碼 (2016/07/27)
    public function opendoor_lprio($parms)
	{
		$parms['lpr'] = urldecode($parms['lpr']);

    	$rows = array();
        // $parms['ts'] = date('Y-m-d H:i:s', $parms['ts']);
    	trigger_error(__FUNCTION__ . '|車牌傳入:' . print_r($parms, true));

        if ($parms['etag'] != 'NONE')
        {
          	if ($parms['lpr'] != 'NONE')
            {
            	// do nothing
            }
            else	// 車辨失敗但有eTag, 查詢是否有車號
            {
            	//$parms['lpr'] = $this->etag2lpr_2($parms['etag']); // 2017/01/10 預設都不用 ETAG 找車牌
            }
        }

        $rows = $this->get_member($parms['lpr']);

        $this->save_db_io($parms, $rows, true);
        return true;
    }

    // 車輛進出傳入車牌號碼
    public function lprio($parms)
	{
		//$parms['lpr'] = urldecode($parms['lpr']);

    	$rows = array();
        // $parms['ts'] = date('Y-m-d H:i:s', $parms['ts']);
    	trigger_error('車牌傳入:' . print_r($parms, true));

        if ($parms['etag'] != 'NONE')
        {
          	if ($parms['lpr'] != 'NONE')
            {
            	// 有車牌有eTag, 檢查資料庫是否double驗證
              	//get_headers("http://192.168.10.201/cars.html/check_lpr_etag/{$parms['lpr']}/{$parms['etag']}");
				get_headers("http://localhost/cars.html/check_lpr_etag/{$parms['lpr']}/{$parms['etag']}"); // update 2016/07/26
            }
            else	// 車辨失敗但有eTag, 查詢是否有車號
            {
            	// $parms['lpr'] = $this->etag2lpr_2($parms['etag']); // 2017/01/10 預設都不用 ETAG 找車牌
            }
        }

        $rows = $this->get_member($parms['lpr']);

        $this->save_db_io($parms, $rows);
        return true;
    }


    // 入出口異動cario
    public function save_db_io($parms, $rows, $opendoor=false)
	{
        if (!empty($rows['lpr_correct'])) $parms['lpr'] = $rows['lpr_correct'];
		
		// [START] 擋重覆 20170912 前端不止一筆 opendoor 送來時, 只處理第一個 （限 2 sec 內）
		if($opendoor)
		{
			$skip_or_not = false;
			$new_cars_tmp = array
			(
				'timestamp' => time(),
				'sno_io' => $parms['sno'] . $parms['io'],
				'lpr' => $parms['lpr']
			);
			$cars_tmp_arr = array();
			$cars_tmp_log_arr = $this->vars['mcache']->get(CARS_TMP_LOG);
			if(!empty($cars_tmp_log_arr))
			{
				foreach($cars_tmp_log_arr as $tmp)
				{
					if(isset($tmp['timestamp']) && $tmp['timestamp'] > time() - 2) // 時限內才判斷
					{
						array_push($cars_tmp_arr, $tmp);
					}
				}
			}
			
			// 判斷是否繼續
			foreach($cars_tmp_arr as $tmp)
			{
				if(	$new_cars_tmp['lpr'] == $tmp['lpr'] && 
					$new_cars_tmp['sno_io'] == $tmp['sno_io'])
				{
					$skip_or_not = true;
				}
			}
			
			// 更新
			array_push($cars_tmp_arr, $new_cars_tmp);
			$this->vars['mcache']->set(CARS_TMP_LOG, $cars_tmp_arr);
			trigger_error(__FUNCTION__ . '..new ' . CARS_TMP_LOG . " |s:{$skip_or_not}|" . print_r($cars_tmp_arr, true));	
			
			// 跳過
			if($skip_or_not)
			{
				trigger_error(__FUNCTION__ . '..skip..');	
				return false;
			}
		}
		// [END] 擋重覆

		// 車辨失敗, 結束
        if ($parms['lpr'] == 'NONE')
        {
			if($opendoor)
			{
				$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",1,{$parms['ivsno']}".MQ_ALTOB_MSG_END_TAG);
				// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},車辨失敗");
				
				if(substr($parms['io'], -strlen('O')) === 'O')
				{
					// [acer] cmd:102 離場車辨失敗流程 START
					$this->call_acer('102', array('cario_no' => 0, 'ivs_no' => $parms['ivsno'], 'msg_code' => 1));
					// [acer] cmd:102 離場車辨失敗流程 END
				}
			}
			else
			{
				if(substr($parms['io'], -strlen('I')) === 'I')
				{
					// [acer] cmd:101 入場車辨失敗流程 START
					$ticket_no = $this->gen_pass_code();
					
					$data = array
					(
						'station_no' => $parms['sno'],
						'obj_type' => 1,
						'obj_id' => $parms['lpr'],
						'etag' => $parms['etag'] == 'NONE' ? '' : $parms['etag'],
						'in_out' => $parms['io'],
						'member_no' => 0,
						'finished' => 0,
						'in_time' => $this->now_str,
						'in_lane' => $parms['ivsno'],
						'in_pic_name' => empty($parms['pic_name']) ? '' : $parms['pic_name'],
						'out_before_time' => date("Y-m-d H:i:s"),
						'ticket_no' => $ticket_no
					);
					$this->db->insert('cario', $data);
					trigger_error("[車辨失敗] 新增入場資料:".print_r($parms, true));
					
					$acer_parms = array
					(
						'cario_no' => $this->db->insert_id(),
						'in_time' => $this->now_str,
						'ticket_no' => $ticket_no,
						'lpr' => '',
						'ivs_no' => $parms['ivsno']
					);
					$this->call_acer('101', $acer_parms);
					// [acer] cmd:101 入場車辨失敗流程 END
				}
			}
        	return false;
        }

        $msg = $rows['member_no'] != 0 ? "{$parms['lpr']}." : $parms['lpr'];	// 月租車號加.符號

        // 月租鎖車, 結束
        //if (($parms['io'] == 'CO' || $parms['io'] == 'MO') && $rows['member_no'] != 0 && !empty($rows['locked']) && $rows['locked'] == 1)
		if ((substr($parms['io'], -strlen('O')) === 'O') && $rows['member_no'] != 0 && !empty($rows['locked']) && $rows['locked'] == 1)
        {
        	if($opendoor){
				//// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}已鎖車!");
				$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",2,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
				// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}禁止離場請後退");
				
				// [acer] cmd:102 離場車辨已鎖車 START
				$this->call_acer('102', array('cario_no' => 0, 'ivs_no' => $parms['ivsno'], 'msg_code' => 2));
				// [acer] cmd:102 離場車辨已鎖車 END
			}
        	return false;
        }

        // 取得會員資訊
        $parms['member_no'] = $rows['member_no'];

		switch($parms['io'])
        {
          	case 'CI':
			case 'MI':

				if($opendoor){
					// 開門
					/*
					if ($rows['member_no'] != 0)
					{
						$this->member_opendoors($parms);
					}
					else
					{
						$this->temp_opendoors($parms);
					}
					*/
					
					// 取得出入口 888 資訊
					$pks_888_arr = $this->get_888_info($parms);
					if ($pks_888_arr['availables'] == 0)
					{
						if ($rows['member_no'] == 0)
						{
							// 滿車訊號 (臨停)
							$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",14,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
							// 開門訊號 (臨停)
							$this->temp_opendoors($parms);
							return false;
						}
						else
						{
							// 滿車訊號 (會員)
							$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",15,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
						}
					}

					// 空車位導引
					$pks_arr = $this->get_valid_seat();
					if ($pks_arr['result']['location_no'] != 0)
					{
						$pks_loc_name = $pks_arr['loc_name'];
						$pks_loc_no = $pks_arr['result']['location_no'];
						$pks_floors = $pks_arr['floors'];
					}
					else
					{
						$pks_loc_name = 0;
						$pks_loc_no = 0;
						$pks_floors = 0;
					}
					
					// 訊息
					if ($rows['member_no'] == 0)
					{
						$ck = md5($parms['lpr']);
						$jdata = file_get_contents("http://localhost/allpa_service.html/get_allpa_valid_user/{$parms['lpr']}/{$ck}");
						$results = json_decode($jdata, true);
						if($results['result_code'] == 0)
						{	
							// 歐pa, 開門
							$this->member_opendoors($parms);
							
							// 歐pa, 進場
							$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",3,{$parms['ivsno']},{$parms['lpr']},{$pks_floors},{$pks_loc_no}".MQ_ALTOB_MSG_END_TAG);	
							
							// old msg
							// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}，歐pa卡用戶您好：請停{$pks_loc_name}{$pks_loc_no}車位");
						}
						else
						{
							// 臨停車, 開門 (同會員)
							$this->temp_opendoors($parms);
							
							// 臨停車, 進場
							$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",11,{$parms['ivsno']},{$parms['lpr']},{$pks_floors},{$pks_loc_no}".MQ_ALTOB_MSG_END_TAG);	
							
							// old msg
							// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}請停{$pks_loc_name}{$pks_loc_no}車位");
						}
					}
					else
					{
						// 會員, 開門
						$this->member_opendoors($parms);
						
						// 會員, 進場
						$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",4,{$parms['ivsno']},{$parms['lpr']},{$pks_floors},{$pks_loc_no}".MQ_ALTOB_MSG_END_TAG);
						
						// old msg
						// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}，月租戶請進場：請停{$pks_loc_name}{$pks_loc_no}車位");
					}


				}else{
					// 資料流

					if ($parms['lpr'] != 'NONE')
					{
						$data = array
						(
							'err' => 1,
							'finished' => 1
						);
						// 原有歷史記錄, 設定錯誤碼為1(入場不應該有歷史記錄)
						$this->db->update('cario', $data, array('obj_id' => $parms['lpr'], 'finished' => 0, 'err' => 0, 'obj_type' => 1));

						$affect_rows = $this->db->affected_rows();

						if ($affect_rows > 0)
						{
							trigger_error("err://入場郤已有歷史進場記錄[{$affect_rows}]筆,已設成錯誤並結清記錄".print_r($parms, true));
						}
					}
					
					// [acer] cmd:101 入場車辨成功流程 START
					$ticket_no = $this->gen_pass_code();
					
					$data = array
					(
						'station_no' => $parms['sno'],
						'obj_type' => 1,
						'obj_id' => $parms['lpr'],
						'etag' => $parms['etag'] == 'NONE' ? '' : $parms['etag'],
						'in_out' => $parms['io'],
						'member_no' => $rows['member_no'],
						'finished' => 0,
						'in_time' => $this->now_str,
						'in_lane' => $parms['ivsno'],
						'in_pic_name' => empty($parms['pic_name']) ? '' : $parms['pic_name'],
						'out_before_time' => date("Y-m-d H:i:s"),
						'ticket_no' => $ticket_no
					);
					$this->db->insert('cario', $data);
					trigger_error("新增入場資料:".print_r($parms, true));
					
					$acer_parms = array
					(
						'cario_no' => $this->db->insert_id(),
						'in_time' => $this->now_str,
						'ticket_no' => $ticket_no,
						'lpr' => $parms['lpr'],
						'ivs_no' => $parms['ivsno']
					);
					$this->call_acer('101', $acer_parms);
					// [acer] cmd:101 入場車辨成功流程 END
					
					// 傳送進場記錄
					$sync_agent = new AltobSyncAgent();
					$sync_agent->init($parms['sno'], $this->now_str);
					$sync_agent->cario_no = $this->db->insert_id();		// 進出編號
					$sync_agent->member_no = $rows['member_no'];		// 會員編號
					$sync_result = $sync_agent->sync_st_in($parms);
					trigger_error( "..sync_st_in.." .  $sync_result);
					
				}

                break;

            // 出場
            case 'CO':
			case 'MO':
			
            	// 讀取最近一筆入場資料
        		$rows_cario = $this->db
        					->select('cario_no, payed, in_time, pay_time, out_before_time')
        					->from('cario')
                			//->where(array('in_out' => 'CI', 'obj_id' => $parms['lpr'], 'finished' => 0, 'err' => 0))
							        ->where(array('obj_id' => $parms['lpr'], 'err' => 0))
                  			->order_by('cario_no', 'desc')
                  			->limit(1)
                			->get()
                			->row_array();

                trigger_error("opendoor={$opendoor}| 出場讀到資料:{$rows['member_no']}|".time().'|'.print_r($rows_cario, true));

                if (!empty($rows_cario['cario_no']))	// 在限時內可出場
                {
					$co_time_minutes = floor((strtotime($this->now_str) - strtotime($rows_cario['in_time'])) / 60); // 停車時數 (分鐘)
					
                    // 合規定者開門放行
                    switch(true)
                    {
                    	case $rows['member_no'] != 0:
							// CO.A.1 會員車
							
							// 判斷時段租是否超時 (超過 12 小時)
							if($rows['park_time'] != 'RE' && $co_time_minutes > 720)
							{
								if($opendoor)
								{
									// 時段租超時字幕
									$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",16,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);	
									
									// [acer] cmd:102 離場車辨成功流程 START
									$this->call_acer('102', array('cario_no' => $rows_cario['cario_no'], 'ivs_no' => $parms['ivsno'], 'msg_code' => 16));
									// [acer] cmd:102 離場車辨成功流程 END
								}
								else
								{
									$data = array
									(
										'out_time' => $this->now_str,
										'out_lane' => $parms['ivsno'],
										'minutes' => $co_time_minutes,
										'out_pic_name' => $parms['pic_name']
									);
									$this->db->update('cario', $data, array('cario_no' => $rows_cario['cario_no']));	// 記錄出場
									trigger_error("{$parms['lpr']}|時段租超時" . print_r($rows_cario, true));

									// 傳送離場記錄	
									$sync_agent = new AltobSyncAgent();
									$sync_agent->init($parms['sno'], $this->now_str);
									$sync_agent->cario_no = $rows_cario['cario_no'];		// 進出編號
									$sync_agent->member_no = $rows['member_no'];			// 會員編號
									$sync_agent->in_time = $rows_cario['in_time'];			// 入場時間
									$sync_result = $sync_agent->sync_st_out($parms);
									trigger_error( "..sync_st_out.." .  $sync_result);											
								}
								return false;
							}
							
							if($opendoor)
							{
								// 會員開門
								$this->member_opendoors($parms);
								// 會員字幕
								$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",5,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
								// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}謝謝光臨");
								
								// [acer] cmd:102 離場車辨成功流程 START
								$this->call_acer('102', array('cario_no' => $rows_cario['cario_no'], 'ivs_no' => $parms['ivsno'], 'msg_code' => 5));
								// [acer] cmd:102 離場車辨成功流程 END
							}
							else
							{
								$data = array
								(
									'in_out' => $parms['io'],
									'finished' => 1,
									'out_time' => $this->now_str,
									'out_lane' => $parms['ivsno'],
									'minutes' => $co_time_minutes,
									'out_pic_name' => $parms['pic_name']
								);
								$this->db->update('cario', $data, array('cario_no' => $rows_cario['cario_no']));
								trigger_error('會員車離場:' . print_r($rows, true));
								
								// 傳送離場記錄
								$sync_agent = new AltobSyncAgent();
								$sync_agent->init($parms['sno'], $this->now_str);
								$sync_agent->cario_no = $rows_cario['cario_no'];		// 進出編號
								$sync_agent->member_no = $rows['member_no'];			// 會員編號
								$sync_agent->in_time = $rows_cario['in_time'];			// 入場時間
								$sync_agent->finished = 1;								// 已離場
								$sync_result = $sync_agent->sync_st_out($parms);
								trigger_error( "..sync_st_out.." .  $sync_result);
							}

                            break;

                        case strtotime($rows_cario['out_before_time']) >= time():

                        	if ($rows_cario['payed'])
                            {
								// CO.B.1 臨停車已付款
								if($opendoor)
								{
									// 臨停開門
									$this->temp_opendoors($parms);
									// 臨停字幕
									$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",6,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
									//// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}付款確認請 離 場謝謝光臨");
									// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}謝謝光臨");
									
									// [acer] cmd:102 離場車辨成功流程 START
									$this->call_acer('102', array('cario_no' => $rows_cario['cario_no'], 'ivs_no' => $parms['ivsno'], 'msg_code' => 6));
									// [acer] cmd:102 離場車辨成功流程 END
								}
								else
								{
									$data = array
									(
										'in_out' => $parms['io'],
										'finished' => 1,
										'out_time' => $this->now_str,
										'out_lane' => $parms['ivsno'],
										'minutes' => $co_time_minutes,
										'out_pic_name' => $parms['pic_name']
									);
									$this->db->update('cario', $data, array('cario_no' => $rows_cario['cario_no']));
									trigger_error('臨停車已付款:' . print_r($rows, true));
									
									// 傳送離場記錄
									$sync_agent = new AltobSyncAgent();
									$sync_agent->init($parms['sno'], $this->now_str);
									$sync_agent->cario_no = $rows_cario['cario_no'];		// 進出編號
									$sync_agent->in_time = $rows_cario['in_time'];			// 入場時間
									$sync_agent->finished = 1;								// 已離場
									$sync_result = $sync_agent->sync_st_out($parms);
									trigger_error( "..sync_st_out.." .  $sync_result);
								}
                            }
							else
                            {
								// CO.B.2 臨停車未付款
								if($opendoor)
								{
									// 臨停字幕
									$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",8,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
									// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}臨時車請投票卡或刷卡出場");
									
									// [acer] cmd:102 離場車辨成功流程 START
									$this->call_acer('102', array('cario_no' => $rows_cario['cario_no'], 'ivs_no' => $parms['ivsno'], 'msg_code' => 8));
									// [acer] cmd:102 離場車辨成功流程 END
								}
								else
								{
									// TODO: 歐pa卡離場記錄和臨停未付款一樣, 待更正
									$data = array
									(
										'out_time' => $this->now_str,
										'out_lane' => $parms['ivsno'],
										'minutes' => $co_time_minutes,
										'out_pic_name' => $parms['pic_name']
									);
									$this->db->update('cario', $data, array('cario_no' => $rows_cario['cario_no']));	// 記錄出場
									trigger_error('臨停未付款:' . print_r($rows, true));
									
									// 傳送離場記錄
									$sync_agent = new AltobSyncAgent();
									$sync_agent->init($parms['sno'], $this->now_str);
									$sync_agent->cario_no = $rows_cario['cario_no'];		// 進出編號
									$sync_agent->in_time = $rows_cario['in_time'];			// 入場時間
									$sync_result = $sync_agent->sync_st_out($parms);
									trigger_error( "..sync_st_out.." .  $sync_result);
								}
                            }
                            break;

                        default:
							// CO.C.1 其它付款方式
							if($opendoor)
							{
								$in_time = strtotime($rows_cario['out_before_time']);
								$ck = md5($in_time. $parms['lpr'] . $parms['sno']);
								$jdata = file_get_contents("http://localhost/allpa_service.html/allpa_go/{$in_time}/{$parms['lpr']}/{$parms['sno']}/{$ck}");
								trigger_error("allpa回傳:{$jdata}|{$in_time}/{$parms['lpr']}/{$parms['sno']}/{$ck}");
								$results = json_decode($jdata, true);
								trigger_error("+++".print_r($results, true));
								if ($results['result_code'] == 0)	// 歐pa卡, 點數足夠扣
								{
									// 臨停開門
									$this->temp_opendoors($parms);
									// 歐pa卡, 字幕
									$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",7,{$parms['ivsno']},{$parms['lpr']},{$results['amt']}".MQ_ALTOB_MSG_END_TAG);
									// // $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}扣{$results['amt']}點請離場");
									// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}請離場歐pa卡扣:{$results['amt']}點謝謝光臨");
									// updated 2016/09/01
									$data = array(
											'out_before_time' =>  date('Y-m-d H:i:s', strtotime(" + 15 minutes")),
											'pay_time' => $this->now_str,
											'pay_type' => 9, // 歐pa卡
											'payed' => 1
										);
									$this->db->update('cario', $data, array('cario_no' => $rows_cario['cario_no']));	// 記錄出場
									
									// [acer] cmd:102 離場車辨成功流程 START
									$this->call_acer('102', array('cario_no' => $rows_cario['cario_no'], 'ivs_no' => $parms['ivsno'], 'msg_code' => 7));
									// [acer] cmd:102 離場車辨成功流程 END
								}
								else if ($results['result_code'] == 12)	// 歐pa卡, 餘額不足
								{
									// 臨停字幕
									$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",12,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
									
									// [acer] cmd:102 離場車辨成功流程 START
									$this->call_acer('102', array('cario_no' => $rows_cario['cario_no'], 'ivs_no' => $parms['ivsno'], 'msg_code' => 12));
									// [acer] cmd:102 離場車辨成功流程 END
								}
								else if ($results['result_code'] == 11)	// 歐pa卡, 查無會員
								{
									// 臨停字幕
									$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",9,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
									//// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}其它付款方式");
									// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}臨時車請投票卡或刷卡出場");
									
									// [acer] cmd:102 離場車辨成功流程 START
									$this->call_acer('102', array('cario_no' => $rows_cario['cario_no'], 'ivs_no' => $parms['ivsno'], 'msg_code' => 9));
									// [acer] cmd:102 離場車辨成功流程 END
								}
								else
								{
									// 臨停字幕
									$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",9,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
									
									// [acer] cmd:102 離場車辨成功流程 START
									$this->call_acer('102', array('cario_no' => $rows_cario['cario_no'], 'ivs_no' => $parms['ivsno'], 'msg_code' => 9));
									// [acer] cmd:102 離場車辨成功流程 END
								}
							}
							else
							{
								$data = array
								(
									'out_time' => $this->now_str,
									'out_lane' => $parms['ivsno'],
									'minutes' => $co_time_minutes,
									'out_pic_name' => $parms['pic_name']
								);
								$this->db->update('cario', $data, array('cario_no' => $rows_cario['cario_no']));	// 記錄出場
								trigger_error("{$parms['lpr']}|其它付款方式:" . print_r($rows_cario, true));
								
								// 傳送離場記錄
								$sync_agent = new AltobSyncAgent();
								$sync_agent->init($parms['sno'], $this->now_str);
								$sync_agent->cario_no = $rows_cario['cario_no'];		// 進出編號
								$sync_agent->in_time = $rows_cario['in_time'];			// 入場時間
								$sync_result = $sync_agent->sync_st_out($parms);
								trigger_error( "..sync_st_out.." .  $sync_result);
							}
                            break;
                    }

                }
                else if ($rows['member_no'] != 0)
				{
					// CO.Z.1 月租車無入場資料
					if($opendoor)
					{
						// 會員開門
						$this->member_opendoors($parms);
						// 會員字幕
						$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",10,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
						// $this->mq_send(MQ_TOPIC_SUBTEXT, "{$parms['ivsno']},{$msg}月租戶請離場謝謝光臨");
						
						// [acer] cmd:102 離場車辨成功流程 START
						$this->call_acer('102', array('cario_no' => $rows_cario['cario_no'], 'ivs_no' => $parms['ivsno'], 'msg_code' => 10));
						// [acer] cmd:102 離場車辨成功流程 END
					}
					else
					{
						trigger_error('月租車無入場資料:' . print_r($rows, true));
						
						// 傳送離場記錄
						$sync_agent = new AltobSyncAgent();
						$sync_agent->init($parms['sno'], $this->now_str);
						$sync_agent->member_no = $rows['member_no'];			// 會員編號
						$sync_agent->finished = 1;								// 已離場
						$sync_result = $sync_agent->sync_st_out($parms);
						trigger_error( "..sync_st_out.." .  $sync_result);
					}
				}
				else
				{
					// CO.Z.Z 無入場資料
					if($opendoor)
					{
						$this->mq_send(MQ_TOPIC_ALTOB, MQ_ALTOB_MSG.",13,{$parms['ivsno']},{$parms['lpr']}".MQ_ALTOB_MSG_END_TAG);
						
						// [acer] cmd:102 離場車辨成功流程 START
						$this->call_acer('102', array('cario_no' => 0, 'ivs_no' => $parms['ivsno'], 'msg_code' => 13));
						// [acer] cmd:102 離場車辨成功流程 END
					}
					else
					{
						trigger_error('無入場資料:' . print_r($rows, true));
						
						// 傳送離場記錄
						$sync_agent = new AltobSyncAgent();
						$sync_agent->init($parms['sno'], $this->now_str);
						$sync_result = $sync_agent->sync_st_out($parms);
						trigger_error( "..sync_st_out.." .  $sync_result);
					}
				}
            	break;
        }

    }


    // 檢查是否合法會員或VIP資料
	public function get_member($lpr)
	{
    	$where_arr = array
        (
         	'c.lpr' => $lpr,
         	'c.start_time <=' => $this->now_str,
         	'c.end_time >=' => $this->now_str
        );

    	$sql = "select
        		c.lpr_correct,
                c.member_no,
                m.member_name,
                m.member_type,
                m.locked,
                m.remarks,
				m.park_time,
				m.suspended,
				m.valid_time,
                c.etag,
                c.start_time,
                c.end_time
                from member_car c, members m
                where c.member_no = m.member_no
                and c.start_time <= '{$this->now_str}'
                and c.end_time >= '{$this->now_str}'
                and c.lpr = '{$lpr}'
                limit 1";

        $rows = $this->db->query($sql)->row_array();
		
		// 新增 park_time_check 2016/11/11                                           
        $park_time_check = 0;
        if (!empty($rows['lpr_correct']))
        {
        	$park_time = $rows['park_time'];
        	$pt_arr = $this->vars['mcache']->get('pt'); 
			
			if(empty($pt_arr) || empty($park_time))
			{
				// ERROR: 無法驗証時段, 跳過時段限制判斷
				trigger_error("[ERROR] mcache.pt is empty !!");
				$park_time_check = 1;
			}
			else
			{
				$now_time = substr($this->now_str, 11);				// 日期字串只取最後時間字串(13:25:32)
				$week_no = date('w',strtotime($this->now_str));		// 取星期幾 
				$park_time_array = explode(',', $park_time);		// 用 , 格開
				foreach($park_time_array as $idx => $park_time_value)
				{
					foreach($pt_arr[$park_time_value]['timex'] as $idx => $pt_rows)
					{
						if ($week_no >= $pt_rows['w_start'] && 
							$week_no <= $pt_rows['w_end'] && 
							$now_time >= $pt_rows['time_start'] && 
							$now_time <= $pt_rows['time_end'])
							{
								$park_time_check = 2;
								
								trigger_error("時段代碼:{$park_time_value} 星期:{$week_no}");
								break;
							}
					}
				}
			}
        }
		
        if (empty($rows['lpr_correct'])) 		// A. 非月租車
        {
          	$rows = array
            (
            	'lpr_correct' => '',
            	'member_no' => 0,
            	'member_name' => '',
            	'member_type' => 9,
            	'etag' => '',
            	'start_time' => '',
            	'end_time' => '',
            );
        }
		else if(empty($park_time_check))		// B. 月租車, 時段無效
		{
			trigger_error("無效的時段!! " . print_r($rows, true));
			
			$rows = array
            (
            	'lpr_correct' => '',
            	'member_no' => 0,
            	'member_name' => '',
            	'member_type' => 9,
            	'etag' => '',
            	'start_time' => '',
            	'end_time' => '',
            );
		}
		else if(!empty($rows['suspended']))		// C. 月租車, 停權中
		{
			trigger_error("停權中!! " . print_r($rows, true));
			
			$rows = array
            (
            	'lpr_correct' => '',
            	'member_no' => 0,
            	'member_name' => '',
            	'member_type' => 9,
            	'etag' => '',
            	'start_time' => '',
            	'end_time' => '',
            );
		}
		else if(!empty($rows['valid_time']) && $rows['valid_time'] < $this->now_str)		// D. 月租車, 已無效 (審核未通過)
		{
			trigger_error("已無效!! " . print_r($rows, true));
			
			$rows = array
            (
            	'lpr_correct' => '',
            	'member_no' => 0,
            	'member_name' => '',
            	'member_type' => 9,
            	'etag' => '',
            	'start_time' => '',
            	'end_time' => '',
            );
		}
		
        trigger_error('讀取會員:' . print_r($rows, true) . ", park_time_check: {$park_time_check}");
        return $rows;
    }

	// 開門 (月租)
    public function member_opendoors($parms)
	{
		$this->mq_send(MQ_TOPIC_OPEN_DOOR, "DO{$parms['ivsno']},OPEN,{$parms['lpr']}");
        return true;
    }

	// 開門 (臨停)
	public function temp_opendoors($parms)
	{
		$this->mq_send(MQ_TOPIC_OPEN_DOOR, "DO{$parms['ivsno']},TICKET,{$parms['lpr']}");
		return true;
	}


    // 用eTag讀出車號
	public function etag2lpr_2($etag)
	{
        // 用讀取eTag記錄(有double驗證過)
        $rows = $this->db->select('lpr')
        			->from('etag_lpr')
                    ->where(array('etag' => $etag, 'confirms >' => 0))
                    ->limit(1)
                    ->get()
                    ->row_array();

        // 讀出eTag資料
        if (!empty($rows['lpr']))
        {
        	trigger_error("+++車牌NONE,以eTag讀入車牌:{$etag}|{$rows['lpr']}");
          	return $rows['lpr'];
        }

        return 'NONE';
    }


	// 有車牌與eTag, 檢查資料庫 (2017/03/22 new)
	public function check_lpr_etag($lpr, $etag)
	{
		$ETAG_LOG_TITLE = 'etag://';
		$ETAG_WARMIN_TITLE = 'etag-warning://';
		trigger_error($ETAG_LOG_TITLE. "輸入: {$lpr},{$etag}");
		
		// 手動值上下限
		$max_admin_confirms_value = 99;
		$min_admin_confirms_value = 50;
		// 自動值上下限
		$max_system_confirms_value = 33;
		$min_system_confirms_value = 0;
		// 判斷對應加權
		$etag_confirms_bias_plus = 11;		// etag 找 車牌, 對上一次可扺 11次
		$etag_confirms_bias_minus = -1;
		$lpr_confirms_bias_plus = 3;		// 車牌 找 etag, 對上一次可扺 3次
		$lpr_confirms_bias_minus = -1;
		
		// eTag 找 車牌
		$lpr_info_from_etag = $this->db->select('lpr, confirms')
					->from('etag_lpr')
					->where(array('etag' => $etag))
					->limit(1)
					->get()
					->row_array();
						
		if (!empty($lpr_info_from_etag['lpr']))
		{
			// B. etag 有找到 車牌
			
			if ($lpr_info_from_etag['lpr'] == $lpr)
			{
				// B.1. etag 有找到 車牌, 且 車牌 相符, confirms 上升
				$confirms_bias = $etag_confirms_bias_plus;
			}
			else
			{
				// B.2. etag 有找到 車牌, 但 車牌 不符, confirms 下降
				$confirms_bias = $etag_confirms_bias_minus;
				trigger_error($ETAG_WARMIN_TITLE . "etag 找 lpr | lpr error : {$lpr},{$etag} | query:" . print_r($lpr_info_from_etag, true));
			}
			
			$next_confirms_value = $lpr_info_from_etag['confirms'] + $confirms_bias;
			trigger_error($ETAG_LOG_TITLE . "etag 找 lpr | {$lpr},{$etag}, next_confirms: {$next_confirms_value}, bias:{$confirms_bias}");
			
			// 更新 confirms 資訊
			if($next_confirms_value > $max_admin_confirms_value)
			{
				// B.3.0 confirms 超過 max_admin_confirms_value, skip
				//trigger_error($ETAG_LOG_TITLE . "etag 找 lpr | {$lpr},{$etag} next_confirms_value > max_admin_confirms_value : {$max_admin_confirms_value}");
			}
			else if ($next_confirms_value >= $min_admin_confirms_value)
			{
				// B.3.1 人工確認過的記錄, 誤判多次後會停留在 min_admin_confirms_value, 或加到 max_admin_confirms_value
				$this->db->where('etag', $etag)->update('etag_lpr', array('confirms' => $next_confirms_value));
			}
			else if ($next_confirms_value > $max_system_confirms_value)
			{
				// B.3.2 confirms 超過 max_system_confirms_value, skip
				//trigger_error($ETAG_LOG_TITLE . "etag 找 lpr | {$lpr},{$etag} next_confirms_value > max_system_confirms_value : {$max_system_confirms_value}");
			}
			else if ($next_confirms_value <= $max_system_confirms_value && $next_confirms_value >= $min_system_confirms_value)
			{
				// B.3.3 confirms 不到 max_system_confirms_value 為系統生成記錄, 誤判多次後 confirms 會扣到 min_system_confirms_value
				$this->db->where('etag', $etag)->update('etag_lpr', array('confirms' => $next_confirms_value));
			}
			else
			{
				// B.3.4 若低於 min_system_confirms_value，刪除
				$this->db->delete('etag_lpr', array('etag' => $etag));
				trigger_error($ETAG_LOG_TITLE . "etag 找 lpr | etag confirms fail and removed : {$lpr_info_from_etag['lpr']}, {$etag}");
				trigger_error($ETAG_WARMIN_TITLE . "etag 找 lpr | etag confirms fail and removed : {$lpr_info_from_etag['lpr']}, {$etag}");
			}
		}
		else
		{
			// 車牌 找 etag
			$etag_info_form_lpr = $this->db->select('etag, confirms, member_no')
							->from('etag_lpr')
							->where(array('lpr' => $lpr))
							->limit(1)
							->get()
							->row_array();
			
			if (!empty($etag_info_form_lpr['etag']))
			{	
				// A. 車牌 有找到 etag
					
				if ($etag_info_form_lpr['etag'] == $etag)
				{
					// A.1. 車牌 有找到 etag, 且 etag 相符, confirms 上升
					$confirms_bias = $lpr_confirms_bias_plus;
						
					// 若尚未登記為會員
					if(empty($etag_info_form_lpr['member_no']))
					{
						// 檢查是否會員
						$member_info_from_lpr = $this->db->select('member_no, member_name')
							->from('members')
							->where(array('lpr' => $lpr))
							->limit(1)
							->get()
							->row_array();
							
						// 確認為會員, 建立 eTag 資訊
						if (!empty($member_info_from_lpr['member_no']))
						{
							$data['member_no'] = $member_info_from_lpr['member_no'];
							$data['member_name'] = $member_info_from_lpr['member_name'];
							$this->db->where('member_no', $member_info_from_lpr['member_no'])->update('member_car', array('etag' => $etag));
							$this->db->where('member_no', $member_info_from_lpr['member_no'])->update('members', array('etag' => $etag));
								
							// 更新 etag_lpr
							$this->db->where('etag', $etag)->update('etag_lpr', $data);
						}
					}
				}
				else
				{
					// A.2. 車牌 有找到 etag, 但 etag 不符, confirms 下降
					$confirms_bias = $lpr_confirms_bias_minus;
					trigger_error($ETAG_WARMIN_TITLE . "lpr 找 etag | etag error : {$lpr},{$etag} | query:" . print_r($etag_info_form_lpr, true));
				}
				
				$next_confirms_value = $lpr_info_from_etag['confirms'] + $confirms_bias;
				trigger_error($ETAG_LOG_TITLE . "lpr 找 etag | {$lpr},{$etag}, next_confirms: {$next_confirms_value}, bias:{$confirms_bias}");
				
				// 更新 confirms 資訊
				if($next_confirms_value > $max_admin_confirms_value)
				{
					// A.3.0 confirms 超過 max_admin_confirms_value, skip
					//trigger_error($ETAG_LOG_TITLE . "lpr 找 etag | {$lpr},{$etag} next_confirms_value > max_admin_confirms_value : {$max_admin_confirms_value}");
				}
				else if ($next_confirms_value >= $min_admin_confirms_value)
				{
					// A.3.1 人工確認過的記錄, 誤判多次後會停留在 min_admin_confirms_value, 或加到 max_admin_confirms_value
					$this->db->where('lpr', $lpr)->update('etag_lpr', array('confirms' => $next_confirms_value));
				}
				else if ($next_confirms_value > $max_system_confirms_value)
				{
					// A.3.2 confirms 超過 max_system_confirms_value, skip
					//trigger_error($ETAG_LOG_TITLE . "lpr 找 etag | {$lpr},{$etag} next_confirms_value > max_system_confirms_value : {$max_system_confirms_value}");
				}
				else if ($next_confirms_value <= $max_system_confirms_value && $next_confirms_value >= $min_system_confirms_value)
				{
					// A.3.3 confirms 不到 max_system_confirms_value 為系統生成記錄, 誤判多次後 confirms 會扣到 min_system_confirms_value
					$this->db->where('lpr', $lpr)->update('etag_lpr', array('confirms' => $next_confirms_value));
				}
				else
				{
					// A.3.4 若低於 min_system_confirms_value，刪除
					$this->db->delete('etag_lpr', array('lpr' => $lpr));
					trigger_error($ETAG_LOG_TITLE . "lpr 找 etag | lpr confirms fail and removed : {$lpr}, {$etag_info_form_lpr['etag']}");
					trigger_error($ETAG_WARMIN_TITLE . "lpr 找 etag | lpr confirms fail and removed : {$lpr}, {$etag_info_form_lpr['etag']}");
				}
			}
			else
			{
				// C. 車牌 與 etag 都找不到記錄
				$data = array
				(
					'lpr' => $lpr,
					'lpr_correct' => $lpr,
					'etag' => $etag
				);

				// 檢查是否會員
				$member_info_from_lpr = $this->db->select('member_no, member_name')
							->from('members')
							->where(array('lpr' => $lpr))
							->limit(1)
							->get()
							->row_array();

				// 會員者, 將eTag update回去
				if (!empty($member_info_from_lpr['member_no']))
				{
					$data['member_no'] = $member_info_from_lpr['member_no'];
					$data['member_name'] = $member_info_from_lpr['member_name'];

					$this->db->where('member_no', $member_info_from_lpr['member_no'])->update('member_car', array('etag' => $etag));
					$this->db->where('member_no', $member_info_from_lpr['member_no'])->update('members', array('etag' => $etag));
				}
					
				// 建立第一筆記錄
				$this->db->insert('etag_lpr', $data);
				$etag_lpr_seqno = $this->db->insert_id();
				
				trigger_error($ETAG_LOG_TITLE . "create | insert seqno = {$etag_lpr_seqno}". print_r($data, true));
			}	
		}
	}
	
	/*
    // 有車牌與eTag, 檢查資料庫
	public function check_lpr_etag($lpr, $etag)
	{
        // 用讀取eTag記錄(有double驗證過)
        $rows = $this->db->select('etag, confirms')
        			->from('etag_lpr')
                    ->where(array('lpr' => $lpr))
                    ->limit(1)
                    ->get()
                    ->row_array();

        // 讀出eTag資料
        if (!empty($rows['etag']))
        {
        	// 車牌與eTag皆相符, 檢查是否confirms欄位若為0, 設成1(double驗證)
        	if ($rows['etag'] == $etag)
            {
            	if ($rows['confirms'] == 0) $this->db->where('lpr', $lpr)->update('etag_lpr', array('confirms' => 1));
            }
            else	// eTag不相符
            {
            	if ($rows['confirms'] == 1)
                {
                	$this->db->where('lpr', $lpr)->update('etag_lpr', array('confirms' => 0));
                }
                else 	// 原confirms為0者, 刪除之
                {
                	$this->db->delete('etag_lpr', array('lpr' => $lpr));
                }
            }
        }
        else	// 無資料, 新增一筆
        {
            // 再檢查一次是否有eTag ?
        	$rows_etag = $this->db->select('lpr, confirms')
        				->from('etag_lpr')
                    	->where(array('etag' => $etag))
                    	->limit(1)
                    	->get()
                    	->row_array();
            if (empty($rows_etag['lpr']))	// 無資料
            {
        		$data = array
            	(
            		'lpr' => $lpr,
                	'lpr_correct' => $lpr,
                	'etag' => $etag
            	);

                // 檢查是否會員
            	$rows_members = $this->db->select('member_no, member_name')
        				->from('members')
                    	->where(array('lpr' => $lpr))
                    	->limit(1)
                    	->get()
                    	->row_array();

                // 會員者, 將eTag update回去
                if (!empty($rows_members['member_no']))
                {
                	$data['member_no'] = $rows_members['member_no'];
                	$data['member_name'] = $rows_members['member_name'];

                	$this->db->where('member_no', $rows_members['member_no'])->update('member_car', array('etag' => $etag));
                	$this->db->where('member_no', $rows_members['member_no'])->update('members', array('etag' => $etag));
                }

            	$this->db->insert('etag_lpr', $data);
            }
            else
            {
            	if ($rows_etag['confirms'] == 1)
                {
                	$this->db->where('etag', $etag)->update('etag_lpr', array('confirms' => 0));
                }
                else 	// 原confirms為0者, 刪除之
                {
                	$this->db->delete('etag_lpr', array('etag' => $etag));
                }
            }
        }
    }
	*/


    // 送出至message queue(目前用mqtt)
	public function mq_send($topic, $msg)
	{
		$this->vars['mqtt']->publish($topic, $msg, 0);
    	trigger_error("mqtt:{$topic}|{$msg}");
    }


    // 指派車位
    // http://203.75.167.89/parkingquery.html/get_valid_seat
    // 註記現在時間, 並保留10分鐘
	public function get_valid_seat()
	{
    	$data = array();
		//$data['result']['location_no'] = '0';
        //$data['result_code'] = 'FAIL';
		//return $data;
		
		// 撈 roger db
		/*
		$sql = "
			SELECT ParkingNum AS pksno FROM table_carpark 
				WHERE LPR = '' AND DisableSeat = 0 AND 
					(SELECT COUNT(*) FROM table_carpark WHERE LPR = '' AND DisableSeat = 0) <= 10
			ORDER BY RAND() LIMIT 1 FOR UPDATE;
			";
		$dsn_old_db = $this->load->database('old_db', true);
		
		// 2016/12/14 roger_db 掛了的可能
		if ($dsn_old_db->initialize())
		{
		   $retults = $dsn_old_db->query($sql)->result_array();
		}
			
		if(!empty($retults[0]))
		{
			$data['result']['location_no'] = substr($retults[0]['pksno'], 1);
			$data['result_code'] = 'OK';
			$data['loc_name'] = 'B'.substr($retults[0]['pksno'], 0, 1);
			$data['floors'] = 'B'.substr($retults[0]['pksno'], 0, 1);
		}
		else
		{
			$data['result']['location_no'] = '0';
        	$data['result_code'] = 'FAIL';
		}
		*/
		
        $this->db->trans_start();
        $sql = "select pksno from pks where status = 'VA' and prioritys != 0 and (book_time is null or book_time <= now()) order by prioritys asc limit 1 for update;";
        $rows = $this->db->query($sql)->row_array();
        if (!empty($rows['pksno']))
        {
        	$data['result']['location_no'] = substr($rows['pksno'], -3);
        	$data['result_code'] = 'OK';
            $sql = "update pks set book_time = addtime(now(), '00:10:00') where pksno = {$rows['pksno']};";
            $this->db->query($sql);
            $sql = "select g.group_name, g.floors from pks_groups g, pks_group_member m where m.pksno = {$rows['pksno']} and g.group_id = m.group_id and g.group_type = 1 limit 1";
            $rows = $this->db->query($sql)->row_array();
            $data['loc_name'] = $rows['group_name'];
			$data['floors'] = $rows['floors'];
        }
        else
        {
        	$data['result']['location_no'] = '0';
        	$data['result_code'] = 'FAIL';
        }
        $this->db->trans_complete();
		
        return $data;
    }
	
	// 取得出入口 888 資訊
	public function get_888_info($parms)
	{
    	$data = array();
        $sql = "select availables as availables, tot as tot from pks_groups where group_id = 'C888' and station_no = {$parms['sno']}";
        $rows = $this->db->query($sql)->row_array();
        if (!empty($rows) && array_key_exists('availables', $rows))
        {
        	$data['result_code'] = 'OK';
			$data['availables'] = $rows['availables'];
			$data['tot'] = $rows['tot'];
        }
        else
        {
			trigger_error(__FUNCTION__ . "..not found..".print_r($parms, true));
        	$data['result_code'] = 'FAIL';
			$data['availables'] = 9999;			// 如果拿不到就忽略這個流程
			$data['tot'] = 0;
        }
        return $data;
    }
	
	
	
	// ===============================================
	// acer cmd
	// ===============================================
	
	// 產生通行碼
	function gen_pass_code()
	{
		return rand(100000,999999);
	}
	
	// 呼叫acer
	function call_acer($cmd, $parms)
	{
		return false; // 尚未啟用
		
		try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost/acer_service.html/cmd_'. $cmd);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,3);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parms));
            $data = curl_exec($ch);
			
			if(curl_errno($ch))
			{
				trigger_error(__FUNCTION__ . ', curl error: '. curl_error($ch));
			}
			
            curl_close($ch);
			
			trigger_error(__FUNCTION__ . '..'. $data);

		}catch (Exception $e){
			trigger_error(__FUNCTION__ . 'error:'.$e->getMessage());
		}
	}
	
	
}
