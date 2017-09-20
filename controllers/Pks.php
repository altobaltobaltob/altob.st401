<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
file: pks.php 	車位在席模組    

IVS -> 車號, 影像 
鼎高IVS傳送車號及影像檔   
http://203.75.167.89/pks.html/cameras/sno/12112/ivsno/3/pksno/2016/io/KI/type/C/lpr/ABC1234/color/red/sq/5236        
http://203.75.167.89/pks.html/cameras/sno/12119/ivsno/3/pksno/195/io/KO/type/C/lpr/NONE/color/red/sq/5236
sno:	場站編號(新北市圖書館:12118)
ivsno:	ivs編號, 每一支都是獨立編號(序號)
pksno:	車位編號
io:		KI:進車格, KO:出車格, KL:車牌
type:	C:汽車, H:重機, M:機車
lpr:	ABC1234(車號), 無:NONE
color:	red(紅色), 若無請用NONE(4個字)
sq:		序號(查詢時參考用)

http設定說明:
method: POST
上傳圖檔名英數字, 副檔名為gif/jpg/png均可
上傳圖檔欄位名稱為cars
*/

require_once(MQ_CLASS_FILE); 

class Pks extends CI_Controller
{          
    var $vars = array();	// 共用變數     
    
	function __construct() 
	{                            
    	// $this->time_start = microtime(true);  
		parent::__construct();          
        
		ignore_user_abort();	// 接受client斷線, 繼續run
               
        $method_name = $this->router->fetch_method();
        if ($method_name == 'cameras')
        {
        	ob_end_clean();
			ignore_user_abort();
			ob_start();
			header('Connection: close');
			header('Content-Length: ' . ob_get_length());
			ob_end_flush();
			flush();
        }  
        
		$this->vars['date_time'] = date('Y-m-d H:i:s');	// 格式化時間(2015-10-12 14:36:21) 
		$this->vars['time_num'] = str_replace(array('-', ':', ' '), '', $this->vars['date_time']); //數字化時間(20151012143621) 
		$this->vars['date_num'] = substr($this->vars['time_num'], 0, 8);	// 數字化日期(20151012) 
		$this->vars['station_no'] = STATION_NO;	// 本站編號 
           
        // ----- 程式開發階段log設定 -----
        if (@ENVIRONMENT == 'development')
        {                        
          	ini_set('display_errors', '1');
			//error_reporting(E_ALL ^ E_NOTICE); 
			error_reporting(E_ALL); 
        }  
        set_error_handler(array($this, 'error_handler'), E_ALL);	// 資料庫異動需做log  

		// mqtt subscribe
		$this->vars['mqtt'] = new phpMQTT(MQ_HOST, MQ_PORT, uniqid());  
		
		if(!$this->vars['mqtt']->connect()){ die ('Could not connect mqtt');  }		
        
        // ----- 定義常數(路徑, cache秒數) -----       
        define('APP_VERSION', '100');		// 版本號
                                        
        define('MAX_AGE', 604800);			// cache秒數, 此定義1個月     
        define('APP_NAME', 'pks');		// 應用系統名稱   
          
        define('PAGE_PATH', APP_BASE.'ci_application/views/'.APP_NAME.'/');						// path of views
        
        define('SERVER_URL', 'http://'.(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost').'/');	// URL
        define('APP_URL', SERVER_URL.APP_NAME.'.html/');										// controller路徑 
        define('WEB_URL', SERVER_URL.APP_NAME.'/');												// 網頁路徑
        define('WEB_LIB', SERVER_URL.'/libs/');													// 網頁lib
        define('BOOTSTRAPS', WEB_LIB.'bootstrap_sb/');											// bootstrap lib  
        define('LOG_PATH', FILE_BASE.APP_NAME.'/logs/');		// log path name
        define('LOG_FILE', FILE_BASE.APP_NAME.'/logs/pks.');	// log file name
        
		$this->load->model('pks_model'); 
        $this->pks_model->init($this->vars);
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
        
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == APP_VERSION && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time)
    	{                  
        	header('HTTP/1.1 304 Not Modified');
    	}
        else
        {                                           
        	$this->load->view(APP_NAME.'/'.$page_name, $data);
        }    
	} 
        
        	
	public function parked()
	{                               
		$data['group_id'] = $this->uri->segment(3);  
		$data['init_value'] = $this->uri->segment(4);  
        // $data['client_id'] = uniqid();
        // $data['mqtt_ip'] = '192.168.10.201';
        // $data['port_no'] = 8000;
        $this->load->view(APP_NAME.'/parked', $data);
	}
	 
	// 樓層平面圖
    // http://203.75.167.89/parkingquery.html/floor_map
	public function floor_map()
	{    
    	/*
    	header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');
        */

		$this->load->view("parkingquery/floor_map");
	}        
    
    
    // response http               
	protected function http_return($return_code, $type)
	{                                      
    	if ($type == 'text')	echo $return_code;
        else					echo json_encode($return_code, JSON_UNESCAPED_UNICODE);  
    }      
    
    // 顯示logs
	public function show_logs()
	{             
        $lines = $this->uri->segment(3);	// 顯示行數
        if (empty($lines)) $lines = 40;		// 無行數參數, 預設為40行
    	
        // echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><pre style="white-space: pre-wrap;">';
        echo '<html lang="zh-TW"><body><pre style="white-space: pre-wrap;">';
        if (PHP_OS == 'Linux')
			passthru('/usr/bin/tail -n ' . $lines . '  ' . LOG_FILE);		// 利用linux指令顯示倒數幾行的logs內容
        else 
			passthru('d:/afiles/bin/unix_cmd/tail.exe -n ' . $lines . '  ' . LOG_FILE);	
        echo "\n----- " . LOG_FILE . ' -----';   
        echo '</pre></body></html>';
	}    
         
    
    // IVS -> 車號, 影像 
    /*
	IVS -> 車號, 影像 
	鼎高IVS傳送車號及影像檔   
	http://203.75.167.89/pks.html/cameras/sno/12119/ivsno/3/pksno/102/io/KI/type/C/lpr/ABC1234/color/red/sq/5236
	sno:	場站編號(新北市圖書館:12118)
	ivsno:	ivs編號, 每一支都是獨立編號(序號)
	pksno:	車位編號
	io:		KI:進車格, KO:出車格, KL:車牌辨識
	type:	C:汽車, H:重機, M:機車
	lpr:	ABC1234(車號)
	color:	red(紅色), 若無請用NONE(4個字)
	sq:		序號(查詢時參考用)

	http設定說明:
	method: POST
	上傳圖檔名英數字, 副檔名為gif/jpg/png均可
	上傳圖檔欄位名稱為cars
    */
    public function cameras()
	{                             
    	$parms = $this->uri->uri_to_assoc(3);
        trigger_error('在席參數傳入:'.print_r($parms, true));  
        
        // array_map('unlink', glob(PKS_PIC."pks-{$parms['pksno']}-*")); 
                                          
        /*
        // 車入格後的車牌辨識(lpr), 傅送圖檔
        if ($parms['io'] == 'KL')
        {                                   
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
        }      
        */
        $this->pks_model->pksio($parms);	// 車輛進出車格資料庫處理 
        exit;          
	}   
    
         
    // 重新計算
    // http://203.75.167.89/pks.html/reculc/
    public function reculc()
	{ 
    	$this->pks_model->reculc();  
    }
	
	
	
	// 取得所有車位狀態資訊
    // http://203.75.167.89/pks.html/query_station_status/12112
	public function query_station_status() 
	{   
		$station_no = $this->uri->segment(3);      
        $data = $this->pks_model->query_station_status($station_no);
        echo json_encode($data, JSON_UNESCAPED_UNICODE); 
    }
	
	// 取得車位資訊
    // http://203.75.167.89/pks.html/query_station_pks/12112/2021
	public function query_station_pks(){
		$station_no = $this->uri->segment(3);      
		$pksno = $this->uri->segment(4);      
		$data = $this->pks_model->query_station_pks($station_no, $pksno);
		echo json_encode($data, JSON_UNESCAPED_UNICODE); 
	}
	
	// 車位狀態資訊圖
    // http://203.75.167.89/pks.html/status_map
	public function status_map()
	{    
		$this->show_page("status_map");
	}  
	
}
