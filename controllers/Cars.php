<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
file: cars.php	車輛進出場處理(板車版含感應線圈) 
              
URL:
http://192.168.10.201/cars.html/ipcam/sno/12119/ivsno/0/io/O/type/C/lpr/4750YC/color/NULL/sq/0/ts/1441051995/sq2/0/etag/ABCD123456789/ant/1
                                                                  
javascript/html從server讀取key/value方式
http://61.220.179.128/jsid.i/ip=ip&uniqid=clientid&mqtt_ip=mqtt_ip
例如:
<script src="http://61.220.179.128/jsid.i/ip=ip&uniqid=clientid&mqtt_ip=mqtt_ip"></script> 
response:
var ip='66.249.82.183';var clientid='565162cb67dfb';var mqtt_ip='192.168.51.11';

*/

// ----- 定義常數(路徑, cache秒數) -----       
        define('APP_VERSION', '100');		// 版本號
                                        
        define('MAX_AGE', 604800);			// cache秒數, 此定義1個月     
        define('APP_NAME', 'cars');		// 應用系統名稱   
          
        define('PAGE_PATH', APP_BASE.'ci_application/views/'.APP_NAME.'/');						// path of views
        
        define('SERVER_URL', 'http://'.(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost').'/');	// URL
        define('APP_URL', SERVER_URL.APP_NAME.'.html/');										// controller路徑 
        define('WEB_URL', SERVER_URL.APP_NAME.'/');												// 網頁路徑
        define('WEB_LIB', SERVER_URL.'/libs/');													// 網頁lib
        define('BOOTSTRAPS', WEB_LIB.'bootstrap_sb/');											// bootstrap lib  
        define('LOG_PATH', FILE_BASE.APP_NAME.'/logs/');		// log path name
        define('LOG_FILE', FILE_BASE.APP_NAME.'/logs/cario.');	// log file name

require_once(MQ_CLASS_FILE); 

class Cars extends CI_Controller
{          
    var $vars = array();      
    
	function __construct() 
	{                            
    	// $this->time_start = microtime(true);  
		parent::__construct();      
        
		ignore_user_abort();	// 接受client斷線, 繼續run 
               
        $method_name = $this->router->fetch_method();
        if ($method_name == 'ipcam' || $method_name == 'check_lpr_etag')
        {
        	ob_end_clean();
			ignore_user_abort();
			ob_start();
			header('Connection: close');
			header('Content-Length: ' . ob_get_length());
			ob_end_flush();
			flush();
        }
        else if($method_name == 'opendoor')
		{
			ob_end_clean();
			ignore_user_abort();
			ob_start();
			
			echo 'ok';
			
			header('Connection: close');
			header('Content-Length: ' . ob_get_length());
			ob_end_flush();
			flush();
		}
        
		$this->vars['date_time'] = date('Y-m-d H:i:s');	// 格式化時間(2015-10-12 14:36:21) 
		$this->vars['time_num'] = str_replace(array('-', ':', ' '), '', $this->vars['date_time']); //數字化時間(20151012143621) 
        $this->vars['date_num'] = substr($this->vars['time_num'], 0, 8);	// 數字化日期(20151012) 
		//$this->vars['station_no'] = STATION_NO;	// 本站編號    
        
        session_id(ip2long($_SERVER['REMOTE_ADDR']));	// 設定同一device為同一個session 
        session_start();   
            
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
		$this->vars['mcache']->pconnect(MEMCACHE_HOST, MEMCACHE_PORT) or die ('Could not connect memcache');   
		
		// mqtt subscribe
		$this->vars['mqtt'] = new phpMQTT(MQ_HOST, MQ_PORT, uniqid());  
		//if(!$this->vars['mqtt']->connect()){ die ('Could not connect mqtt');  }	
		if(!$this->vars['mqtt']->connect()){ trigger_error('..Could not connect mqtt..go on..'); }			
        
		$this->load->model('cars_model'); 
        $this->cars_model->init($this->vars);
	}
     
    
    // 發生錯誤時集中在此處理
	public function error_handler($errno, $errstr, $errfile, $errline, $errcontext)
	{           
    	$log_msg = explode('://', $errstr);
        if (count($log_msg) > 1)
        {
            $log_file = $log_msg[0];    
        	$str = date('H:i:s')."|{$log_msg[1]}|{$errfile}|{$errline}|{$errno}\n"; 
        } 
        else
        {   
        	$log_file = APP_NAME;
    		$str = date('H:i:s')."|{$errstr}|{$errfile}|{$errline}|{$errno}\n";
        }              
          
        error_log($str, 3, LOG_PATH.$log_file . '.' . date('Ymd').'.log.txt');	// 3代表參考後面的檔名  
    }
    
    
    // 顯示logs
	public function show_logs()
	{             
        $lines = $this->uri->segment(3);	// 顯示行數
        if (empty($lines)) $lines = 140;		// 無行數參數, 預設為40行
    	
        // echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><pre style="white-space: pre-wrap;">';
        echo '<html lang="zh-TW"><body><pre style="white-space: pre-wrap;">';      
       
		passthru('/usr/bin/tail -n ' . $lines . '  ' . LOG_PATH.APP_NAME . '.' . date('Ymd').'.log.txt');	// 利用linux指令顯示倒數幾行的logs內容 
        echo "\n----- " . LOG_PATH.APP_NAME . '.' . date('Ymd').'.log.txt' . ' -----';   
        echo '</pre></body></html>';
	}
	
	/*
		出入口
		
		說明: 與ipcam相同判斷邏輯, 但不做任何資料更改
    */
	public function opendoor()
	{
		$parms = $this->uri->uri_to_assoc(3);
		$parms['lpr'] = urldecode($parms['lpr']); // 中文車牌
		$this->cars_model->opendoor_lprio($parms);
	}
    
    // IVS -> 車號, 影像 
    /*
    	鼎高IVS傳送車號及影像檔
http://192.168.10.201/cars.html/ipcam/sno/12119/ivsno/0/io/O/type/C/lpr/4750YC/color/NULL/sq/0/ts/1441051995/sq2/0/etag/ABCD123456789/ant/1
		sno:       場站編號(光興國小:12119)
		ivsno:     ivs編號, 每一支都是獨立編號(序號)
		io:        i:進場, o:出場
		type:       C:汽車, H:重機, M:機車
		lpr:  ABC-1234(車號)
		color:     red(紅色), 若無請用NULL(4個字)
		sq: 序號(參考用) 
        sq2:		暫不用
        etag:		eTag ID
        ant:		eTag

		http設定說明:
		method: POST
		上傳圖檔名英數字, 副檔名為gif/jpg/png均可
		上傳圖檔欄位名稱為cars
    */
    public function ipcam()
	{                             
    	$parms = $this->uri->uri_to_assoc(3);
		$parms['lpr'] = urldecode($parms['lpr']); // 中文車牌
		
		// 同步並送出一次出入口 888
		$this->load->model('sync_data_model'); 
		$this->sync_data_model->init($this->vars);
		$this->sync_data_model->sync_888($parms);
                                                                  
        $pic_folder = CAR_PIC.$this->vars['date_num'].'/';		// 今日資料夾名(yyyymmdd)
        if (!file_exists($pic_folder))	mkdir($pic_folder);		// 如果資料夾不存在, 建立日期資料夾
        
        $config['upload_path'] = $pic_folder;
        // $config['allowed_types'] = 'gif|jpg|png';                 
        $config['allowed_types'] = '*';                 
        // ex. lpr_1625AB_I_1_152_C_1_2015080526.jpg -> car_交易序號_進出_順序_車號_時間.jpg
        $config['file_name'] = "lpr-{$parms['lpr']}-{$parms['io']}-{$parms['ivsno']}-{$parms['sq']}-{$parms['type']}-{$parms['sq2']}-{$this->vars['time_num']}.jpg"; 
		
		if (!isset($_FILES['cars'])) 
		{
			$status = 'error';		// 顯示上傳錯誤
			trigger_error('[ERROR] cars not found: ' . print_r($_FILES, true));
		}
		else
		{
			$this->load->library('upload', $config);
        
			if(!$this->upload->do_upload('cars')){         
				$status = 'error';		// 顯示上傳錯誤
				trigger_error($this->upload->display_errors());
			} 
			else
			{
				// 若無錯誤，則上傳檔案
				$file = $this->upload->data('cars');
				$status = 'ok';
			}
		}
        
        $parms['obj_type'] = 1;	// 車牌類  
        $parms['curr_time_str'] = $this->vars['date_time'];	// 現在時間, 例2015-09-21 15:36:47  
        $parms['pic_name'] = $config['file_name'];	// 圖片檔名 
        
        $this->cars_model->lprio($parms);	// 測試eTag
	}     
    
    // 用車牌與eTag, 檢查資料庫
    public function check_lpr_etag()
	{                                  
    	$lpr = $this->uri->segment(3);
    	$etag = $this->uri->segment(4);  
        
        $this->cars_model->check_lpr_etag($lpr, $etag);	
        exit;
    }     

	public function test_now()
	{
		echo date('Y-m-d H:i:s');
	}	
	
	public function test_phpinfo()
	{
		phpinfo();
	}
	
}
