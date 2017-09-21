<?php
/*
file: carpark.php		停車管理
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(MQ_CLASS_FILE); 

// ----- 定義常數(路徑, cache秒數) -----       
define('APP_VERSION', '100');		// 版本號                                        
define('MAX_AGE', 604800);			// cache秒數, 此定義1個月     
define('APP_NAME', 'carpark');		// 應用系統名稱
define('PAGE_PATH', APP_BASE.'ci_application/views/'.APP_NAME.'/');						// path of views        
define('SERVER_URL', 'http://'.(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost').'/');	// URL
define('APP_URL', SERVER_URL.APP_NAME.'.html/');										// controller路徑 
define('WEB_URL', SERVER_URL.APP_NAME.'/');												// 網頁路徑
define('WEB_LIB', SERVER_URL.'libs/');													// 網頁lib
define('BOOTSTRAPS', WEB_LIB.'bootstrap_sb/');											// bootstrap lib  
define('LOG_PATH', FILE_BASE.APP_NAME.'/logs/');	// log path

class Carpark extends CI_Controller
{                 
    var $vars = array();	// 共用變數   
    
	function __construct() 
	{        
		parent::__construct();     
        // ----- 程式開發階段log設定 -----
        if (@ENVIRONMENT == 'development')
        {                        
          	ini_set('display_errors', '1');
			//error_reporting(E_ALL ^ E_NOTICE); 
			error_reporting(E_ALL); 
        }  
        set_error_handler(array($this, 'error_handler'), E_ALL);	// 資料庫異動需做log   
		
		// 共用記憶體 
        $this->vars['mcache'] = new Memcache;
		$this->vars['mcache']->connect(MEMCACHE_HOST, MEMCACHE_PORT) or die ('Could not connect memcache'); 
		
		// mqtt subscribe
		$this->vars['mqtt'] = new phpMQTT(MQ_HOST, MQ_PORT, uniqid());  
		//if(!$this->vars['mqtt']->connect()){ die ('Could not connect mqtt');  }				
		$this->vars['mqtt']->connect();
		
		$this->load->model('carpark_model'); 
        $this->carpark_model->init($this->vars);
		
		// 微調剩餘車位數
		$this->load->model('sync_data_model'); 
		$this->sync_data_model->init($this->vars);
		
		// 產生 excel 報表
		$this->load->model('excel_model'); 
        $this->excel_model->init($this->vars);
		
		// [START] 2016/06/03 登入
		$this->load->model('user_model'); 
		// load library
		$this->load->library(array('form_validation','session'));
		// load helpers
		$this->load->helper(array('form'));  
		// ajax code
		define('RESULT_SUCCESS', 'ok');
		define('RESULT_FORM_VALIDATION_FAIL', '-1');
		define('RESULE_FAIL', 'gg');
		// [START] 2016/06/03 登入
	}
	
	
	// 發生錯誤時集中在此處理
	public function error_handler($errno, $errstr, $errfile, $errline, $errcontext)
	{                                      
    	// ex: car_err://message....
    	$log_msg = explode('://', $errstr);
        if (count($log_msg) > 1)
        {
            $log_file = LOG_PATH.$log_msg[0];    
        	$str = date('H:i:s')."|{$log_msg[1]}|{$errfile}|{$errline}|{$errno}\n"; 
        } 
        else
        {   
        	$log_file = LOG_PATH.APP_NAME;
    		$str = date('H:i:s')."|{$errstr}|{$errfile}|{$errline}|{$errno}\n";
        }
        
    	//$str = date('H:i:s')."|{$errstr}|{$errfile}|{$errline}|{$errno}\n";               
    	error_log($str, 3, $log_file . '.' . date('Ymd').'.log.txt');	// 3代表參考後面的檔名
    }
    
    
	// 顯示靜態網頁(html檔)
	protected function show_page($page_name, &$data = null)
	{           
    	$page_file = PAGE_PATH.$page_name.'.php';
        $last_modified_time = filemtime($page_file);         
            
    	// 若檔案修改時間沒有異動, 或版本無異動, 通知瀏覽器使用cache, 不再下傳網頁
		// header('Cache-Control:max-age='.MAX_AGE);	// cache 1個月
    	header('Last-Modified: '.gmdate('D, d M Y H:i:s', $last_modified_time).' GMT');
        header('Etag: '. APP_VERSION);
		header('Cache-Control: public'); 
		
		// 20170921
		header("cache-Control: no-store, no-cache, must-revalidate");
		header("cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");		
        
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == APP_VERSION && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time)
    	{                  
        	header('HTTP/1.1 304 Not Modified');
    	}
        else
        {                                           
        	$this->load->view(APP_NAME.'/'.$page_name, $data);
        }    
	} 
	
	
	
	
	
	
	// ------------------------------------------------
	//
	// 接收端 (START)
	//
	// ------------------------------------------------
	
	// [設定檔] 取得設定
	public function station_setting_query()
	{
		$reload = $this->input->post('reload', true);
		
		if(isset($reload) && $reload > 0)
		{
			$station_setting = $this->sync_data_model->station_setting_query(true);	// 強制重新載入	
			trigger_error(__FUNCTION__ . '..station_setting: '. print_r($station_setting, true));
			
			if(!$station_setting)
			{
				echo json_encode('fail', JSON_UNESCAPED_UNICODE);
				exit;	// 中斷
			}
			
			$info = array('station_no_arr' => $station_setting['station_no']);
			
			usleep(300000); // 0.3 sec delay
			
			// 費率資料同步
			$result = $this->sync_data_model->sync_price_plan($info);
			trigger_error(__FUNCTION__ . '..sync_price_plan: '. $result);
			
			usleep(300000); // 0.3 sec delay
			
			// 會員資料同步
			$result = $this->sync_data_model->sync_members($info);
			trigger_error(__FUNCTION__ . '..sync_members: '. $result);
			
			usleep(300000); // 0.3 sec delay
			
			// 在席資料同步
			$result = $this->sync_data_model->sync_pks_groups_reload($station_setting);
			trigger_error(__FUNCTION__ . '..sync_pks_groups_reload: '. $result);
		}
		else
		{
			$station_setting = $this->sync_data_model->station_setting_query(false);
			
			if(!$station_setting)
			{
				echo json_encode('fail', JSON_UNESCAPED_UNICODE);
				exit;	// 中斷
			}
		}
		echo json_encode($station_setting, JSON_UNESCAPED_UNICODE);
	}
	
	// [排程 or 強制] 同步場站資訊
	public function sync_station_data()
	{
		trigger_error(__FUNCTION__ . '..from..'. $this->my_ip() .'..network..');	// IP 會浮動？
		
		$switch_lpr_arr = array();	// 換車牌
		
		$meta = $this->input->post('meta', true);
		if(!empty($meta))
		{
			trigger_error( __FUNCTION__ . '..meta_arr..' . $meta);
			
			$meta_arr = explode('@@@', $meta);
			
			foreach($meta_arr as $raw)
			{
				if(empty($raw))
					continue;
				
				$data = json_decode($raw, true);
				
				if($data['key'] == 'switch_lpr')
				{
					array_push($switch_lpr_arr, $data['value']);
				}
			}
		}
		
		// 0. 取得場站設定
		$station_setting = $this->sync_data_model->station_setting_query(false);
		trigger_error(__FUNCTION__ . '..station_setting: '. print_r($station_setting, true));
		
		$station_no_arr = array('station_no_arr' => $station_setting['station_no']);
		
		// 1. 月租系統
		$result = $this->sync_data_model->sync_members($station_no_arr);
		trigger_error(__FUNCTION__ . '..sync_members: '. $result);
		
		// 2. 同步車牌更換
		if(!empty($switch_lpr_arr))
		{
			$this->sync_data_model->sync_switch_lpr($switch_lpr_arr);
		}
		
	}
	
	// [API] 取得最新未結清
	public function get_last_unbalanced_cario()
	{
		trigger_error(__FUNCTION__ . '..from..'. $this->my_ip() .'..network..');	// IP 會浮動？
		
		$lpr = $this->input->post('lpr', true);
		$station_no = $this->input->post('station_no', true);
		$c_s = $this->input->post('c_s', true);
		
		// 確認正確性
		if(empty($c_s) || $c_s != md5('altob'.$lpr.'botla'.$station_no))
		{
			trigger_error(__FUNCTION__ . "..{$lpr}|".$station_no."|{$c_s}..check fail..");
			echo 'fail';
			exit;
		}
		
        $data = $this->carpark_model->get_last_unbalanced_cario($lpr, $station_no);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	
	// [API] 更新未結清 (行動支付)
	public function sync_m2payed()
	{
		trigger_error(__FUNCTION__ . '..from..'. $this->my_ip() .'..network..');	// IP 會浮動？
		
		$lpr = $this->input->post('lpr', true);
		$amt = $this->input->post('amt', true);
		$station_no = $this->input->post('station_no', true);
		$cario_no = $this->input->post('cario_no', true);
		$c_s = $this->input->post('c_s', true);
		
		// 確認正確性
		if(empty($c_s) || $c_s != md5($lpr.$amt.'altob'.$station_no.'botla'.$cario_no))
		{
			trigger_error(__FUNCTION__ . "..{$lpr}|{$amt}|{$station_no}|{$cario_no}|{$c_s}..check fail..");
			echo 'fail';
			exit;
		}
		
		// 臨停繳費
		$this->load->model('carpayment_model'); 
        echo $this->carpayment_model->p2payed(array('seqno' => $cario_no, 'lpr' => $lpr, 'amt' => $amt), true);
		exit;
		//echo 'ok';
	}
	
	// 驗証 IP
	function is_ip_valid()
	{
		$client_ip = $this->my_ip();
		if(!in_array($client_ip, array('61.219.172.11', '61.219.172.82')))
		{
			trigger_error('..block..from:'.$client_ip.'..unknown network..');
			return false;
		}
		return true;
	}
	
	// 取得 IP
	function my_ip()
	{
		if (getenv('HTTP_X_FORWARDED_FOR')) 
		{
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_X_REAL_IP')) 
		{
			$ip = getenv('HTTP_X_REAL_IP');
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
	
	// 同步 （由排程呼叫）
	public function sync_minutely()
	{
		$this->sync_data_model->sync_pks_groups();	// 同步在席現況
	}
	
	// 20170816 手動新增入場資料
	public function gen_carin()
	{
		trigger_error(__FUNCTION__ . '..from..'. $this->my_ip() .'..network..');	// IP 會浮動？
		
		$lpr = $this->input->post('lpr', true);
		$station_no = $this->input->post('station_no', true);
		$c_s = $this->input->post('c_s', true);
		
		// 確認正確性
		if(empty($c_s) || $c_s != md5($lpr.'altob'.$station_no.'botla'. __FUNCTION__ ))
		{
			trigger_error(__FUNCTION__ . "..{$lpr}|{$station_no}|{$c_s}..check fail..");
			echo 'fail';
			exit;
		}
		
		$parms = array(
					'sno' => $station_no,
					'lpr' => $lpr,
					'etag' => 'NONE',
					'io' => 'CI',
					'ivsno' => 0
				);
				
		$this->carpark_model->gen_carin($parms);
        echo 'ok';
	}
	
	// ------------------------------------------------
	//
	// 接收端 (END)
	//
	// ------------------------------------------------
	
	
	
	
	// [START] 2016/06/03 登入
	
	public function index()
	{                   
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data['type'] = $session_data['type'];
			
			if($data['type'] == 'admin')
			{
				$this->show_page('admin_page', $data); // 進階管理者介面
			}
			else
			{
				$this->show_page('main_page', $data); // 一般
			}
		}
		else
		{
			//If no session, redirect to login page
			//redirect('login', 'refresh');
			$this->show_page('login_page');
		}
	}
	
	// 登入
	public function user_login()
	{   		
		// form_validation
		$this->form_validation->set_rules('login_name', 'login_name', 'trim|required');
		$this->form_validation->set_rules('pswd', 'pswd', 'trim|required');

		if($this->form_validation->run() == FALSE)
		{
			return RESULT_FORM_VALIDATION_FAIL;
		}
		
		// go model
		$data = array
				(
					'login_name' => $this->input->post('login_name', true),                 
					'pswd' => $this->input->post('pswd', true)
				);                           
		
		$result = $this->user_model->user_login($data);
		
		if($result)
		{
			$sess_array = array();
			foreach($result as $row)
			{
				$sess_array = array
				(
					'username' => $row->login_name ,
					'type' => $row->user_type
				);
				$this->session->set_userdata('logged_in', $sess_array);
			}
			echo RESULT_SUCCESS;
		}
		else
		{
			return RESULE_FAIL;
		}
	}
	
	// 登出
	public function user_logout()
	{   
		$this->session->unset_userdata('logged_in');
		session_destroy();
		return RESULT_SUCCESS;
	}
	
	// [END] 2016/06/03 登入
	
	

    
    
    // response http               
	protected function http_return($return_code, $type)
	{                                      
    	if ($type == 'text')	echo $return_code;
        else					echo json_encode($return_code, JSON_UNESCAPED_UNICODE);  
        
    }    
    
    // 送出html code     	
	public function get_html()
	{                             
		/*
    	$data = array
        (
			'company_no' => $this->input->post('company_no', true),	// 場站統編
            'hq_company_no' => '80682490'         
        );
		$this->load->view(APP_NAME.'/'.$this->input->post('tag_name', true), $data);
		*/
    	$this->load->view(APP_NAME.'/'.$this->input->post('tag_name', true), array());
	}         
    
    // 讀取cookie內容
	protected function get_cookie($cookie_name)
	{                     
    	if (empty($_COOKIE[$cookie_name]))	return array();
    	return(json_decode($_COOKIE[$cookie_name], true));  
    }  
    
    
    // 儲存cookie內容
	protected function save_cookie($cookie_name, $cookie_info)
	{ 
    	return setcookie($cookie_name, json_encode($cookie_info, JSON_UNESCAPED_UNICODE), 0, '/');
    } 
    
    
    // 月租資料同步    	
	public function rent_sync()
	{                           
    	$station_no = $this->input->post('station_no', true);
    	$start_date = $this->input->post('start_date', true);
    	$end_date = $this->input->post('end_date', true);
                
        // $data = $this->carpark_model->rent_sync($station_no, $start_date, $end_date);
		                                     
        // print_r($data);
	}       
    // 重設剩餘車位數    	
	public function available_set()
	{                                           
        $data = $this->carpark_model->available_set();
        $this->http_return($data, 'json');
	}
    
          
    // 剩餘車位數更新    	
	public function available_update()
	{       
    	$station_no = $this->input->get_post('station_no', true);                                    
    	$data['name'] = $this->input->get_post('st_name', true);                                    
    	$data['tot_pkg'] = $this->input->get_post('tot_pkg', true); 
    	$data['ava_pkg'] = $this->input->get_post('ava_pkg', true); 
                                           
        $this->http_return($this->carpark_model->available_update($station_no, $data), 'json');
	} 
    
          
    // 剩餘車位數查核    	
	public function available_check()
	{       
    	$time_point = $this->input->get_post('time_point', true);    
                                           
        $this->http_return($this->carpark_model->available_check($time_point), 'json');
	}
    
    // 顯示logs
	public function show_logs()
	{             
        $lines = $this->uri->segment(3);	// 顯示行數
        if (empty($lines)) $lines = 40;		// 無行數參數, 預設為40行
    	
        // echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><pre style="white-space: pre-wrap;">';
        echo '<html lang="zh-TW"><body><pre style="white-space: pre-wrap;">';
		passthru('/usr/bin/tail -n ' . $lines . '  ' . LOG_FILE);		// 利用linux指令顯示倒數幾行的logs內容 
        echo "\n----- " . LOG_FILE . ' -----';   
        echo '</pre></body></html>';
	}    
	
    
    // 新增月租資料
    public function member_add()
	{          
    	$data = array
        		(
					'member_no' => $this->input->post('member_no', true),                 
					'station_no' => $this->input->post('station_no', true),                 
					'lpr' => strtoupper($this->input->post('lpr', true)),                 
					'old_lpr' => strtoupper($this->input->post('old_lpr', true)),                 
					'etag' => strtoupper($this->input->post('etag', true)),                 
					'start_date' => $this->input->post('start_date', true),                 
					'end_date' => $this->input->post('end_date', true),                 
					'member_name' => $this->input->post('member_name', true),         
					'member_nick_name' => $this->input->post('member_name', true),         
					'mobile_no' => $this->input->post('mobile_no', true),                
					'member_id' => $this->input->post('member_id', true),                          
					'contract_no' => $this->input->post('contract_no', true),                          
					'amt' => $this->input->post('amt', true),                          
					'tel_h' => $this->input->post('tel_h', true),                          
					'tel_o' => $this->input->post('tel_o', true),                          
					'addr' => $this->input->post('addr', true)                          
                );                           
                                                          
        trigger_error("add:".print_r($data, true));
        if ($data['member_no'] == 0 || $data['old_lpr'] != $data['lpr'])
        {
    		if ($this->carpark_model->check_lpr($data['lpr']) > 0)
        	{
       			echo '車牌重複, 請查明再輸入'; 
                exit;  
        	} 
        }
        
        $this->carpark_model->member_add($data);
        echo 'ok';
	}  
    
    
    // 查詢月租資料
    public function member_query()
	{                                
        $data = $this->carpark_model->member_query();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}    
    
    
    // 刪除月租資料
    public function member_delete()
	{                                
        $member_no = $this->input->post('member_no', true);
        $this->carpark_model->member_delete($member_no);
        echo 'ok';
	}       
    
       
    // 進出場現況表
    public function cario_list()
	{                                
        $data = $this->carpark_model->cario_list(); 
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
    
       
    // 進出場事件即時顯示
    public function cario_event()
	{                    
    	set_time_limit(0);            
        while(true)
        {
          
        }
	}
    
    
    // 顯示圖檔(http://url/carpark.html/pics/lpr_ABY8873_O_0_0_C_20150919210022)
    public function pics()
	{                                                         
    	// ???
        readfile(CAR_PIC.$this->uri->segment(3).'/'.str_replace('/', '', $this->uri->segment(4)).'.jpg');
	}    
    
    
    // 汽車開門
    public function opendoors()
	{   
        $ivsno = $this->uri->segment(3);
    	$lanes = array
        (
			0 => array ('devno' => 0, 'temp' => 0, 'member' => 1),		// 1號入口, temp:臨停, member:月租	        
//			1 => array ('devno' => 0, 'temp' => 2, 'member' => 3),		// 2號調撥入口	        
			1 => array ('devno' => 1, 'temp' => 0, 'member' => 1),		// 3號調撥出口	        
			2 => array ('devno' => 1, 'temp' => 0, 'member' => 1),		// 3號調撥出口	        
			3 => array ('devno' => 1, 'temp' => 2, 'member' => 3)		// 4號出口	
        );
                                              
        $url = 'http://admin:99999999@192.168.10.53/cgi-bin/basic_setting.cgi?ID=1&';
        $member_tag = 'member';	// member:月租會員
                     
		// 短路開柵欄
        @get_headers("{$url}OUTDEV={$lanes[$ivsno]['devno']}&OUTCH={$lanes[$ivsno][$member_tag]}&OUTSTATUS=1");  
                
		usleep(400000);		// 暫停0.4秒  
        
        // 斷路, 車過關柵欄 
		@get_headers("{$url}OUTDEV={$lanes[$ivsno]['devno']}&OUTCH={$lanes[$ivsno][$member_tag]}&OUTSTATUS=0");
	}
       
    
    
    // 調撥車道查詢
    public function reversible_lane_query()
	{     
    	$max_lane = 4;	// 共幾個車道數
        for ($idx = 0; $idx < $max_lane; ++$idx)
        {
          	$data[$idx] = file_get_contents("http://192.168.10.51:8090/cgi-bin/switcher.cgi?id={$idx}&action=9");
        }   
        // $data = array(1, 1, 0, 1);
        echo json_encode($data);                       
	}  
       
    
    // 車號入場查詢
    public function carin_lpr_query()
	{              
    	$lpr = $this->uri->segment(3);
        $data = $this->carpark_model->carin_lpr_query($lpr); 
        echo json_encode($data);                       
	}  
               
    
    // 以時間查詢入場資訊
    public function carin_time_query()
	{              
    	$time_query = $this->input->post('time_query', true);
    	$minutes_range = $this->input->post('minutes_range', true);
        
        $data = $this->carpark_model->carin_time_query($time_query, $minutes_range); 
        echo json_encode($data);                       
	}   
    
    // 調撥車道設定
    public function reversible_lane_set()
	{     
        $lane_no = $this->input->post('lane_no', true);            
        $actions = $this->input->post('actions', true);            
        $data = file_get_contents("http://192.168.10.51:8090/cgi-bin/switcher.cgi?id={$lane_no}&action={$actions}");
        
        echo "{$lane_no}|{$actions}|{$data}";                       
	}     
    
                         
    // 在席車位檢查未有入場資料清單
    public function pks_check_list()
	{                      
        $max_rows = $this->uri->segment(3, 100);	// 一次讀取筆數, 預設為100筆
        $data = $this->carpark_model->pks_check_list($max_rows);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }       
     
                         
    // 重設在席查核
    public function reset_pks_check()
	{                      
        $data = $this->carpark_model->reset_pks_check();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }            
    
           
    // 更正在席車號
    public function correct_pks_lpr()
	{                       
        $pksno = $this->uri->segment(3, 0);	// 車格號碼
        $lpr = $this->uri->segment(4, 'NONE');	// 車號
        
        if ($pksno == 0 || $lpr == 'NONE')
        {
        	echo json_encode(array('err' => 1, 'cario_no' => 0), JSON_UNESCAPED_UNICODE); 
        }                 
        else
        {
       		$data = $this->carpark_model->correct_pks_lpr($pksno, $lpr);
        	echo json_encode($data, JSON_UNESCAPED_UNICODE);       
        }
    }          
    
                     
    // 入場車號查核在席無資料清單
    public function carin_check_list()
	{                      
        $max_rows = $this->uri->segment(3, 20);	// 一次讀取筆數, 預設為100筆
        $data = $this->carpark_model->carin_check_list($max_rows);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } 
           
    // 更正入場車號
    public function correct_carin_lpr()
	{                       
        $cario_no = $this->uri->segment(3, 0);	// 車格號碼
        $lpr = $this->uri->segment(4, 'NONE');	// 車號
        $in_time = urldecode($this->uri->segment(5, ''));	// 入場時間
        
       	$data = $this->carpark_model->correct_carin_lpr($cario_no, $lpr, $in_time);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);       
    } 
	
	// 查詢行動支付記錄
    public function tx_bill_query()
	{                                
        $data = $this->carpark_model->tx_bill_query();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
	} 
	
	// 查詢月租繳款機記錄
    public function tx_bill_ats_query()
	{                                
        $data = $this->carpark_model->tx_bill_ats_query();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
	} 
	
	// 查詢樓層在席群組
    public function pks_group_query()
	{                                
        $data = $this->carpark_model->pks_group_query();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
	} 
	
	// 微調剩餘車位數
    public function pks_availables_update()
	{                      
		$group_id = $this->uri->segment(3);		// id
        $value = $this->uri->segment(4, 0);		// value
		$station_no = $this->uri->segment(5);	// station_no
        $data = $this->sync_data_model->pks_availables_update($group_id, $value, true, $station_no);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

	// 進出場觸發 888 呼叫
	/*
	public function sync_888()
	{
		$io = $this->uri->segment(3);	// CI, CO, MI, MO
		
		if(empty($io))
		{
			echo 'io_not_found';
			exit;
		}
			
		$parms = array('io' => $io, 'etag' => __FUNCTION__, 'lpr' => __FUNCTION__);
		echo $this->sync_data_model->sync_888($parms);
		exit;
	}
	*/
	
	public function test()
	{
		//echo `whoami`;
		//echo phpinfo();
	}
	
	// 博辰測試用
	// http://localhost/carpark.html/test_8068_002/APK7310
	public function test_8068_002()
	{
		$lpr_in = $this->uri->segment(3);
		
		$seqno = '00001';
		$cmd = '002';
		$token = '000000000';
		$lpr = str_pad($lpr_in, 7, '%', STR_PAD_LEFT);
		$in_time = '2000/01/01 00:00:00';
		
		$error_str = '';
		$service_port = 8068;
		$address = "192.168.10.201";

		/* Create a TCP/IP socket. */
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
			$error_str .= "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
		} else {
			echo "OK.\n";
		}

		echo "Attempting to connect to '{$address}' on port '{$service_port}'...";
		$result = socket_connect($socket, $address, $service_port);
		if ($result === false) {
			echo "socket_connect() failed.\nReason: ({$result}) " . socket_strerror(socket_last_error($socket)) . "\n";
			$error_str .= "socket_connect() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
		} else {
			echo "OK.\n";
		}
		
		$in = pack('Ca5Ca3Ca9Ca7Ca19', 0x1c, $seqno, 0x1c, $cmd, 0x1c, 
					$token, 0x1f, $lpr, 0x1f, $in_time
				);
		$out = '';
		echo "socket_write:";
		echo "{$in}<br/><br/>";
		
		if(!socket_write($socket, $in, strlen($in)))
		{
			echo('<p>Write failed</p>');
			$error_str .= "socket_write() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
		}
		echo "socket_write..OK..";

		echo "<br/><br/>socket_read:";
		$out = socket_read($socket, 2048) or die("Could not read server responsen");
		//while ($out = socket_read($socket, 2048)) {
		//	echo $out;
		//}
		echo "{$out}<br/><br/>";
		
		echo "Closing socket...";
		socket_close($socket);
		echo "OK.\n\n";

		trigger_error($error_str);
		exit;
	}
	
	// test
	public function test_phpexcel()
	{
		$query_year = 2017;
		$query_month = 3;
		
		//$this->excel_model->export_cario_data($query_year, $query_month);
		$this->excel_model->export_members();
	}
	
}
