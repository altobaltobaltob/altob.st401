<!DOCTYPE html>
<html lang="zh-TW">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>歐特儀營管系統</title>  
<script>   
// document.write("<base href='http://" + document.location.host + "' />");
</script>
<script src="/libs/js/jquery.min.js"></script>
<script src="/admins_station.html/js_vars"></script>
<style>                                            
.cario_list{text-align:center;vertical-align:middle;}
</style>
<!-- Bootstrap Core CSS -->
<link href="/libs/bootstrap_sb/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- MetisMenu CSS -->
<link href="/libs/bootstrap_sb/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
<!-- Timeline CSS -->
<link href="/libs/bootstrap_sb/dist/css/timeline.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="/libs/bootstrap_sb/dist/css/sb-admin-2.css" rel="stylesheet">
<!-- Morris Charts CSS -->
<link href="/libs/bootstrap_sb/bower_components/morrisjs/morris.css" rel="stylesheet">
<!-- Custom Fonts -->
<link href="/libs/bootstrap_sb/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<!-- Bootstrap Core JavaScript -->
<script src="/libs/bootstrap_sb/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/libs/bootstrap_sb/bower_components/metisMenu/dist/metisMenu.min.js"></script>  
<link href="/libs/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="/libs/bootstrap/js/bootstrap-datetimepicker.min.js"></script> 
<script src="/libs/bootstrap/js/bootstrap-datetimepicker.zh-TW.js"></script> 
<!-- Morris Charts JavaScript -->
<script src="/libs/bootstrap_sb/bower_components/raphael/raphael-min.js"></script>
<!-- Custom Theme JavaScript -->
<script src="/libs/bootstrap_sb/dist/js/sb-admin-2.js"></script>
<!--script src="/libs/js/mqttws.min.js"></script--> 

	<!-- jQuery validate -->
	<script src="/libs/form-validator/jquery.form-validator.min.js"></script>
	<!-- alertify -->
	<link href="/libs/css/alertify.core.css" rel="stylesheet">
	<link href="/libs/css/alertify.bootstrap.css" rel="stylesheet">
	<script src="/libs/js/alertify.min.js"></script>
	<!-- moment -->
	<script src="/libs/js/moment.min.js"></script>

</head>
<body style="font-family:Microsoft JhengHei;">
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand">歐特儀營管系統<span id="user_name" style="font-size:10px;color:blue;"></span></a>
				<button id='un_synced_count_btn' class='btn btn-default' style='font-size:16px; display:none;' onclick='do_sync_batch();'>
					場站尚有&nbsp;<span style='color:red' id='un_synced_count_str'></span>&nbsp;筆資料待同步！ （點撃此按鈕同步）
				</button>
            </div>
            <!-- /.navbar-top-links(左側選單) -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
						<li>
                            <a href="#"><i class="fa fa-edit fa-fw"></i>帳務管理<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">  
                                <!--li>
                                    <a href="#" onclick="show_item('invoice_close', 'invoice_close');">關帳（每日）</a>
                                </li> 
                                <li>
                                    <a href="#" onclick="show_item('invoice_history', 'invoice_history');">關帳記錄</a>
                                </li-->
								<li>
                                    <a href="#" onclick="show_item('check_point', 'check_point');">關帳（手動）</a>
                                </li> 
								<li>
                                    <a href="#" onclick="show_item('check_point_report', 'check_point_report');">關帳查詢</a>
                                </li>
								<li>
                                    <a href="#" onclick="show_item('member_invoice_report', 'member_invoice_report');">電子發票查詢</a>
                                </li>
								<li>
                                    <a href="#" onclick="show_item('price_plan_query_all', 'price_plan_query_all');">費率設定</a>
                                </li>
                            </ul>
                        </li>					
                        <li>
                            <a href="#"><i class="fa fa-user fa-fw"></i>會員管理<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">  
                                <li>
                                    <a href="#" onclick="show_item('member_query', 'member_query');">會員查詢</a>
                                </li>
                                <li>
                                    <a href="#" onclick="show_item('member_add', 'member_add');">會員加入</a>
                                </li>
								<li>
                                    <a href="#" onclick="show_item('member_query_all', 'member_query_all');">會員清單</a>
                                </li>
								<li>
                                    <a href="#" onclick="show_item('member_tx_refund_query', 'member_tx_refund_query');">退租查詢</a>
                                </li>								
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>    
						<li>
                            <a href="#"><i class="fa fa-user fa-fw"></i>查核作業<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">  
								<li>
                                    <a href="#" onclick="show_item('member_tx_check_query', 'member_tx_check_query');">待審核交易</a>
                                </li>
								<li>
                                    <a href="#" onclick="show_item('cario_temp_not_finished_query_all', 'cario_temp_not_finished_query_all');">臨停未結清單</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>   
						<li>
                            <a href="#"><i class="fa fa-user fa-fw"></i>發票管理<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">  
								<li>
                                    <a href="#" onclick="show_item('member_tx_bill_query', 'ok');">待開立清單</a>
                                </li>
								<li>
                                    <a href="#" onclick="show_item('member_tx_bill_query', 'more');">待補開清單</a>
                                </li>
								<li>
                                    <a href="#" onclick="show_item('member_tx_bill_query', 'refund');">待折讓清單</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>   
						
                        <!--li>
                            <a href="#"><i class="fa fa-user fa-fw"></i>發票管理<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">  
                                <li>
                                    <a href="#" onclick="show_item('invoice_open', 'invoice_open');">發票開帳</a>
                                </li> 
                                <li>
                                    <a href="#" onclick="show_item('invoice_reprint', 'invoice_reprint');">發票補印</a>
                                </li> 
                                <li>
                                    <a href="#" onclick="show_item('invoice_cancel', 'invoice_cancel');">發票作廢</a>
                                </li>
                                <li>
                                    <a href="#" onclick="show_item('invoice_rows', 'invoice_rows');">發票下載</a>
                                </li> 
                                <li>
                                    <a href="#" onclick="show_item('invoice_close', 'invoice_close');">發票清帳</a>
                                </li> 
                            </ul>
                        </li-->                        
                        <li>
                        	<a href="#" onclick="location.replace('/admins_station.html');">登出</a>
                            <!-- /.nav-second-level -->
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        <div id="page-wrapper"><!-- 主要資料顯示區 -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">營業管理作業</h1><!-- 右側小表頭 -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row --> 
			
            <!-- ----- 會員查詢 ----- -->
            <div data-items="member_query" class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            會員查詢
                            <form id="member_query_form" role="form">
                            <div class="form-group">
                            <label class="select-inline" for="station_select">
                            <select class="form-control" id="station_select">
                            </select>
                            </label>
                            <label class="radio-inline"><input type="radio" name="q_item" value="lpr" checked />車號</label>
                            <label class="radio-inline"><input type="radio" name="q_item" value="member_name" />姓名</label>
                            <label class="radio-inline"><input type="radio" name="q_item" value="mobile_no" />手機</label>
                            <label class="radio-inline"><input type="radio" name="q_item" value="end_date" />到期日</label>
                            <label class="radio-inline"><input type="radio" name="q_item" value="contract_no" />合約號碼</label>
                            <label class="input-inline">&nbsp;&nbsp;<input type="text" id="q_str" placeholder="關鍵字" /></label>
                            <label class="input-inline"><input type="submit" value="查詢" /></label> 
                            </div>
                            </form>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left;">場站</th>
                                            <th style="text-align:left;">車號</th>
                                            <th style="text-align:left;">姓名</th>
                                            <th style="text-align:center;">手機</th>
                                            <th style="text-align:center;">開始日</th>
                                            <th style="text-align:center;">結束日</th>
                                            <th style="text-align:center;">繳期</th>
											<th style="text-align:center;">身份</th>
                                            <th style="text-align:center;">合約號</th>
                                            <!--th style="text-align:center;">租金</th-->
											<th style="text-align:center;">停權</th>
                                            <th style="text-align:center;">功能</th>
                                        </tr>
                                    </thead>
                                    <tbody id="member_list" style="font-size:22px;"></tbody>
                                </table>
                            </div><!-- ----- end of dataTable_wrapper ----- -->  
                        </div><!-- ----- end of panel-body ----- -->
                    </div><!-- ----- end of panel panel-default ----- -->
                </div><!-- ----- end of col-lg-12 ----- -->
            </div>
            <!-- ----- 會員查詢(結束) ----- -->
			
			<!-- ----- 交易記錄 ----- -->
            <div data-items="member_tx_query" class="row" style="display:none;">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            交易記錄（
								車號：<span id='member_tx_query_lpr_str_1'></span>&nbsp;，
								會員開始日：<span id='member_tx_query_start_date_last_str_1'></span>
							）
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th style="text-align:center;">代號</th>
											<!--th style="text-align:left;">場站</th-->
											<th style="text-align:center;">入帳日</th>
											<th style="text-align:left;">車號</th>
											<th style="text-align:center;">會員開始日</th>
											<th style="text-align:center;">上期繳期</th>
                                            <th style="text-align:center;">上期結束日</th>
                                            <th style="text-align:center;">上期租金</th>
											<th style="text-align:center;">本期繳期</th>
											<th style="text-align:center;">本期開始日</th>
                                            <th style="text-align:center;">本期結束日</th>
                                            <th style="text-align:center;">本期租金</th>
											<!--th style="text-align:center;">發票時間</th>
											<th style="text-align:center;">買方統編</th>
											<th style="text-align:center;">賣方統編</th>
											<th style="text-align:center;">發票字軌</th>
											<th style="text-align:center;">發票號碼</th>
											<th style="text-align:center;">發票金額</th>
											<th style="text-align:center;">發票種類</th-->
											<th style="text-align:center;">發票記錄</th>
											<th style="text-align:center;">審核狀態</th>
											<th style="text-align:center;">有效期限</th>
											<th style="text-align:center;">備註</th>
											<th style="text-align:center;">功能</th>
                                        </tr>
                                    </thead>
                                    <tbody id="member_tx_list" style="font-size:18px;"></tbody>
                                </table>
                            </div><!-- ----- end of dataTable_wrapper ----- -->  
                        </div><!-- ----- end of panel-body ----- -->
                    </div><!-- ----- end of panel panel-default ----- -->
                </div><!-- ----- end of col-lg-12 ----- -->
            </div>
            <!-- ----- 交易查詢(結束) ----- -->
			
			<!-- ----- 發票記錄 ----- -->
            <div data-items="member_tx_bill_query" class="row" style="display:none;">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            發票記錄（
								代號：<span id='member_tx_bill_query_tx_no_str_1'></span>
							）
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th style="text-align:left;">代號</th>
											<!--th style="text-align:left;">場站</th-->
											<th style="text-align:center;">入帳日</th>
											<th style="text-align:left;">車號</th>
											<th style="text-align:center;">會員開始日</th>
											<th style="text-align:center;">上期繳期</th>
                                            <th style="text-align:center;">上期結束日</th>
                                            <th style="text-align:center;">上期租金</th>
											<th style="text-align:center;">本期繳期</th>
											<th style="text-align:center;">本期開始日</th>
                                            <th style="text-align:center;">本期結束日</th>
                                            <th style="text-align:center;">本期租金</th>
											<th style="text-align:center;">發票時間</th>
											<th style="text-align:center;">買方統編</th>
											<th style="text-align:center;">賣方統編</th>
											<th style="text-align:center;">發票金額</th>
											<th style="text-align:center;">發票字軌</th>
											<th style="text-align:center;">發票號碼</th>
											<th style="text-align:center;">發票種類</th>
											<!--th style="text-align:center;">發票開立狀態</th>
											<th style="text-align:center;">審核狀態</th>
											<th style="text-align:center;">有效期限</th>
											<th style="text-align:center;">備註</th-->
											<th style="text-align:center;">狀態</th>
											<th style="text-align:center;">待辦金額</th>
                                        </tr>
                                    </thead>
                                    <tbody id="member_tx_bill_list" style="font-size:16px;"></tbody>
                                </table>
                            </div><!-- ----- end of dataTable_wrapper ----- -->  
                        </div><!-- ----- end of panel-body ----- -->
                    </div><!-- ----- end of panel panel-default ----- -->
                </div><!-- ----- end of col-lg-12 ----- -->
            </div>
            <!-- ----- 發票記錄(結束) ----- -->
                                         
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper --> 
    
<!-- 作為浮動顯示區之用 -->
<div id="works" style="display:none;"></div>   
             
<!-- ----- 登入小框 ----- -->
<div class="modal fade" id="login_dialog">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header"><h3>登入</h3></div>
<div class="modal-body">
<form id="login_form" class="center-block" method="post" action="/admins_station.html/login_verify">    
<div class="main">	
<label>帳號</label>    
<div class="input-group">
<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
<input type="text" class="form-control" name="login_name" placeholder="請輸入帳號" autofocus />
</div>
<label>密碼</label>
<div class="input-group">
<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
<input type="password" class="form-control" name="login_pswd" placeholder="請輸入密碼">
</div>
<div class="row"><br /></div>
<div class="row">
<div class="col-xs-6 col-md-6"></div>
<div class="col-xs-6 col-md-6 pull-right">
<button type="submit" class="btn btn-large btn-success pull-right">登入</button>
</div>
</div>
</div>
</form>
</div><!-- end of modal-body --> 
</div><!-- end of modal-content --> 
</div><!-- end of modal-dialog -->
</div><!-- end of modal show -->
<!-- ----- 登入小框(結束) ----- --> 
                                    
<!-- ----- 會員時段表小框[修改或新增] ----- -->
<div class="modal fade" id="pt_dialog">
<!-- div class="modal-dialog modal-sm"-->
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header"><h3>時段表</h3></div>
<div class="modal-body">
<form id="pt_form" class="center-block">    
<div class="main">
<div class="dataTable_wrapper">
<table class="table table-striped table-bordered table-hover">
<thead>
<tr>
<th style="text-align:center;">選項</th>
<th style="text-align:center;">代碼</th>
<th style="text-align:left;">備註</th>
</tr>
</thead>
<tbody id="pt_list" style="font-size:10px;"></tbody>
</table>                    
<button type="button" class="btn btn-large btn-success pull-left" onclick="$('#pt_dialog').modal('hide');">確定</button>
&nbsp;&nbsp;
<button type="button" class="btn btn-large btn-cancel" onclick="$('#pt_dialog').modal('hide');">取消</button>
</div><!-- ----- end of dataTable_wrapper ----- -->  
</div><!-- ----- end of main ----- -->
</form>
</div><!-- end of modal-body --> 
</div><!-- end of modal-content --> 
</div><!-- end of modal-dialog -->
</div><!-- end of modal show -->
<!-- ----- 會員時段表小框[修改或新增](結束) ----- --> 

<!-- ----- 停車時段 ----- -->
<div class="modal fade" id="park_time_dialog">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header"><h3>停車時段</h3></div>
<div class="modal-body">
<form id="login_form" class="center-block" method="post" action="/admins_station.html/park_time_modify">    
<div class="main">	
<label>時段代碼</label>    
<div class="input-group">
<input type="text" class="form-control" name="time_id" placeholder="請輸入時段代碼" autofocus />
</div>
<label>排序序號</label>
<div class="input-group">
<input type="text" class="form-control" name="seqno" placeholder="請輸入排序序號" />
</div>
<div class="row"><br /></div>
<div class="row">
<div class="col-xs-6 col-md-6"></div>
<div class="col-xs-6 col-md-6 pull-right">
<button type="submit" class="btn btn-large btn-success pull-left">存檔</button>
<span class="button" data-dismiss="modal" aria-label="Close">&nbsp;&nbsp;
<button type="button" class="btn btn-large btn-cancel">取消</button>
</span>
</div>
</div>
</div>
</form>
</div><!-- end of modal-body --> 
</div><!-- end of modal-content --> 
</div><!-- end of modal-dialog -->
</div><!-- end of modal show -->
<!-- ----- 停車時段(結束) ----- -->



<div id="works" style="display:none"></div>
</body>
</html>   
<script>

function alertify_count_down($msg, $delay)
{
	alertify.set({delay : $delay});
	alertify.log($msg);
}
function alertify_log($msg)
{
	alertify.set({delay : 2000});
	alertify.log($msg);
}
function alertify_error($msg)
{
	alertify.set({delay : 2000});
	alertify.error($msg);
}
function alertify_success($msg)
{
	alertify.set({delay : 2000});
	alertify.success($msg);
}

function alertify_msg($msg)
{
	alertify.set({ labels: {
		ok     : "確定"
	} });
	alertify.alert($msg, function (e){
		// do nothing
	});
}

/*       
// 設定href, src, action的路徑及參數
$("[data-src]").each(function()
{
	arr = $(this).data("src").split("::");
	$(this).prop(arr[0], eval(arr[1])+arr[2]);
});
*/
// global var(共用陣列與變數)
var week_name = Array("週日", "週一", "週二", "週三", "週四", "週五", "週六");
var park_type = Array("", "一般", "非假日", "假日");
  
// -- 顯示指定項目 --
function show_item(tags, type)
{              
    // client.disconnect();   
    
    // -- 新增月租資料, 設定初始值 --             
    if (type == "member_add")
    {   
    	$("#member_list").html("");	// 清空member_list                      
		$("#member_list_all").html("");	// 清空member_list_all                      
    	$("#ma_station_no").val("");
		$("#ma_lpr").val("").prop("readonly",false);			// 2017-02-13 updated
    	$("#ma_old_lpr").val("").prop("readonly",false);		// 2017-02-13 updated
    	$("#ma_etag").val("");       
        $("#ma_demonth_start_date").val("").prop("disabled",false);
		
		// 禁止直接輸入到期日
        $("#ma_demonth_end_date").val("").prop("disabled",true);
        $("#ma_start_date").val("").prop("disabled",true);
        $("#ma_end_date").val("").prop("disabled",true);
		
		$("#ma_demonth_start_date_done").val("");
        $("#ma_demonth_end_date_done").val("");
        $("#ma_start_date_done").val("");
		$("#ma_end_date_done").val("");
		//$("#ma_fee_period1_done").val("");
        //$("#ma_fee_period_done").val("");
        //$("#ma_member_attr_done").val("");
		
		$("#ma_refund_transfer_id").val("0");				// 2017-02-24 updated
		$("#ma_refund_transfer_discount").val("0");			// 2017-02-24 updated
		
        $("#ma_member_name").val("");
        $("#ma_member_id").val("");
        $("#ma_mobile_no").val("");
        $("#ma_deposit").val("").prop("readonly",true);
        $("#ma_amt1").val("").prop("readonly",true);
        $("#ma_amt").val("").prop("readonly",true);
    	$("#ma_member_no").val("0");
    	$("#ma_member_company_no").val("");
    	$("#member_data_type").html("新增會員資料");
		
    	// 設定以上三個item的第一個項目為選項預設值
    	$("#member_attr")[0].selectedIndex = 0; 
    	$("#fee_period1")[0].selectedIndex = 0; 
    	$("#fee_period")[0].selectedIndex = 0; 
		
		$("#member_attr").prop("disabled",false);
		$("#fee_period1").prop("disabled",false);
		$("#fee_period").prop("disabled",false);
		$('input:checkbox.pt_id_checkbox').prop("disabled",false);
    }
	else if (type == "member_switch")
    {   
		// 繳期修改
    	$("#member_attr").prop("disabled",false);
		$("#fee_period1").prop("disabled",true);
		$("#fee_period").prop("disabled",false);
		$('input:checkbox.pt_id_checkbox').prop("disabled",false);
	}
	else
	{	
		$("#member_attr").prop("disabled",true);
		$("#fee_period1").prop("disabled",true);
		$("#fee_period").prop("disabled",true);
		$('input:checkbox.pt_id_checkbox').prop("disabled",true);
	}
	
	$("#amt_tot_view").hide();
	$("#amt_accrued_view").hide();
	//$("#ma_contract_no").hide();	
	$("#amt1_max_view").hide();
	$("#amt2_max_view").hide();
    
    switch(tags)
    {          
		// -- 費率總覽 --
    	case "price_plan_query_all":
			load_page(tags);
			reload_price_plan_query_all();  // reload
        	break;
	
		// -- 會員總覽 --
    	case "member_query_all":
        	load_page(tags);
			reload_member_query_all(); // reload
        	break;
		
		// -- 審核總覽 --		
		case "member_tx_check_query":
        	load_page(tags);
			reload_member_tx_check_query(); // reload
        	break;  
			
		// -- 臨停未結清單 --		
		case "cario_temp_not_finished_query_all":
			load_page(tags);
			reload_cario_temp_not_finished_query_all(); // reload
        	break;  
			
		// -- 退租交易清單 --		
		case "member_tx_refund_query":
        	load_page(tags);
			// 不用 reload
        	break; 
	
    	// -- 會員查詢, 並列出清單 --
    	case "member_query":
        	//$("#member_list").html("");	// -- 清除原內容 --
        	break;  
		
		// -- 交易查詢, 並列出清單 --		
		case "member_tx_query":
        	$("#member_tx_list").html("");	// -- 清除原內容 --
        	break;  
			
		// -- 發票查詢, 並列出清單 --		
		case "member_tx_bill_query":
        	$("#member_tx_bill_list").html("");	// -- 清除原內容 --
			
			if (type == "ok")
			{
				show_member_tx_bill(0, '0,1,99', '100', '0');	// 待開立 (未審核, 已審核, 審核未通過, 未開立發票)
			}
			else if (type == "more")
			{
				show_member_tx_bill(0, '', '1', '4');		// 待補開 (已退租, 待補開發票)
			}
			else if (type == "refund")
			{
				show_member_tx_bill(0, '', '2', '4');		// 待折讓 (已退租, 已開立發票)
			}
			
        	break;  
        
        // 停車時段設定        
        case "park_time":
            $.ajax
        	({
        		url: APP_URL+"park_time",
            	type: "post", 
            	dataType:"json",
            	data: {},
            	success: function(jdata)
            	{    
    	       		var str = "";
    	       		for(idx in jdata)
    	        	{                 
                    	pt_id = jdata[idx]['time_id'];                        
                	    str += "<tr id='pt_"+pt_id+"' data-type='"+jdata[idx]['park_type']+"' data-week_start='"+jdata[idx]['week_start']+"' data-week_end='"+jdata[idx]['week_end']+"'>";
                	    str += "<td id='pt_seqno_"+pt_id+"' style='text-align:center;'>"+jdata[idx]['seqno']+"</td>";
                	    str += "<td id='pt_time_id_"+pt_id+"' style='text-align:center;'>"+pt_id+"</td>";
                        /*
                	    str += "<td id='pt_park_type_"+pt_id+"' style='text-align:center;'>"+park_type[jdata[idx]['park_type']]+"</td>";
                	    str += "<td id='pt_week_start_"+pt_id+"' style='text-align:center;'>"+week_name[jdata[idx]['week_start']]+"</td>";
                	    str += "<td id='pt_week_end_"+pt_id+"' style='text-align:center;'>"+week_name[jdata[idx]['week_end']]+"</td>";
                	    str += "<td id='pt_daytime_start_"+pt_id+"' style='text-align:center;'>"+jdata[idx]['daytime_start']+"</td>";
                	    str += "<td id='pt_daytime_end_"+pt_id+"' style='text-align:center;'>"+jdata[idx]['daytime_end']+"</td>";
                        */
                	    str += "<td id='pt_remarks_"+pt_id+"' style='text-align:left;'>"+jdata[idx]['remarks']+"</td>";
                	    str += "<td style='text-align:center;'><select id='sel_park_time_"+pt_id+"' onclick='park_time_modify(\""+pt_id+"\");'><option value='choice'>請選擇</option><option value='modify'>修改</option><option value='delete'>刪除</option></select></td>";
    	        	   	str += "</tr>";	
    	        	}
    	       		//$("#park_time_list").html("").append(str);  
    	       		$("#park_time_list").html(str);  
                }
        	});
            break;             
        
		// -- 關帳 --	
		case "check_point":
			load_page(tags);
			reload_check_point();	// reload
			break;
		
		// -- 關帳查詢 --			
		case "check_point_report":
			load_page(tags);
			$("#check_point_form").submit(); // auto submit
			break;
			
		// -- 電子發票查詢 --			
		case "member_invoice_report":
			load_page(tags);
			$("#member_invoice_report_form").submit(); // auto submit
			break;
			
        case "invoice_open": 
        case "invoice_reprint": 
        case "invoice_cancel": 
        case "invoice_rows": 
        case "invoice_close": 
			load_page(tags);
            break;
            
        default: 
        	$("#"+tags+"_list").html("");	// -- 清除原內容 --
        	break;
    }
    
	$("[data-items]").hide();
	$("[data-items="+tags+"]").show();
    return false;
}

// 載入頁面
function load_page(tags)
{
	if ($("[data-items='"+tags+"']").length == 0)	// 第一次loading
    {     
		$.ajax
		({
			url:APP_URL+"get_html",
        			async:false,    
        			timeout:1500,
            		type:"post", 
            		dataType:"text",
            		data:{"tag_name":tags,"company_no":company_no},
            		success:function(jdata)
            		{
            	    	$("#page-wrapper").append(jdata);  
            	    }
		}); 
	}
}

// 更新未同步筆數顯示
function do_sync_batch()
{
	alertify_log('同步中。。');
	
	//if (!confirm("執行資料同步 ?"))	return false;
	$.ajax({
		url:APP_URL+"do_sync_batch_100",
		type:"post", 
		dataType:"json",
		data:{},
		success:function(jdata)
		{   
		}
	});
	
	// 一律先隱藏, 由同步判斷自行調整
	$("#un_synced_count_str").text('0');
	$("#un_synced_count_btn").hide();
}

// 更新未同步筆數顯示
function update_un_synced_count_view()
{
	$.ajax({
		url:APP_URL+"get_un_synced_count",
		type:"post", 
		dataType:"json",
		data:{},
		success:function(jdata)
		{       
			if(jdata['count'] > 0)
			{
				$("#un_synced_count_str").text('0').text(jdata['count']);
				$("#un_synced_count_btn").show();
				
				// 自動執行一次同步
				do_sync_batch();
			}
			else
			{
				$("#un_synced_count_str").text('0');
				$("#un_synced_count_btn").hide();	// 隱藏
			}
		}
	});
}

// 更新所有顯示畫面
function update_all_view()
{
	update_un_synced_count_view(); // 更新未同步筆數顯示
}
                           
// 停車時段修改或刪除選項 
function park_time_modify(park_time_id)
{
	select_item = $("#sel_park_time_"+park_time_id).val();
    switch(select_item)
    {
     	case "choice":	// -- 請選擇(忽略不處理) --
        	return false;
        
        case "modify": 
        	$("[name=time_id]").val($("#pt_time_id_"+park_time_id).text());
        	$("[name=seqno]").val($("#pt_seqno_"+park_time_id).text());
        
    		$("#park_time_dialog").modal
        	({
          		backdrop:false,
            	keyboard:false
        	});
        	break;
            
        case "delete":
			if (!confirm("確定刪除嗎 ?"))	return false;
                                   
			$.ajax
    		({     
        		url:APP_URL+"park_time_delete",
            	type:"post",
            	dataType:"text",  
            	data:{"time_id":park_time_id},
            	success:function(jdata)
            	{
                	if (jdata == "ok")
                    {
						alertify_msg("刪除成功 !");
						show_item("park_time", "");
                    }  
                }        
    		});        
        	break;
    }	
}  

// 查核作業
function member_tx_check(tx_no)
{
	alertify_msg("請通知總公司營管!<br/><br/>電話：02-27057716 分機 119<br/><br/>");    
	return false;
}

// 接續開立發票 (補開發票)
function next_refund_bill(tx_bill_no)
{
	if (!confirm("建立下一張發票 ?"))	return false;
	
	var station_no = $("#tx_bill_lpr_"+tx_bill_no).data("station_no");
	var member_no = $("#tx_bill_lpr_"+tx_bill_no).data("member_no");
	var remain_amt = $("#tx_bill_lpr_"+tx_bill_no).data("remain_amt");
	var tx_no = $("#tx_bill_lpr_"+tx_bill_no).data("tx_no");
	
	if(remain_amt > 0)
	{
		// 尚餘金額繼續開立
		xvars["rents"] = Array();             
		xvars["rents"]["tx_bill_no"] = tx_bill_no;
		xvars["rents"]["tx_no"] = tx_no;
		xvars["rents"]["station_no"] = station_no;
		xvars["rents"]["member_no"] = member_no;
		xvars["rents"]["remain_amt"] = parseInt(remain_amt);

		//console.log(xvars["rents"]);		
		$.ajax
			({
				url:APP_URL+"next_tx_bill",
				type:"post", 
				dataType:"text",
				data:{	"station_no":station_no, "tx_no":tx_no, 
						"tx_bill_no":tx_bill_no, "member_no":member_no, 
						"remain_amt":remain_amt
						},
				success:function(jdata)
				{       
					if (jdata == "ok")	
					{                              
						alertify_msg("建立完成 ! ");
						show_member_tx_bill(0, '', '1', '4');		// 待補開 (已退租, 待補開發票)
					}
					else if (jdata == "tx_error_not_found")	
					{                              
						alertify_msg("異常：查無開立資訊");
					}
					else if (jdata == "tx_error_not_ready")	
					{                              
						alertify_msg("操作錯誤：請先處理本期發票");
					}
					else if (jdata == "tx_error_next")	
					{                              
						alertify_msg("異常：查無待開立資訊");
					}
					else
					{
						alertify_msg("請於 " + jdata + "，再建立新的發票");
					} 
				}
			});
	}
	else
	{
		// 各期月租發票補印
		alertify_msg("流程異常：請通知總公司處理：" + tx_bill_no);
	}
}
/*
function refund_invoice_more(tx_bill_no)
{
	if (!confirm("確定補開發票 ?"))	return false;
	
	var station_no = $("#tx_bill_lpr_"+tx_bill_no).data("station_no");
	var member_no = $("#tx_bill_lpr_"+tx_bill_no).data("member_no");
	var invoice_amt = $("#tx_bill_lpr_"+tx_bill_no).data("invoice_amt");
	var tx_no = $("#tx_bill_lpr_"+tx_bill_no).data("tx_no");
	
	if(refund_amt > 0)
	{
		// 尚餘金額繼續開立
		xvars["rents"] = Array();             
		xvars["rents"]["tx_bill_no"] = tx_bill_no;
		xvars["rents"]["station_no"] = station_no;
		xvars["rents"]["member_no"] = member_no;
		xvars["rents"]["invoice_amt"] = parseInt(invoice_amt);

		$.ajax
			({
				url:APP_URL+"refund_invoice_more",
				type:"post", 
				dataType:"text",
				data:{	"station_no":station_no, "tx_no":tx_no, 
						"tx_bill_no":tx_bill_no, "member_no":member_no, 
						"refund_amt":refund_amt},
				success:function(jdata)
				{       
					if (jdata == "ok")	
					{                              
						alertify_msg("補開完成 ! ");
						show_member_tx_bill(0, '', '1', '4');		// 待補開 (已退租, 待補開發票)
					}
					else if (jdata == "tx_error_not_found")	
					{                              
						alertify_msg("異常：查無開立資訊");
					}
					else if (jdata == "tx_error_not_ready")	
					{                              
						alertify_msg("異常：查無發票資訊");
					}
					else
					{
						alertify_msg("異常：" + jdata);
					} 
				}
			});
	}
	else
	{
		// 各期月租發票補印
		alertify_msg("流程異常：請通知總公司處理：" + tx_bill_no);
	}
}
*/

// 折讓發票
function refund_invoice_allowance(tx_bill_no)
{
	if (!confirm("確定折讓發票 ?"))	return false;
	
	var station_no = $("#tx_bill_lpr_"+tx_bill_no).data("station_no");
	var member_no = $("#tx_bill_lpr_"+tx_bill_no).data("member_no");
	var refund_amt = $("#tx_bill_lpr_"+tx_bill_no).data("refund_amt");
	var tx_no = $("#tx_bill_lpr_"+tx_bill_no).data("tx_no");
	
	if(refund_amt > 0)
	{
		// 尚餘金額繼續開立
		xvars["rents"] = Array();             
		xvars["rents"]["tx_bill_no"] = tx_bill_no;
		xvars["rents"]["station_no"] = station_no;
		xvars["rents"]["member_no"] = member_no;
		xvars["rents"]["refund_amt"] = parseInt(refund_amt);

		$.ajax
			({
				url:APP_URL+"refund_invoice_allowance",
				type:"post", 
				dataType:"text",
				data:{	"station_no":station_no, "tx_no":tx_no, 
						"tx_bill_no":tx_bill_no, "member_no":member_no, 
						"refund_amt":refund_amt},
				success:function(jdata)
				{       
					if (jdata == "ok")	
					{                              
						alertify_msg("折讓完成 ! ");
						show_member_tx_bill(0, '', '2', '4');		// 待折讓 (已退租, 已開立發票)
					}
					else if (jdata == "tx_error_not_found")	
					{                              
						alertify_msg("異常：查無開立資訊");
					}
					else if (jdata == "tx_error_not_ready")	
					{                              
						alertify_msg("異常：查無發票資訊");
					}
					else
					{
						alertify_msg("異常：" + jdata);
					} 
				}
			});
	}
	else
	{
		// 各期月租發票補印
		alertify_msg("流程異常：請通知總公司處理：" + tx_bill_no);
	}
}

// 接續開立發票
function next_tx_bill(tx_bill_no)
{
	if (!confirm("建立下一張發票 ?"))	return false;
	
	var station_no = $("#tx_bill_lpr_"+tx_bill_no).data("station_no");
	var member_no = $("#tx_bill_lpr_"+tx_bill_no).data("member_no");
	var remain_amt = $("#tx_bill_lpr_"+tx_bill_no).data("remain_amt");
	var tx_no = $("#tx_bill_lpr_"+tx_bill_no).data("tx_no");
	var invoice_state = $("#tx_bill_lpr_"+tx_bill_no).data("invoice_state");
	
	if(remain_amt > 0)
	{
		// 尚餘金額繼續開立
		xvars["rents"] = Array();             
		xvars["rents"]["tx_bill_no"] = tx_bill_no;
		xvars["rents"]["tx_no"] = tx_no;
		xvars["rents"]["station_no"] = station_no;
		xvars["rents"]["member_no"] = member_no;
		xvars["rents"]["remain_amt"] = parseInt(remain_amt);

		//console.log(xvars["rents"]);		
		$.ajax
			({
				url:APP_URL+"next_tx_bill",
				type:"post", 
				dataType:"text",
				data:{	"station_no":station_no, "tx_no":tx_no, 
						"tx_bill_no":tx_bill_no, "member_no":member_no, 
						"remain_amt":remain_amt},
				success:function(jdata)
				{       
					if (jdata == "ok")	
					{                              
						alertify_msg("建立完成 ! ");
						
						if(invoice_state == 1)
						{
							show_member_tx_bill(0, '', '1', '4');	// 待補開 (已退租)
						}
						else if(invoice_state == 2)
						{
							show_member_tx_bill(0, '', '2', '4');	// 待折讓 (已退租, 已開立發票)
						}
						else
						{
							show_member_tx_bill(tx_no);
						}
					}
					else if (jdata == "tx_error_not_found")	
					{                              
						alertify_msg("異常：查無開立資訊");
					}
					else if (jdata == "tx_error_not_ready")	
					{                              
						alertify_msg("操作錯誤：請先處理本期發票");
					}
					else if (jdata == "tx_error_next")	
					{                              
						alertify_msg("異常：查無待開立資訊");
					}
					else
					{
						alertify_msg("請於 " + jdata + "，再建立新的發票");
					} 
				}
			});
	}
	else
	{
		// 各期月租發票補印
		alertify_msg("流程異常：請通知總公司處理：" + tx_bill_no);
	}
}

/*
// 待折讓 (已退租, 已開立發票)
function show_member_refund_allowance()
{
	show_member_tx_bill(0, '', '2', '4');
}

// 待補開 (已退租)
function show_member_refund_more_invoice()
{
	show_member_tx_bill(0, '', '1', '4');	
}
*/

// 發票開立記錄
function show_member_tx_bill(tx_no=0, verify_state_str='', invoice_state_str='', tx_state_str='', tx_bill_no =0, member_refund_id=0)
{	
	//show_item("member_tx_bill_query", "member_tx_bill_query"); 
	
	$.ajax
			({
				url:APP_URL+"member_tx_bill_query",
				type:"post", 
				dataType:"json",
				data:{"station_no":station_no, "tx_no":tx_no, "verify_state_str":verify_state_str, 
					"invoice_state_str":invoice_state_str, "tx_state_str":tx_state_str, 
					"tx_bill_no":tx_bill_no, "member_refund_id":member_refund_id},
				success:function(jdata)
				{    
					$("#member_tx_bill_list").html("");	// -- 清除原內容 --
					$("[data-items=member_tx_bill_query]").show();	// 顯示
					
					if(tx_no)
					{
						$("#member_tx_bill_query_tx_no_str_1").text('').text(tx_no);
					}
					else
					{
						$("#member_tx_bill_query_tx_no_str_1").text('未指定');
					}
				
					var member_list = [["<tr>"]];
					for(idx in jdata)
					{                    
						//console.log(jdata.length + " : " + idx + " , " + jdata[idx]['invoice_amt'] + " ： " + jdata[idx]['remain_amt']);				
						
						tx_no = jdata[idx]['tx_no'];   
						member_list = member_list.concat(["<td style='text-align:left;'>", jdata[idx]['tx_no'], "_", jdata[idx]['tx_bill_no'], "</td>"]);
						//member_list = member_list.concat(["<td style='text-align:left;'>", st[jdata[idx]['station_no']], "</td>"]);
						member_list = member_list.concat(["<td id='acc_date_", tx_no, "' style='text-align:center;'>", jdata[idx]['acc_date'], "</td>"]);
						member_list = member_list.concat(["<td id='tx_bill_lpr_", jdata[idx]['tx_bill_no'], 
							"' data-station_no='", jdata[idx]['station_no'], 
							"' data-member_no='", jdata[idx]['member_no'], 
							"' data-tx_bill_no='", jdata[idx]['tx_bill_no'], 
							"' data-tx_no='", jdata[idx]['tx_no'], 
							"' data-member_company_no='", jdata[idx]['member_company_no'], 
							"' data-company_no='", jdata[idx]['company_no'], 
							"' data-invoice_amt='", jdata[idx]['invoice_amt'], 
							"' data-remain_amt='", jdata[idx]['remain_amt'], 
							"' data-period_3_amt='", jdata[idx]['period_3_amt'], 
							"' data-amt='", jdata[idx]['amt'], 
							"' data-amt1='", jdata[idx]['amt1'], 
							"' data-deposit='", jdata[idx]['deposit'], 
							"' data-start_date_last='", jdata[idx]['start_date_last'], 
							"' data-end_date='", jdata[idx]['end_date'], 
							"' data-lpr='", jdata[idx]['lpr'], 
							"' data-fee_period='", jdata[idx]['fee_period'], 
							"' data-refund_amt='", jdata[idx]['refund_amt'], 
							"' data-invoice_state='", jdata[idx]['invoice_state'], 
							"' style='text-align:left;'>", jdata[idx]['lpr'], "</td>"]);

						member_list = member_list.concat(["<td id='sdate_last_", tx_no, "' style='text-align:center;'>", jdata[idx]['start_date_last'], "</td>"]);	
						member_list = member_list.concat(["<td id='fee_period_last_", tx_no, "' style='text-align:center;'>", period_name[jdata[idx]['fee_period_last']], "</td>"]);	
						member_list = member_list.concat(["<td id='edate_last_", tx_no, "' style='text-align:center;'>", jdata[idx]['end_date_last'], "</td>"]);	
						member_list = member_list.concat(["<td id='amt_last_", tx_no, "' style='text-align:center;'>", jdata[idx]['amt_last'], "</td>"]);	
						member_list = member_list.concat(["<td id='fee_period_", tx_no, "' style='text-align:center;'>", period_name[jdata[idx]['fee_period']], "</td>"]);	
						member_list = member_list.concat(["<td id='sdate_", tx_no, "' style='text-align:center;'>", jdata[idx]['start_date'], "</td>"]);	
						
						if(jdata[idx]['invoice_state'] == 1)
						{
							// 待補開
							member_list = member_list.concat(["<td id='edate_", tx_no, "' style='text-align:center;'>指定退租日<br/>", jdata[idx]['end_date'], "</td>"]);	
							member_list = member_list.concat(["<td id='amt_", tx_no, "' style='text-align:center;'>補繳總金額<br/>", jdata[idx]['amt'], " 元</td>"]);		
						}
						else if(jdata[idx]['invoice_state'] == 2)
						{
							// 待折讓
							member_list = member_list.concat(["<td id='edate_", tx_no, "' style='text-align:center;'>指定退租日<br/>", jdata[idx]['end_date'], "</td>"]);	
							member_list = member_list.concat(["<td id='amt_", tx_no, "' style='text-align:center;'>折讓總金額<br/>", jdata[idx]['amt'], " 元</td>"]);		
						}
						else
						{
							member_list = member_list.concat(["<td id='edate_", tx_no, "' style='text-align:center;'>", jdata[idx]['end_date'], "</td>"]);	
							member_list = member_list.concat(["<td id='amt_", tx_no, "' style='text-align:center;'>", jdata[idx]['amt'], " 元</td>"]);			
						}
						
						// 是否已有發票
						if(jdata[idx]['invoice_no'] > 0)
						{
							member_list = member_list.concat(["<td id='invoice_time_", tx_no, "' style='text-align:center;'>", jdata[idx]['invoice_time'], "</td>"]);
							member_list = member_list.concat(["<td id='member_company_no_", tx_no, "' style='text-align:center;'>", jdata[idx]['member_company_no'], "</td>"]);
							member_list = member_list.concat(["<td id='company_no_", tx_no, "' style='text-align:center;'>", jdata[idx]['company_no'], "</td>"]);
							member_list = member_list.concat(["<td id='invoice_amt_", tx_no, "' style='text-align:center;'>", jdata[idx]['invoice_amt'], "</td>"]);
							member_list = member_list.concat(["<td id='invoice_track_", tx_no, "' style='text-align:center;'>", jdata[idx]['invoice_track'], "</td>"]);
							member_list = member_list.concat(["<td id='invoice_no_", tx_no, "' style='text-align:center;'>", jdata[idx]['invoice_no'], "</td>"]);
							
							if(jdata[idx]['invoice_type'] == 0)
							{
								member_list = member_list.concat(["<td id='invoice_type_", tx_no, "' style='text-align:center;'>電子發票</td>"]);
							}
							else if(jdata[idx]['invoice_type'] == 1)
							{
								member_list = member_list.concat(["<td id='invoice_type_", tx_no, "' style='text-align:center;'>手開發票</td>"]);
							}
							else
							{
								member_list = member_list.concat(["<td id='invoice_type_", tx_no, "' style='text-align:center;'>異常</td>"]);
							}
						}
						else
						{
							member_list = member_list.concat(["<td id='invoice_time_", tx_no, "' style='text-align:center;'>未開立</td>"]);
							member_list = member_list.concat(["<td id='member_company_no_", tx_no, "' style='text-align:center;'>", jdata[idx]['member_company_no'], "</td>"]);
							
							//member_list = member_list.concat(["<td id='company_no_", tx_no, "' style='text-align:center;'>", jdata[idx]['company_no'], "</td>"]);
							
							if(jdata[idx]['tx_state'] == 4 || jdata[idx]['tx_state'] == 44)
							{
								// 已退租 或 取消, 原先交易將不再開放切換統編
								member_list = member_list.concat(["<td id='company_no_", tx_no, "' style='text-align:center;'>", jdata[idx]['company_no'], "</td>"]);							
								
								member_list = member_list.concat(["<td id='invoice_amt_", tx_no, "' style='text-align:center;'>", jdata[idx]['invoice_amt'], "</td>"]);
								member_list = member_list.concat(["<td id='invoice_track_", tx_no, "' style='text-align:center;'></td>"]);
								
								// 已退租 或 取消, 原先交易將不再開放開立
								member_list = member_list.concat(["<td id='invoice_no_", tx_no, "' style='text-align:center;'></td>"]);
								member_list = member_list.concat(["<td id='invoice_type_", tx_no, "' style='text-align:center;'></td>"]);
							}
							//else if(jdata[idx]['tx_state'] == 4 && jdata[idx]['invoice_state'] == 0)
							//{
							//	// 已退租, 原先交易將不再開放開立
							//	member_list = member_list.concat(["<td id='invoice_no_", tx_no, "' style='text-align:center;'></td>"]);
							//	member_list = member_list.concat(["<td id='invoice_type_", tx_no, "' style='text-align:center;'></td>"]);
							//}
							else
							{
								member_list = member_list.concat(["<td id='company_no_", tx_no, "' style='text-align:center;'><button class='btn btn-default' onclick='switch_company_no(",  jdata[idx]['tx_bill_no'] ,");'>", jdata[idx]['company_no'], "</button></td>"]);
								member_list = member_list.concat(["<td id='invoice_amt_", tx_no, "' style='text-align:center;'>", jdata[idx]['invoice_amt'], "</td>"]);
								member_list = member_list.concat(["<td id='invoice_track_", tx_no, "' style='text-align:center;'></td>"]);
								member_list = member_list.concat(["<td id='invoice_no_", tx_no, "' style='text-align:center;'><button class='btn btn-default' onclick='print_tx_invoice(",  jdata[idx]['tx_bill_no'] ,");'>電子發票</button></td>"]);
								member_list = member_list.concat(["<td id='invoice_type_", tx_no, "' style='text-align:center;'><button class='btn btn-default' onclick='hand_tx_invoice(",  jdata[idx]['tx_bill_no'] ,");'>手開發票</button></td>"]);	
							}
						}
						
						//member_list = member_list.concat(["<td style='color:blue;text-align:center;' id='remarks_", jdata[idx]['tx_bill_no'], "'>", jdata[idx]['remarks'], "</td>"]);	

						if(jdata[idx]['tx_state'] == 4)
						{
							member_list = member_list.concat(["<td style='color:black;text-align:center;'>已退租</td>"]);
						}						
						else if(jdata[idx]['tx_state'] == 44)
						{
							member_list = member_list.concat(["<td style='color:black;text-align:center;'>交易取消</td>"]);
						}
						else if(jdata[idx]['verify_state'] == 0)
						{
							member_list = member_list.concat(["<td style='color:red;text-align:center;'><button class='btn btn-default' style='color:red;' onclick='member_tx_check(",  tx_no + ");'>待審核</button></td>"]);
						}
						else if(jdata[idx]['verify_state'] == 1)
						{
							member_list = member_list.concat(["<td style='color:green;text-align:center;'>已審核</td>"]);
						}
						else
						{
							member_list = member_list.concat(["<td style='color:red;text-align:center;'><button class='btn btn-default' style='color:blue;' onclick='member_tx_check(",  tx_no + ");'>未通過</button></td>"]);
						}
						
						// 剩餘開立金額
						if(jdata[idx]['invoice_state'] == 1)
						{
							// 待開立
							if(jdata[idx]['remain_amt'] > 0)
							{
								member_list = member_list.concat(["<td style='text-align:center;'><button class='btn btn-default' onclick='next_refund_bill(",  jdata[idx]['tx_bill_no'] ,");'>尚餘 ", jdata[idx]['remain_amt'] ," 元</button></td>"]);			
							}
							else
							{
								member_list = member_list.concat(["<td style='text-align:center;'>無</td>"]);
							}	
						}
						else if(jdata[idx]['invoice_state'] == 2)
						{
							// 待折讓
							if(jdata[idx]['refund_amt'] > 0)
							{
								//member_list = member_list.concat(["<td style='text-align:center;'><button class='btn btn-default' onclick='refund_invoice_allowance(",  jdata[idx]['tx_bill_no'] ,");'>待折讓 ", jdata[idx]['refund_amt'] ," 元</button></td>"]);			
								member_list = member_list.concat(["<td style='text-align:center;'><button class='btn btn-default' onclick='member_tx_check(",  jdata[idx]['tx_bill_no'] ,");'>待折讓 ", jdata[idx]['refund_amt'] ," 元</button></td>"]);			
							}
							else
							{
								member_list = member_list.concat(["<td style='text-align:center;'>異常</td>"]);
							}
						}	
						else if(jdata[idx]['invoice_state'] == 92)
						{
							// 已折讓
							member_list = member_list.concat(["<td style='text-align:center;'>已折讓 ", jdata[idx]['refund_amt'] ," 元</td>"]);			
						}
						else
						{
							// 待開立
							if(jdata[idx]['remain_amt'] > 0)
							{
								if(jdata[idx]['tx_state'] == 4 && jdata[idx]['invoice_state'] == 0)
								{
									// 已退租, 原先交易將不再開放開立
									member_list = member_list.concat(["<td style='text-align:center;'>尚餘 ", jdata[idx]['remain_amt'] ," 元</td>"]);			
								}
								else
								{
									member_list = member_list.concat(["<td style='text-align:center;'><button class='btn btn-default' onclick='next_tx_bill(",  jdata[idx]['tx_bill_no'] ,");'>尚餘 ", jdata[idx]['remain_amt'] ," 元</button></td>"]);				
								}
								
							}
							else
							{
								member_list = member_list.concat(["<td style='text-align:center;'>無</td>"]);
							}	
						}
						
						member_list = member_list.concat(["</tr>"]);;	
					}
					$("#member_tx_bill_list").append(member_list.join('')); 
				}
			});	
}

// 顯示會員交易記錄
function show_member_tx(member_no)
{
	$.ajax
			({
				url:APP_URL+"member_tx_query",
				type:"post", 
				dataType:"json",
				data:{"station_no":station_no, "member_no":member_no},
				success:function(jdata)
				{       
					//show_item("member_tx_query", "member_tx_query"); 
					$("#member_tx_list").html("");					// -- 交易記錄，清除原內容 --
					$("[data-items=member_tx_query]").show();		// 顯示
					
					$("#member_tx_bill_list").html("");				// -- 發票記錄，清除原內容 --
					$("[data-items=member_tx_bill_query]").hide();	// 隱藏
					
					// 拿資料內容的車號當標題顯示
					if(jdata[0])
					{
						$("#member_tx_query_lpr_str_1").text(jdata[0]['lpr']);
						$("#member_tx_query_start_date_last_str_1").text(jdata[0]['start_date_last']);	
					}
					else
					{
						$("#member_tx_query_lpr_str_1").text('??');
						$("#member_tx_query_start_date_last_str_1").text('??');	
					}
					
					var member_list = ['<tr>'];
					for(idx in jdata)
					{                                         
						tx_no = jdata[idx]['tx_no'];   
						member_list = member_list.concat(["<td style='text-align:center;'>", tx_no, "</td>"]);
						//member_list = member_list.concat(["<td style='text-align:left;'>", st[jdata[idx]['station_no']], "</td>"]);
						member_list = member_list.concat(["<td id='acc_date_", tx_no, "' style='text-align:center;'>", jdata[idx]['acc_date'], "</td>"]);
						member_list = member_list.concat(["<td id='show_member_tx_lpr_", tx_no, 
							"' data-station_no='", jdata[idx]['station_no'], 
							"' data-member_no='", jdata[idx]['member_no'], 
							"' data-tx_no='", jdata[idx]['tx_no'], 
							"' data-member_company_no='", jdata[idx]['member_company_no'], 
							"' data-company_no='", jdata[idx]['company_no'], 
							"' data-amt='", jdata[idx]['amt'], 
							"' data-amt1='", jdata[idx]['amt1'], 
							"' data-deposit='", jdata[idx]['deposit'], 
							"' data-start_date_last='", jdata[idx]['start_date_last'], 
							"' data-end_date='", jdata[idx]['end_date'], 
							"' data-lpr='", jdata[idx]['lpr'], 
							"' data-fee_period='", jdata[idx]['fee_period'], 
							"' style='text-align:left;'>", jdata[idx]['lpr'], "</td>"]);

						member_list = member_list.concat(["<td id='sdate_last_", tx_no, "' style='text-align:center;'>", jdata[idx]['start_date_last'], "</td>"]);	
						member_list = member_list.concat(["<td id='fee_period_last_", tx_no, "' style='text-align:center;'>", period_name[jdata[idx]['fee_period_last']], "</td>"]);	
						member_list = member_list.concat(["<td id='edate_last_", tx_no, "' style='text-align:center;'>", jdata[idx]['end_date_last'], "</td>"]);	
						member_list = member_list.concat(["<td id='amt_last_", tx_no, "' style='text-align:center;'>", jdata[idx]['amt_last'], "</td>"]);	
						member_list = member_list.concat(["<td id='fee_period_", tx_no, "' style='text-align:center;'>", period_name[jdata[idx]['fee_period']], "</td>"]);	
						member_list = member_list.concat(["<td id='sdate_", tx_no, "' style='text-align:center;'>", jdata[idx]['start_date'], "</td>"]);	
						member_list = member_list.concat(["<td id='edate_", tx_no, "' style='text-align:center;'>", jdata[idx]['end_date'], "</td>"]);	
						member_list = member_list.concat(["<td id='amt_", tx_no, "' style='text-align:center;'>", jdata[idx]['amt'], "</td>"]);		
						member_list = member_list.concat(["<td style='text-align:center;'><button class='btn btn-default' onclick='show_member_tx_bill(",  tx_no ,");'>瀏覽</button></td>"]);

						if(jdata[idx]['tx_state'] == 4)
						{
							member_list = member_list.concat(["<td style='color:black;text-align:center;'>已退租</td>"]);
						}						
						else if(jdata[idx]['tx_state'] == 44)
						{
							member_list = member_list.concat(["<td style='color:black;text-align:center;'>交易取消</td>"]);
						}
						else if(jdata[idx]['verify_state'] == 0)
						{
							member_list = member_list.concat(["<td style='color:red;text-align:center;'><button class='btn btn-default' style='color:red;' onclick='member_tx_check(",  tx_no + ");'>待審核</button></td>"]);
						}
						else if(jdata[idx]['verify_state'] == 1)
						{
							member_list = member_list.concat(["<td style='color:green;text-align:center;'>已審核</td>"]);
						}
						else
						{
							member_list = member_list.concat(["<td style='color:red;text-align:center;'><button class='btn btn-default' style='color:blue;' onclick='member_tx_check(",  tx_no + ");'>未通過</button></td>"]);
						}
						
						member_list = member_list.concat(["<td style='color:red;text-align:center;' id='valid_time_", tx_no, "'>", jdata[idx]['valid_time'], "</td>"]);	
						member_list = member_list.concat(["<td style='color:blue;text-align:left;' id='remarks_", tx_no, "'>", jdata[idx]['remarks'], "</td>"]);	
						member_list = member_list.concat(["<td style='text-align:center, '><select id='member_tx_sel_",tx_no,"' onChange='member_tx_modify(", tx_no, "); '><option value='choice'>請選擇</option><option value='cancel'>交易取消</option></select></td>"]);
						member_list = member_list.concat(["</tr>"]);
					}
					$("#member_tx_list").append(member_list.join('')); 
				}
			});	
}

// 交易異動
function member_tx_modify(tx_no)
{
	select_item = $("#member_tx_sel_"+tx_no).val(); 
    $("#member_tx_sel_"+tx_no+" option[value='choice']").prop("selected", true);
    switch(select_item)
    {   
		case "cancel":
			if (!confirm("確定取消這筆交易 ? \n\n[注意事項]\n**若為剛建立之會員，且交易尚未審核\n**將直接刪除會員資料！！"))	return false;
			
			var member_no = $("#show_member_tx_lpr_"+tx_no).data("member_no");
			
			$.ajax
    		({     
        		url:APP_URL+"member_tx_cancel",
            	type:"post",
            	dataType:"text",
				data:
                {	
                	"station_no": $("#show_member_tx_lpr_"+tx_no).data("station_no"),
					"member_no": member_no,
					"tx_no": tx_no
                },
				error:function(xhr, ajaxOptions, thrownError)
				{
					alertify_msg(xhr.responseText);
					console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
				},
            	success:function(jdata)
            	{
					if (jdata == "ok")	
					{                              
						alertify_msg("交易已取消"); 
						
						show_member_tx(member_no);
					}
					else if (jdata == "tx_error_not_found")	
					{                              
						alertify_msg("查無資料"); 
					}
					else if (jdata == "member_not_found")	
					{                              
						alertify_msg("查無會員"); 
					}
					else if (jdata == "tx_error_refuse")	
					{                              
						alertify_msg("拒絕處理：請進行退租操作"); 
					}
					else if (jdata == "tx_error_not_last")	
					{                              
						alertify_msg("拒絕處理：請由最新一筆交易開始操作"); 
					}
					else
					{
						alertify_msg("未知的錯誤");
					}
				}
    		});	
		break;
		
		default:	// -- 其餘選擇(忽略不處理) --
        	return false;
	}
}

                           
// 修改或刪除選項 
function member_modify(member_no)
{
	select_item = $("#sel_"+member_no).val(); 
    $("#sel_"+member_no+" option[value='choice']").prop("selected", true);
    switch(select_item)
    {   
		// 交易記錄
		case "tx_logs":
			show_member_tx(member_no);
		break;
	
        // 繳租作業
        case "rents":
			// get data
			xvars["rents"] = Array();             
        	xvars["rents"]["station_no"] = $("#lpr_"+member_no).data("station_no");
        	xvars["rents"]["member_no"] = member_no;
			xvars["rents"]["member_attr"] = $("#lpr_"+member_no).data("member_attr");
        	xvars["rents"]["lpr"] = $("#lpr_"+member_no).text();
        	xvars["rents"]["fee_period"] = $("#lpr_"+member_no).data("fee_period");
        	xvars["rents"]["member_company_no"] = $("#lpr_"+member_no).data("member_company_no");
        	xvars["rents"]["company_no"] = $("#lpr_"+member_no).data("company_no");
        	xvars["rents"]["fee_period_last"] = $("#lpr_"+member_no).data("fee_period_last");
        	xvars["rents"]["amt_last"] = $("#lpr_"+member_no).data("amt");//$("#amt_"+member_no).text();
        	xvars["rents"]["end_date_last"] = $("#edate_"+member_no).text();
			
			// clean view
			$("#rents_name").text("");
			$("#rents_station_name").text("");
			$("#rents_end_date_last").text("");
			$("#rents_period").text("");
			$("#rents_company_no").val("");
			$("#rents_member_company_no").val("");
			$("#rents_amt").text("");
			$("#rents_end_date").text("");
			
			// set view
			$("#rents_name").text($("#lpr_"+member_no).data("member_name")+" ("+$("#lpr_"+member_no).data("lpr")+")");
        	$("#rents_station_name").text(st[xvars["rents"]["station_no"]]);
        	$("#rents_end_date_last").text($("#lpr_"+member_no).data("end_date"));					// 上期到期日
			$("#rents_period").text(period_name[xvars["rents"]["fee_period"]]);
			$("#rents_company_no").val(xvars["rents"]["company_no"]);						// 賣方統編
			$("#rents_member_company_no").val(xvars["rents"]["member_company_no"]); 		// 買方統編
			
			// 一律顯示作業框
			$("#rents_dialog").modal({backdrop:false,keyboard:false}); 
		
			// 取得目前租金費率
			$.ajax
    		({     
        		url:APP_URL+"get_rents_json",
            	type:"post",
            	dataType:"json",
				error:function(xhr, ajaxOptions, thrownError)
				{
					alertify_msg(xhr.responseText);
					console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
				},
            	success:function(jdata)
            	{
					//console.log('data:' + xvars["rents"]["fee_period"] + ', ' + xvars["rents"]["member_attr"]);
					xvars["rents"]["amt"] = jdata[xvars["rents"]["fee_period"]][xvars["rents"]["member_attr"]];
					
					if(xvars["rents"]["amt"] > 0)
						$("#rents_amt").text(xvars["rents"]["amt"]);							// 本期租金
					else
						$("#rents_amt").text("查無設定。。");									// 異常
				}
    		});	
		
			// 繳租作業小框
			$.ajax
    		({     
        		url:WEB_SERVICE,	// 計算下期截止日
            	type:"post",
            	dataType:"text",  
            	data:
                {	
                	"cmd":"last_date_next",
                	"last_date_curr":$("#lpr_"+member_no).data("end_date"),
                    "fee_period":$("#lpr_"+member_no).data("fee_period")
                },
				error:function(xhr, ajaxOptions, thrownError)
				{
					var error_msg = xhr.responseText ? xhr.responseText : "連線失敗, 請稍候再試";
					alertify_msg(error_msg);
					console.log("error:"+error_msg+"|"+ajaxOptions+"|"+thrownError);  
				},
            	success:function(jdata)
            	{                                                 
        			xvars["rents"]["end_date"] = jdata;
					if(xvars["rents"]["end_date"])
						$("#rents_end_date").text(xvars["rents"]["end_date"]);					// 本期到期日	
					else
						$("#rents_end_date").text("發生異常。。");								// 異常
					
                }    
    		});				
        	break; 
			
		// 手開：繳租作業
        case "hand_rents":
			$.ajax
    		({     
        		url:WEB_SERVICE,	// 計算下期截止日
            	type:"post",
            	dataType:"text",  
            	data:
                {	
                	"cmd":"last_date_next",
                	"last_date_curr":$("#lpr_"+member_no).data("end_date"),
                    "fee_period":$("#lpr_"+member_no).data("fee_period")
                },
				error:function(xhr, ajaxOptions, thrownError)
				{
					var error_msg = xhr.responseText ? xhr.responseText : "連線失敗, 請稍候再試";
					alertify_msg(error_msg);
					console.log("error:"+error_msg+"|"+ajaxOptions+"|"+thrownError);  
				},
            	success:function(jdata)
            	{                                                 
                	xvars["rents"] = Array();             
        			xvars["rents"]["station_no"] = $("#lpr_"+member_no).data("station_no");
        			xvars["rents"]["member_no"] = member_no;
        			xvars["rents"]["lpr"] = $("#lpr_"+member_no).data("lpr");
        			xvars["rents"]["fee_period"] = $("#lpr_"+member_no).data("fee_period");
        			xvars["rents"]["member_company_no"] = $("#lpr_"+member_no).data("member_company_no");
        			xvars["rents"]["company_no"] = $("#lpr_"+member_no).data("company_no");
        			xvars["rents"]["fee_period_last"] = $("#lpr_"+member_no).data("fee_period_last");
        			xvars["rents"]["amt_last"] = $("#lpr_"+member_no).data("amt");//$("#amt_"+member_no).text();
        			xvars["rents"]["amt"] = xvars["rents"]["amt_last"];
					xvars["rents"]["start_date"] = $("#lpr_"+member_no).data("start_date");
        			xvars["rents"]["end_date_last"] = $("#lpr_"+member_no).data("end_date");
        			xvars["rents"]["end_date"] = jdata;
                                                                                                         
        			$("#hand_rents_name").text($("#lpr_"+member_no).data("member_name")+" ("+$("#lpr_"+member_no).data("lpr")+")");
        			$("#hand_rents_station_name").text(st[xvars["rents"]["station_no"]]);
        			$("#hand_rents_end_date_last").text($("#lpr_"+member_no).data("end_date"));			// 上期到期日
					$("#hand_rents_end_date").text(xvars["rents"]["end_date"]);							// 本期到期日
        			$("#hand_rents_period").text(period_name[xvars["rents"]["fee_period"]]);
        			$("#hand_rents_amt").text(xvars["rents"]["amt"]);
					$("#hand_rents_amt_tot").text(xvars["rents"]["amt"]);
					$("#hand_rents_company_no").val(xvars["rents"]["company_no"]);					// 賣方統編
					
					if(xvars["rents"]["member_company_no"] == 0 || xvars["rents"]["member_company_no"] == '')
					{
						$("#hand_rents_member_company_no").val("");
					}
					else
					{
						$("#hand_rents_member_company_no").val(xvars["rents"]["member_company_no"]); // 買方統編
					}
                    
    				$("#hand_rents_dialog").modal({backdrop:false,keyboard:false}); 
                }        
    		});	
        	break; 
            
        // 修改作業
        case "modify": 
            $("#station_no_modify").val($("#lpr_"+member_no).data("station_no"));
            $("#ma_station_no").val($("#lpr_"+member_no).data("station_no"));
			
            //$("#ma_lpr").val($("#lpr_"+member_no).data("lpr"));
            //$("#ma_old_lpr").val($("#lpr_"+member_no).data("lpr"));
			$("#ma_lpr").val($("#lpr_"+member_no).data("lpr")).prop("readonly",true);		// 2017-02-13 updated
            $("#ma_old_lpr").val($("#lpr_"+member_no).data("lpr")).prop("readonly",true);	// 2017-02-13 updated
			
			$("#ma_etag").val($("#lpr_"+member_no).data("etag"));
            $("#ma_member_id").val($("#lpr_"+member_no).data("member_id"));
            $("#ma_member_company_no").val($("#lpr_"+member_no).data("member_company_no"));
            $("#ma_tel_o").val($("#lpr_"+member_no).data("tel_o"));
            $("#ma_tel_h").val($("#lpr_"+member_no).data("tel_h"));
            $("#ma_addr").val($("#lpr_"+member_no).data("addr"));
            $("#ma_member_name").val($("#lpr_"+member_no).data("member_name"));      

			// 取得目前租金費率
			$("#ma_amt").val('?????').prop("readonly",true);
			$.ajax
    		({     
        		url:APP_URL+"get_rents_json",
            	type:"post",
            	dataType:"json",
				error:function(xhr, ajaxOptions, thrownError)
				{
					alertify_msg(xhr.responseText);
					console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
				},
            	success:function(jdata)
            	{
					$current_price_plan_amt = jdata[$("#lpr_"+member_no).data("fee_period")][$("#lpr_"+member_no).data("member_attr")];
					
					if($current_price_plan_amt > 0)
					{
						$("#ma_amt").val($current_price_plan_amt).prop("readonly",true);
					}
				}
    		});	
			
			
			if(	$("#lpr_"+member_no).data("demonth_start_date") == null &&
				$("#lpr_"+member_no).data("demonth_end_date") == null &&
				$("#lpr_"+member_no).data("rent_start_date") == null)
			{
				$("#ma_demonth_start_date").val($("#lpr_"+member_no).data("start_date")).prop("disabled",true);  // 只有起訖時間的情況
			}
			else
			{
				$("#ma_demonth_start_date").val($("#lpr_"+member_no).data("demonth_start_date")).prop("disabled",true);	
			}
			$("#ma_demonth_end_date").val($("#lpr_"+member_no).data("demonth_end_date")).prop("disabled",true); 
			$("#ma_start_date").val($("#lpr_"+member_no).data("rent_start_date")).prop("disabled",true);
            $("#ma_end_date").val($("#lpr_"+member_no).data("end_date")).prop("disabled",true);
			
			$("#ma_demonth_start_date_done").val($("#lpr_"+member_no).data("demonth_start_date"));  
            $("#ma_demonth_end_date_done").val($("#lpr_"+member_no).data("demonth_end_date")); 
            $("#ma_start_date_done").val($("#lpr_"+member_no).data("rent_start_date"));
            $("#ma_end_date_done").val($("#lpr_"+member_no).data("end_date"));
			//$("#ma_fee_period1_done").val($("#lpr_"+member_no).data("fee_period1"));
			//$("#ma_fee_period_done").val($("#lpr_"+member_no).data("fee_period"));
			//$("#ma_member_attr_done").val($("#lpr_"+member_no).data("member_attr"));
			
            $("#ma_member_name").val($("#lpr_"+member_no).data("member_name"));
            $("#ma_mobile_no").val($("#lpr_"+member_no).data("mobile_no"));
            $("#ma_contract_no").val($("#lpr_"+member_no).data("contract_no"));
            $("#ma_deposit").val($("#lpr_"+member_no).data("deposit")).prop("readonly",true);
            $("#ma_amt1").val($("#lpr_"+member_no).data("amt1")).prop("readonly",true);
			//$("#ma_amt").val($("#amt_"+member_no).text()).prop("readonly",true);
			$("#ma_amt").val($("#lpr_"+member_no).data("amt")).prop("readonly",true);
            $("#ma_member_no").val(member_no);    
			
            $("#fee_period1 option[value='"+$("#lpr_"+member_no).data("fee_period1")+"']").prop("selected", "selected");
            $("#fee_period option[value='"+$("#lpr_"+member_no).data("fee_period")+"']").prop("selected", "selected");
            $("#member_attr option[value='"+$("#lpr_"+member_no).data("member_attr")+"']").prop("selected", "selected"); 
			
    		$("#member_data_type").html("修改會員資料</button>"); 
            $("input[id^=pt_id_]").prop("checked",false);	// 全部取消勾 
            arr = $("#lpr_"+member_no).data("pt_id").split(","); 
            for(idx in arr)
            {
            	$("#pt_id_"+arr[idx]).prop("checked",true);
            }                 
        	show_item("member_add", "member_modify"); 
        	break;
		
		case "switch":     
			alertify_msg("請通知總公司營管!<br/><br/>電話：02-27057716 分機 119<br/><br/>");    
        	break;   
		
		case "stop":    
			xvars["rents"] = Array();             
			xvars["rents"]["station_no"] = $("#lpr_"+member_no).data("station_no");
			xvars["rents"]["member_no"] = member_no;
			xvars["rents"]["member_attr"] = $("#lpr_"+member_no).data("member_attr");
			xvars["rents"]["lpr"] = $("#lpr_"+member_no).data("lpr");
			xvars["rents"]["fee_period"] = $("#lpr_"+member_no).data("fee_period");
			xvars["rents"]["member_company_no"] = $("#lpr_"+member_no).data("member_company_no");
			xvars["rents"]["company_no"] = $("#lpr_"+member_no).data("company_no");
			xvars["rents"]["fee_period_last"] = $("#lpr_"+member_no).data("fee_period_last");
			xvars["rents"]["amt_last"] = $("#lpr_"+member_no).data("amt");//$("#amt_"+member_no).text();
			xvars["rents"]["amt"] = xvars["rents"]["amt_last"];
			xvars["rents"]["end_date_last"] = $("#lpr_"+member_no).data("end_date");
																													 
			$("#stop_rents_name").text("").text($("#lpr_"+member_no).data("member_name")+" ("+$("#lpr_"+member_no).data("lpr")+")");
			$("#stop_rents_station_name").text("").text(st[xvars["rents"]["station_no"]]);
			
			/*
			console.log(
			'demonth_start_date: ' + $("#lpr_"+member_no).data("demonth_start_date") + 
			', demonth_end_date:' + $("#lpr_"+member_no).data("demonth_end_date") + 
			', rent_start_date:' + $("#lpr_"+member_no).data("rent_start_date") + 
			', fee_period:' + $("#lpr_"+member_no).data("fee_period") + ', fee_period1:' + $("#lpr_"+member_no).data("fee_period1") +
			', amt:' + $("#lpr_"+member_no).data("amt") + ', amt1:' + $("#lpr_"+member_no).data("amt1")
			);
			*/
			
			$("#stop_rents_tot_amt").text("");	// clean
			$("#stop_rents_end_date").val("");	// clean
			$("#stop_rents_desc").html("請指定 [退租日]");
			
			$("#stop_rents_end_date_last").text("").text($("#lpr_"+member_no).data("end_date"));					// 上期到期日
			$("#stop_rents_period").text("").text(period_name[xvars["rents"]["fee_period"]]);
			$("#stop_rents_member_attr").text("").text(mem_attr[xvars["rents"]["member_attr"]]);
			
			//$("#stop_rents_amt").text("").text(xvars["rents"]["amt"]);
								
			$("#stop_rents_dialog").modal({backdrop:false,keyboard:false}); 
        	break;   
            
		/*
        case "delete":
			if (!confirm("確定刪除嗎 ?"))	return false;
                                   
			$.ajax
    		({     
        		url:APP_URL+"member_delete",
            	type:"post",
            	dataType:"text",  
            	data:{"member_no":member_no, "station_no":station_no},
				error:function(xhr, ajaxOptions, thrownError)
				{
					alertify_msg(xhr.responseText);
					console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
				},
            	success:function(jdata)
            	{
                	if (jdata == "ok")
                    {
						alertify_msg("刪除成功 !");
						show_item("member_query", "member_query");
                    }
					else if (jdata == "delete_error")
					{
						alertify_msg("刪除失敗");
					}
					else
					{
						alertify_msg("處理失敗");
					}
                }        
    		});        
        	break;            
        */    
        default:	// -- 其餘選擇(忽略不處理) --
        	return false;
    }	
}  

// 切換賣方統編
function switch_company_no(tx_bill_no)
{
	var station_no = $("#tx_bill_lpr_"+tx_bill_no).data("station_no");
	var tx_no = $("#tx_bill_lpr_"+tx_bill_no).data("tx_no");
	var company_no = $("#tx_bill_lpr_"+tx_bill_no).data("company_no");
	var ok_msg = "切換賣方統編";
	
	alertify.set({ 
		buttonFocus: "cancel",
		labels: {
			cancel : "取消",
			ok     : ok_msg
		}
	});
	alertify.confirm("確定 " + ok_msg + "? ( 總公司 <-> 場站 )", function (e){
		if (e) {
			// 執行切換
			$.ajax
			({
				url:APP_URL+"switch_company_no", 
				dataType:"text",
				type:"post",
				data:
				{
					"station_no" :station_no,
					"tx_bill_no" :tx_bill_no,
					"company_no" :company_no
				},
				error:function(xhr, ajaxOptions, thrownError)
				{
					alertify_msg(xhr.responseText);
					console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
				},
				success:function(jdata)
				{                                                                            
					if (jdata == "ok")	
					{                              
						alertify_msg(ok_msg + "，完成 !"); 
						
						show_member_tx_bill(tx_no);	// reload
					}
					else if (jdata == "tx_error_not_found")	
					{
						alertify_msg("異常: 查無記錄。。");
					}
					else if (jdata == "invoice_exist")	
					{
						alertify_msg("異常: 已開立發票");
					}
					else if (jdata == "not_changed")	
					{
						alertify_msg("異常: 一樣的統編？");
					}
					else
					{
						alertify_msg("操作失敗。。" + jdata);
					}
				}
			});
			
		} else {
			alertify_log('操作已取消');
		}
	});
}

$(document).ready(function()   
{                             
	<?php /* validate  設定start */ ?>
	$.validate();
	<?php /* validate  設定end */ ?>
  
	$("#ma_company_no").val(company_no);	// 預設值為場站統編
    
    if (is_no_login())
    {
    	$("#login_dialog").modal
        ({
          	backdrop:false,
            keyboard:false
        });	 
    } 
	else
	{
		update_all_view(); // 更新畫面顯示
	}
    
    // 設定場站資訊 
    for(station_no in st)
    {
    	$(new Option(st[station_no],station_no)).appendTo('#station_select');  
    	//$(new Option(st[station_no],station_no)).appendTo('#station_no_modify');			// 會員修改場站編號  
		//$(new Option(st[station_no],station_no)).appendTo('#station_refund_select');  	// 會員退租場站編號  
    }    
	
	// 預先載入頁面
	load_page("member_add");
      
    /*
    // 設定以上三個item的第一個項目為選項預設值
    $("#member_attr")[0].selectedIndex = 0; 
    $("#fee_period1")[0].selectedIndex = 0; 
    $("#fee_period")[0].selectedIndex = 0; 
    */
    
	$(".cario_list").css({"vertical-align":"middle"});	// 進出場實況表,強制垂直置中
    
    // 帳密登入檢核      
	$("#login_form").submit(function(e)
	{                   
    	e.preventDefault();
        
    	if ($("[name=login_name]").val() == "" || $("[name=login_pswd]").val() == "")
        {
        	alertify_msg("帳號或密碼不可為空白");
            return false;
        } 
        
        $.ajax
        ({
        	url:APP_URL+"login_verify",
            type: "post", 
            dataType:"json",
            data: $(this).serialize(),
            success: function(jdata)
            {         
            	if (jdata["rcode"] == "OK")
                {              
                	xvars["ck"] = jdata["ck"];
                    $('#login_dialog').modal('hide');
					
					update_all_view(); // 更新畫面顯示
                }
                else
                {           
                	//xvars["ck"] = jdata["ck"];
                	//xvars["user_name"] = jdata["ck"];
                 	alertify_msg("帳號或密碼錯誤, 請重新輸入! ");
                }
            }
        }); 
    });
          
    // 會員查詢      
	$("#member_query_form").submit(function(e)
	{ 
      	e.preventDefault();
        
		if ($("#q_str").val() == "")
    	{
    	  	alertify_log("請填寫查詢關鍵字..");
    	    return false;
    	}
		
		$("#member_list").html("");
    	
    	$.ajax
    	({
    		url:APP_URL+"member_query",
    	    type:"post", 
    	    dataType:"json",
    	    data:{"station_no":$("#station_select").val(), "q_item":$("input:radio:checked[name=q_item]").val(), "q_str":$("#q_str").val()},
    	    success:function(jdata)
    	    {       
				show_item('member_query', 'member_query'); // reset view
			
    	       	var member_list = "";
    	       	for(idx in jdata)
    	        {                             
    	           	mno = jdata[idx]['member_no'];   
                    member_list += "<tr><td style='text-align:left;'>"+st[jdata[idx]['station_no']]+"</td>";
    	           	member_list += "<td id='lpr_"+mno+
						"' data-station_no='"+jdata[idx]['station_no']+
						"' data-member_no='"+mno+
						"' data-member_company_no='"+jdata[idx]['member_company_no']+
						//"' data-company_no='"+jdata[idx]['company_no']+
						"' data-company_no='"+ company_no +
						"' data-member_id='"+jdata[idx]['member_id']+
						"' data-etag='"+jdata[idx]['etag']+
						"' data-demonth_start_date='"+jdata[idx]['demonth_start_date']+
						"' data-demonth_end_date='"+jdata[idx]['demonth_end_date']+
						"' data-rent_start_date='"+jdata[idx]['rent_start_date']+
						"' data-member_attr='"+jdata[idx]['member_attr']+
						"' data-fee_period1='"+jdata[idx]['fee_period1']+
						"' data-fee_period='"+jdata[idx]['fee_period']+
						"' data-amt1='"+jdata[idx]['amt1']+
						"' data-amt='"+jdata[idx]['amt']+
						"' data-deposit='"+jdata[idx]['deposit']+
						"' data-tel_o='"+jdata[idx]['tel_o']+
						"' data-tel_h='"+jdata[idx]['tel_h']+
						"' data-addr='"+jdata[idx]['addr']+
						"' data-pt_id='"+jdata[idx]['park_time']+
						
						"' data-lpr='"+jdata[idx]['lpr']+
						"' data-member_name='"+jdata[idx]['member_name']+
						"' data-mobile_no='"+jdata[idx]['mobile_no']+
						"' data-contract_no='"+jdata[idx]['contract_no']+
						
						"' data-start_date='"+jdata[idx]['start_date']+
						"' data-end_date='"+jdata[idx]['end_date']+
						"' style='text-align:left;'>"+jdata[idx]['lpr']+"</td>";
                    member_list += "<td id='name_"+mno+"' style='text-align:left;'>"+jdata[idx]['member_name']+"</td>";	
    	           	member_list += "<td id='mobile_"+mno+"' style='text-align:center;'>"+jdata[idx]['mobile_no']+"</td>";	
    	           	member_list += "<td id='sdate_"+mno+"' style='text-align:center;'>"+jdata[idx]['start_date']+"</td>";	
    	           	member_list += "<td id='edate_"+mno+"' style='text-align:center;'>"+jdata[idx]['end_date']+"</td>";	
                    member_list += "<td id='fee_period_"+mno+"' style='text-align:center;'>"+period_name[jdata[idx]['fee_period']]+"</td>";	
					member_list += "<td id='member_attr_"+mno+"' style='text-align:center;'>"+mem_attr[jdata[idx]['member_attr']]+"</td>";	
    	           	member_list += "<td id='contract_"+mno+"' style='text-align:center;'>"+jdata[idx]['contract_no']+"</td>";	    
    	           	//member_list += "<td id='amt_"+mno+"' style='text-align:center;'>"+jdata[idx]['amt']+"</td>";	
					
					if(jdata[idx]['suspended'] == "1")
					{
						member_list += "<td style='text-align:center;'><input type='checkbox' checked id='suspended_"+mno+"' disabled/></td>";	
						member_list += "<td style='text-align:center;'><select id='sel_"+mno+"' onclick='member_modify("+mno+");'><option value='choice'>請選擇</option><option value='tx_logs'>交易記錄</option><option value='rents'>繳租</option></select></td>";		
					}
					else
					{
						member_list += "<td style='text-align:center;'><input type='checkbox' id='suspended_"+mno+"' disabled/></td>";	
						member_list += "<td style='text-align:center;'><select id='sel_"+mno+"' onclick='member_modify("+mno+");'><option value='choice'>請選擇</option><option value='tx_logs'>交易記錄</option><option value='rents'>繳租</option><option value='modify'>一般修改</option><option value='switch'>繳期修改</option><option value='stop'>退租</option></select></td>";		
					}
    	           	member_list += "</tr>";	
    	        }
    	       	$("#member_list").html(member_list);
    	    }
    	});
    }); 
	
	// 定時自動更新頁面
	(function autoReloadPage(){
		var pageReloadTimeMillis = 600000;			// 頁面, 自動重新載入週期 ( 10 min )
		var pageCheckReloadTimeMillis = 10000;		// 頁面, 判斷重新載入週期 ( 10 sec )
		var pageShowReloadTimeMillis = 50000;		// 頁面, 開始顯示倒數週期 ( 50 sec )
		var aliveTime = moment();
		var countdownTimeMillis = pageReloadTimeMillis;
		$(document.body).bind("mousemove keypress", function(e) {
			aliveTime = moment();
			countdownTimeMillis = pageReloadTimeMillis;
		});
		function refresh() {
			if(moment() - aliveTime >= pageReloadTimeMillis) // 如果頁面沒動作, 才更新
				window.location.reload(true);
			else{
				countdownTimeMillis -= pageCheckReloadTimeMillis;
				if(countdownTimeMillis < pageCheckReloadTimeMillis)
				{
					alertify_count_down("重新載入中..請稍候..", pageCheckReloadTimeMillis);	
				}
				else if(countdownTimeMillis < pageShowReloadTimeMillis){
					alertify_count_down("倒數: " + (countdownTimeMillis / 1000) + " 秒, 重新載入畫面..", pageCheckReloadTimeMillis);	
				}
				else
				{
					// 每 10 sec
					if (is_no_login())
					{
						// 沒登入
					}
					else if(Math.floor((Math.random() * 5)) == 0)
					{
						update_all_view(); // 更新畫面顯示
					}
				}
				setTimeout(refresh, pageCheckReloadTimeMillis);
			}
		}
		setTimeout(refresh, pageCheckReloadTimeMillis);
	})();
	
});

// 是否未登入
function is_no_login()
{
	return (xvars["ck"] == "NOLOGIN");
}

// 會員時段表
function member_park_time()
{
	$("#pt_dialog").modal
    ({
    	backdrop:false,
        keyboard:false
    }); 
}   
  
// 取得發票開立說明
function get_invoice_desc(amt, amt1, invoice_amt, remain_amt, period_3_amt)
{
	var thisAmt = parseInt(invoice_amt) - parseInt(amt1);
	var result;
	
	if(amt1 > 0)
	{
		result = ["首期 " , amt1 , " 元 + 本期 " , thisAmt , " 元 <br/><br/>*發票金額：" , invoice_amt , " 元"];	
	}
	else
	{
		result = ["本期 " , thisAmt , " 元 <br/><br/>*發票金額：" , invoice_amt , " 元"];	
	}
	
	if(remain_amt > 0)
	{
		result = result.concat("<br/>*尚餘：" , remain_amt , " 元，另外開立發票");	
	}
	
	return result.join('');
}

</script>




<!-- ----- [H.1] 手開：首期繳租作業小框 ----- -->

<!-- ----- ID: 		hand_first_rents_dialog ----- -->
<!-- ----- action: 	do_hand_first_rents_payment() ----- -->

<div class="modal fade" id="hand_first_rents_dialog">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header"><h3>手開發票</h3></div>
<div class="modal-body">
<form id="hand_first_rents_form" class="center-block">    
<div class="main">
<div class="dataTable_wrapper">
<table class="table table-striped table-bordered table-hover" style="font-size:12px;">
<tbody id="hand_first_rents_list">
<tr>
<td style="text-align:right;">姓名</td>
<td style="text-align:left;" id="hand_first_rents_name"></td>
</tr>  
<tr>
<td style="text-align:right;">場站</td>
<td style="text-align:left;" id="hand_first_rents_station_name"></td>
</tr>

<!--tr>
<td style="text-align:right;vertical-align:middle">賣方統編</td>
<td style="text-align:left;"><input type="text" id="hand_first_rents_company_no" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">賣方統編</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="hand_first_rents_company_no" class="form-control" placeholder="賣方統編號碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確統編<br/>例如：80682490"
												data-validation-error-msg-container="#company_no_error_msg_hand_first_rents_company_no"
												readonly/>
												<span id="company_no_error_msg_hand_first_rents_company_no"></span>
                                            </td>
                                        </tr>

<tr>
<td style="text-align:right;">開始日</td>
<td style="text-align:left;" id="hand_first_rents_start_date"></td>
</tr>   
<tr>
<td style="text-align:right;">到期日</td>
<td style="text-align:left;" id="hand_first_rents_end_date"></td>
</tr>   
<tr>
<td style="text-align:right;">押金</td>
<td style="text-align:left;" id="hand_first_rents_deposit"></td>
</tr>   
<tr>
<td style="text-align:right;">首期租金</td>
<td style="text-align:left;" id="hand_first_rents_amt1"></td>
</tr>   
<tr>
<td style="text-align:right;">例行繳期</td>
<td style="text-align:left;" id="hand_first_rents_period"></td>
</tr>   
<tr>
<td style="text-align:right;">例行租金</td>
<td style="text-align:left;" id="hand_first_rents_amt"></td>
</tr> 
<tr>

<!--tr>
<td style="text-align:right;vertical-align:middle">買方統編</td>
<td style="text-align:left;"><input type="text" id="hand_first_rents_member_company_no" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">買方統編</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="hand_first_rents_member_company_no" class="form-control" placeholder="買方統編號碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確統編<br/>例如：80682490"
												data-validation-error-msg-container="#company_no_error_msg_hand_first_rents_member_company_no"
												/>
												<span id="company_no_error_msg_hand_first_rents_member_company_no"></span>
                                            </td>
                                        </tr>

<tr>
<td style="text-align:right;">發票金額</td>
<td style="text-align:left;color:blue;" id="hand_first_rents_invoice_amt"></td>
</tr>
<tr>
<td style="text-align:right;">開立說明</td>
<td style="text-align:left;color:blue;" id="hand_first_rents_remain_desc"></td>
</tr>   

<!--tr>
<td style="text-align:right;vertical-align:middle">發票字軌</td>
<td style="text-align:left;"><input type="text" id="hand_first_rents_invoice_track" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">發票字軌</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="text" id="hand_first_rents_invoice_track" class="form-control" placeholder="請填入發票字軌2碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{2}$)([A-Z]+)$"
												data-validation-error-msg="請輸入正確發票字軌<br/>例如：AB"
												data-validation-error-msg-container="#mobile_error_msg_hand_first_rents_invoice_track"
												/>
												<span id="mobile_error_msg_hand_first_rents_invoice_track"></span>
                                            </td>
                                        </tr>

<!--tr>
<td style="text-align:right;vertical-align:middle">發票號碼</td>
<td style="text-align:left;"><input type="text" id="hand_first_rents_invoice_no" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">發票號碼</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="hand_first_rents_invoice_no" class="form-control" placeholder="請填入發票數字8碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確發票號碼<br/>例如：12345678"
												data-validation-error-msg-container="#mobile_error_msg_hand_first_rents_invoice_no"
												/>
												<span id="mobile_error_msg_hand_first_rents_invoice_no"></span>
                                            </td>
                                        </tr>

</tbody>
</table>                    
<button type="button" class="btn btn-large btn-success pull-left" onclick="do_hand_first_rents_payment();">確認送出</button>
&nbsp;&nbsp;
<button type="button" class="btn btn-large btn-cancel" onclick="$('#hand_first_rents_dialog').modal('hide');">取消</button>
</div><!-- ----- end of dataTable_wrapper ----- -->  
</div><!-- ----- end of main ----- -->
</form>
</div><!-- end of modal-body --> 
</div><!-- end of modal-content --> 
</div><!-- end of modal-dialog -->
</div><!-- end of modal show -->
<!-- ----- 手開：首期繳租作業小框(結束) ----- -->   

<script>  
// 手開：首期月租發票
function do_hand_first_rents_payment()
{
	if($("#hand_first_rents_company_no").val() == '0' || $("#hand_first_rents_company_no").val() == '' || $("#hand_first_rents_company_no").val().length != 8)
	{
		alertify_error('賣方統編有誤，請修正');
		return false;
	}
	
	if($("#hand_first_rents_member_company_no").val() != '' && $("#hand_first_rents_member_company_no").val().length != 8)
	{
		alertify_error('買方統編有誤，請修正');
		return false;
	}
	
	if($("#hand_first_rents_invoice_track").val() == '0' || $("#hand_first_rents_invoice_track").val() == '' || $("#hand_first_rents_invoice_track").val().length != 2)
	{
		alertify_error('發票字軌有誤，請修正');
		return false;
	}
	
	if($("#hand_first_rents_invoice_no").val() == '0' || $("#hand_first_rents_invoice_no").val() == '' || $("#hand_first_rents_invoice_no").val().length != 8)
	{
		alertify_error('發票號碼有誤，請修正');
		return false;
	}
	
	if (!confirm("確認資料無誤並送出 ?")) return false; 
	
	var invoice_state = xvars["rents"]["invoice_state"];
	var tx_bill_no = xvars["rents"]["tx_bill_no"];
	var tx_no = xvars["rents"]["tx_no"];
	var member_no = xvars["rents"]["member_no"];
	var company_no = $("#hand_first_rents_company_no").val();
	var member_company_no = $("#hand_first_rents_member_company_no").val();
	if (member_company_no == "")	member_company_no = "0";	// 建立二聯式發票
	
	// 新增月租付款交易
	$.ajax
    ({
        url:APP_URL+"hand_first_rents_payment", 
        dataType:"text",
        type:"post",
        data:
        {
			"tx_bill_no": tx_bill_no,
			"tx_no": tx_no,
        	"station_no":xvars["rents"]["station_no"],
        	"member_no":member_no,  
        	"member_company_no":member_company_no,
        	"company_no":company_no,
        	"amt":xvars["rents"]["amt"],
			"amt1":xvars["rents"]["amt1"],
			"invoice_amt": xvars["rents"]["invoice_amt"],
			"invoice_track":$("#hand_first_rents_invoice_track").val(),
			"invoice_no":$("#hand_first_rents_invoice_no").val()
        },
		error:function(xhr, ajaxOptions, thrownError)
        {
			alertify_msg(xhr.responseText);
        	console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
        },
        success:function(jdata)
        {                                                                            
			if (jdata == "ok")	
            {                              
            	alertify_msg("完成 ! "); 
				
				if(invoice_state == 1)
				{
					show_member_tx_bill(0, '', '1', '4');	// 待補開 (已退租)
				}
				else if(invoice_state == 2)
				{
					show_member_tx_bill(0, '', '2', '4');	// 待折讓 (已退租, 已開立發票)
				}
				else
				{
					show_member_tx_bill(tx_no);
				}
            }
            else
            {
              	//alertify_msg("操作失敗！");
				alertify_msg("操作失敗。。" + jdata);
            }
        }
    });
    
    delete xvars["rents"];
    $('#hand_first_rents_dialog').modal('hide'); 
}

// 手開發票
function hand_tx_invoice(tx_bill_no)
{
	var station_no = $("#tx_bill_lpr_"+tx_bill_no).data("station_no");
	var member_no = $("#tx_bill_lpr_"+tx_bill_no).data("member_no");
	var member_name = $("#tx_bill_lpr_"+tx_bill_no).data("member_name");
	var member_company_no = $("#tx_bill_lpr_"+tx_bill_no).data("member_company_no");
	var company_no = $("#tx_bill_lpr_"+tx_bill_no).data("company_no");
	var invoice_amt = $("#tx_bill_lpr_"+tx_bill_no).data("invoice_amt");
	var remain_amt = $("#tx_bill_lpr_"+tx_bill_no).data("remain_amt");
	var period_3_amt = $("#tx_bill_lpr_"+tx_bill_no).data("period_3_amt");
	var amt = $("#tx_bill_lpr_"+tx_bill_no).data("amt");
	var amt1 = $("#tx_bill_lpr_"+tx_bill_no).data("amt1");
	var deposit = $("#tx_bill_lpr_"+tx_bill_no).data("deposit");
	var start_date_last = $("#tx_bill_lpr_"+tx_bill_no).data("start_date_last");
	var end_date = $("#tx_bill_lpr_"+tx_bill_no).data("end_date");
	var lpr = $("#tx_bill_lpr_"+tx_bill_no).data("lpr");
	var fee_period = $("#tx_bill_lpr_"+tx_bill_no).data("fee_period");
	var fee_period_last = $("#tx_bill_lpr_"+tx_bill_no).data("fee_period_last");
	var tx_no = $("#tx_bill_lpr_"+tx_bill_no).data("tx_no");
	var invoice_state = $("#tx_bill_lpr_"+tx_bill_no).data("invoice_state");
	
	if(invoice_amt > 0)
	{
		// 首期月租付款並建立電子發票
		xvars["rents"] = Array();       
		xvars["rents"]["tx_bill_no"] = tx_bill_no;		
		xvars["rents"]["tx_no"] = tx_no;
		xvars["rents"]["station_no"] = station_no;
		xvars["rents"]["member_no"] = member_no;
		xvars["rents"]["member_company_no"] = member_company_no;
		xvars["rents"]["company_no"] = company_no;
		xvars["rents"]["fee_period"] = fee_period;
		xvars["rents"]["amt"] = parseInt(amt);
		xvars["rents"]["amt1"] = parseInt(amt1);
		xvars["rents"]["invoice_amt"] = parseInt(invoice_amt);
		xvars["rents"]["remain_amt"] = parseInt(remain_amt);
		xvars["rents"]["period_3_amt"] = parseInt(period_3_amt);
		xvars["rents"]["invoice_state"] = invoice_state;

		$("#hand_first_rents_name").text( "車號 " + lpr +"");
		$("#hand_first_rents_station_name").text(st[xvars["rents"]["station_no"]]);
		$("#hand_first_rents_start_date").text(start_date_last);						// 上期開始日
		$("#hand_first_rents_end_date").text(end_date);									// 本期結束日
		$("#hand_first_rents_deposit").text(deposit);
		$("#hand_first_rents_period").text(period_name[xvars["rents"]["fee_period"]]);
		$("#hand_first_rents_amt").text(xvars["rents"]["amt"]);
		$("#hand_first_rents_amt1").text(xvars["rents"]["amt1"]);
		$("#hand_first_rents_company_no").val(xvars["rents"]["company_no"]); // 賣方統編
		
		$("#hand_first_rents_invoice_amt").text(xvars["rents"]["invoice_amt"]);
		$("#hand_first_rents_remain_desc").html(get_invoice_desc(xvars["rents"]["amt"], xvars["rents"]["amt1"], xvars["rents"]["invoice_amt"], xvars["rents"]["remain_amt"], xvars["rents"]["period_3_amt"]));	// 發票說明

		if(xvars["rents"]["member_company_no"] == 0 || xvars["rents"]["member_company_no"] == '')
		{
			$("#hand_first_rents_member_company_no").val("");
		}
		else
		{
			$("#hand_first_rents_member_company_no").val(xvars["rents"]["member_company_no"]); // 買方統編
		}
		
		$("#hand_first_rents_dialog").modal({backdrop:false,keyboard:false});
	}
	else
	{
		// 各期月租發票補印
		alertify_msg("流程異常：請通知總公司處理：" + tx_bill_no);
	}	
}
</script>








<!-- ----- [H.2] 手開：繳租作業小框 ----- -->

<!-- ----- ID: 		hand_rents_dialog ----- -->
<!-- ----- action: 	do_hand_rents_payment() ----- -->

<div class="modal fade" id="hand_rents_dialog">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header"><h3>手開發票</h3></div>
<div class="modal-body">
<form id="hand_rents_form" class="center-block">    
<div class="main">
<div class="dataTable_wrapper">
<table class="table table-striped table-bordered table-hover" style="font-size:12px;">
<tbody id="hand_rents_list">
<tr>
<td style="text-align:right;">姓名</td>
<td style="text-align:left;" id="hand_rents_name"></td>
</tr>  
<tr>
<td style="text-align:right;">場站</td>
<td style="text-align:left;" id="hand_rents_station_name"></td>
</tr>

<!--tr>
<td style="text-align:right;vertical-align:middle">賣方統編</td>
<td style="text-align:left;"><input type="text" id="hand_rents_company_no" class="form-control" style="width:100px !important;" /></td>
</tr-->
										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">賣方統編</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="hand_rents_company_no" class="form-control" placeholder="賣方統編號碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確統編<br/>例如：80682490"
												data-validation-error-msg-container="#company_no_error_msg_hand_rents_company_no"
												/>
												<span id="company_no_error_msg_hand_rents_company_no"></span>
                                            </td>
                                        </tr>

<tr>
<td style="text-align:right;">上期到期日</td>
<td style="text-align:left;" id="hand_rents_end_date_last"></td>
</tr>
<tr>
<td style="text-align:right;">本期到期日</td>
<td style="text-align:left;" id="hand_rents_end_date"></td>
</tr>   
<tr>
<td style="text-align:right;">繳期</td>
<td style="text-align:left;" id="hand_rents_period"></td>
</tr>   
<tr>
<td style="text-align:right;">租金</td>
<td style="text-align:left;" id="hand_rents_amt"></td>
</tr>   

<!--tr>
<td style="text-align:right;vertical-align:middle">買方統編</td>
<td style="text-align:left;"><input type="text" id="hand_rents_member_company_no" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">買方統編</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="hand_rents_member_company_no" class="form-control" placeholder="如不打統編請留空白"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確統編<br/>例如：80682490"
												data-validation-error-msg-container="#company_no_error_msg_hand_rents_member_company_no"
												/>
												<span id="company_no_error_msg_hand_rents_member_company_no"></span>
                                            </td>
                                        </tr>

<tr>
<td style="text-align:right;">發票金額</td>
<td style="text-align:left;color:blue;" id="hand_rents_invoice_amt"></td>
</tr>
<tr>
<td style="text-align:right;">開立說明</td>
<td style="text-align:left;color:blue;" id="hand_rents_remain_desc"></td>
</tr>   

<!--tr>
<td style="text-align:right;vertical-align:middle">發票字軌</td>
<td style="text-align:left;"><input type="text" id="hand_rents_invoice_track" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">發票字軌</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="text" id="hand_rents_invoice_track" class="form-control" placeholder="請填入發票字軌2碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{2}$)([A-Z]+)$"
												data-validation-error-msg="請輸入正確發票字軌<br/>例如：AB"
												data-validation-error-msg-container="#mobile_error_msg_hand_rents_invoice_track"
												/>
												<span id="mobile_error_msg_hand_rents_invoice_track"></span>
                                            </td>
                                        </tr>

<!--tr>
<td style="text-align:right;vertical-align:middle">發票號碼</td>
<td style="text-align:left;"><input type="text" id="hand_rents_invoice_no" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">發票號碼</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="hand_rents_invoice_no" class="form-control" placeholder="請填入發票數字8碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確發票號碼<br/>例如：12345678"
												data-validation-error-msg-container="#mobile_error_msg_hand_rents_invoice_no"
												/>
												<span id="mobile_error_msg_hand_rents_invoice_no"></span>
                                            </td>
                                        </tr>


</tbody>
</table>                    
<button type="button" class="btn btn-large btn-success pull-left" onclick="do_hand_rents_payment();">確認送出</button>
&nbsp;&nbsp;
<button type="button" class="btn btn-large btn-cancel" onclick="$('#hand_rents_dialog').modal('hide');">取消</button>
</div><!-- ----- end of dataTable_wrapper ----- -->  
</div><!-- ----- end of main ----- -->
</form>
</div><!-- end of modal-body --> 
</div><!-- end of modal-content --> 
</div><!-- end of modal-dialog -->
</div><!-- end of modal show -->
<!-- ----- 手開：繳租作業小框(結束) ----- -->  

<script>  
// 手開：月租付款
function do_hand_rents_payment()
{
	if($("#hand_rents_company_no").val() == '0' || $("#hand_rents_company_no").val() == '' || $("#hand_rents_company_no").val().length != 8)
	{
		alertify_error('賣方統編有誤，請修正');
		return false;
	}
	
	if($("#hand_rents_member_company_no").val() != '' && $("#hand_rents_member_company_no").val().length != 8)
	{
		alertify_error('買方統編有誤，請修正');
		return false;
	}
	
	/*
	if($("#hand_rents_invoice_track").val() == '0' || $("#hand_rents_invoice_track").val() == '' || $("#hand_rents_invoice_track").val().length != 2)
	{
		alertify_error('發票字軌有誤，請修正');
		return false;
	}
	
	if($("#hand_rents_invoice_no").val() == '0' || $("#hand_rents_invoice_no").val() == '' || $("#hand_rents_invoice_no").val().length != 8)
	{
		alertify_error('發票號碼有誤，請修正');
		return false;
	}
	*/
	
	if (!confirm("確認資料無誤並送出 ?")) return false; 
	
	var member_no = xvars["rents"]["member_no"];
	var company_no = $("#hand_rents_company_no").val();
	var member_company_no = $("#hand_rents_member_company_no").val();
	if (member_company_no == "")	member_company_no = "0";	// 建立二聯式發票
	
	// 新增月租付款交易
	$.ajax
    ({
        url:APP_URL+"hand_rents_payment", 
        dataType:"text",
        type:"post",
        data:
        {
        	"station_no":xvars["rents"]["station_no"],
        	"member_no":member_no,  
			"lpr":xvars["rents"]["lpr"],
        	"member_company_no":member_company_no,
        	"company_no":company_no,
        	"fee_period":xvars["rents"]["fee_period"],
        	"fee_period_last":xvars["rents"]["fee_period_last"],
        	"amt":xvars["rents"]["amt"],
        	"amt_last":xvars["rents"]["amt_last"],
        	"end_date":xvars["rents"]["end_date"],
        	"start_date_last":xvars["rents"]["start_date"],
        	"end_date_last":xvars["rents"]["end_date_last"],
			"invoice_track":$("#hand_rents_invoice_track").val(),
			"invoice_no":$("#hand_rents_invoice_no").val()
        },
		error:function(xhr, ajaxOptions, thrownError)
        {
			alertify_msg(xhr.responseText);
        	console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
        },
        success:function(jdata)
        {                                                                            
			if (jdata == "ok")	
            {                              
            	alertify_msg("完成 ! "); 
            	show_member_tx(member_no);
            }
            else
            {
              	//alertify_msg("操作失敗！");
				alertify_msg("操作失敗。。" + jdata);
            }
        }
    });
    
    delete xvars["rents"];
    $('#hand_rents_dialog').modal('hide'); 
}
</script>  






<!-- ----- [P.1] 首期繳租作業小框 ----- -->

<!-- ----- ID: 		first_rents_dialog ----- -->
<!-- ----- action: 	do_first_rents_payment() ----- -->

<div class="modal fade" id="first_rents_dialog">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header"><h3>電子發票</h3></div>
<div class="modal-body">
<form id="first_rents_form" class="center-block">    
<div class="main">
<div class="dataTable_wrapper">
<table class="table table-striped table-bordered table-hover" style="font-size:12px;">
<tbody id="first_rents_list">
<tr>
<td style="text-align:right;">姓名</td>
<td style="text-align:left;" id="first_rents_name"></td>
</tr>  
<tr>
<td style="text-align:right;">場站</td>
<td style="text-align:left;" id="first_rents_station_name"></td>
</tr>

<!--tr>
<td style="text-align:right;vertical-align:middle">賣方統編</td>
<td style="text-align:left;"><input type="text" id="first_rents_company_no" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">賣方統編</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="first_rents_company_no" class="form-control" placeholder="賣方統編號碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確統編<br/>例如：80682490"
												data-validation-error-msg-container="#company_no_error_msg_0"
												readonly/>
												<span id="company_no_error_msg_0"></span>
                                            </td>
                                        </tr>

<tr>
<td style="text-align:right;">開始日</td>
<td style="text-align:left;" id="first_rents_start_date"></td>
</tr>   
<tr>
<td style="text-align:right;">到期日</td>
<td style="text-align:left;" id="first_rents_end_date"></td>
</tr>   
<tr>
<td style="text-align:right;">押金</td>
<td style="text-align:left;" id="first_rents_deposit"></td>
</tr>   
<tr>
<td style="text-align:right;">首期租金</td>
<td style="text-align:left;" id="first_rents_amt1"></td>
</tr>   
<tr>
<td style="text-align:right;">例行繳期</td>
<td style="text-align:left;" id="first_rents_period"></td>
</tr>   
<tr>
<td style="text-align:right;">例行租金</td>
<td style="text-align:left;" id="first_rents_amt"></td>
</tr>
<tr>
<td style="text-align:right;">發票金額</td>
<td style="text-align:left;color:blue;" id="first_rents_invoice_amt"></td>
</tr>
<tr>
<td style="text-align:right;">開立說明</td>
<td style="text-align:left;color:blue;" id="first_rents_remain_desc"></td>
</tr>   
<!--tr>
<td style="text-align:right;vertical-align:middle">買方統編</td>
<td style="text-align:left;"><input type="text" id="first_rents_member_company_no" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">公司統編</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="first_rents_member_company_no" class="form-control" placeholder="如不打統編請留空白"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確統編<br/>例如：80682490"
												data-validation-error-msg-container="#company_no_error_msg_first_rents_member_company_no"
												/>
												<span id="company_no_error_msg_first_rents_member_company_no"></span>
                                            </td>
                                        </tr>
										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">電子信箱</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="email" id="first_rents_member_email" class="form-control" placeholder="發票將寄信通知"
												data-validation="email"
												data-validation-optional="true"
												data-validation-error-msg="請輸入正確信箱<br/>例如：altob@gmail.com"
												data-validation-error-msg-container="#email_error_msg_first_rents_member_email"
												/>
												<span id="email_error_msg_first_rents_member_email"></span>
                                            </td>
                                        </tr>
										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">手機號碼</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="first_rents_member_mobile" class="form-control" placeholder="發票將寄簡訊通知"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{10}$)09([0-9]+)$"
												data-validation-error-msg="請輸入正確手機號碼<br/>例如：0912345678"
												data-validation-error-msg-container="#mobile_error_msg_first_rents_member_mobile"
												/>
												<span id="mobile_error_msg_first_rents_member_mobile"></span>
                                            </td>
                                        </tr>
</tbody>
</table>                    
<button type="button" class="btn btn-large btn-success pull-left" onclick="do_first_rents_payment();">建立</button>
&nbsp;&nbsp;
<button type="button" class="btn btn-large btn-cancel" onclick="$('#first_rents_dialog').modal('hide');">稍後再印</button>
</div><!-- ----- end of dataTable_wrapper ----- -->  
</div><!-- ----- end of main ----- -->
</form>
</div><!-- end of modal-body --> 
</div><!-- end of modal-content --> 
</div><!-- end of modal-dialog -->
</div><!-- end of modal show -->
<!-- ----- 首期繳租作業小框(結束) ----- --> 

<script>

// 首期月租付款並建立電子發票
function do_first_rents_payment()
{
	if($("#first_rents_company_no").val() == '0' || $("#first_rents_company_no").val() == '' || $("#first_rents_company_no").val().length != 8)
	{
		alertify_error('賣方統編有誤，請修正');
		return false;
	}
	
	if($("#first_rents_member_company_no").val() != '' && $("#first_rents_member_company_no").val().length != 8)
	{
		alertify_error('買方統編有誤，請修正');
		return false;
	}
	
	if (!confirm("確認已繳款並即將建立電子發票 ?")) return false; 
	
	var member_no = xvars["rents"]["member_no"];
	var company_no = $("#first_rents_company_no").val();
	var member_company_no = $("#first_rents_member_company_no").val();
	if (member_company_no == "")	member_company_no = "0";	// 建立二聯式發票
	
	var member_email = $("#first_rents_member_email").val();
	var member_mobile = $("#first_rents_member_mobile").val();
	
	// 新增月租付款交易
	$.ajax
    ({
        url:APP_URL+"first_rents_payment", 
        dataType:"text",
        type:"post",
        data:
        {
			"tx_no": xvars["rents"]["tx_no"],
        	"station_no":xvars["rents"]["station_no"],
        	"member_no":member_no,  
        	"member_company_no":member_company_no,
        	"company_no":company_no,
        	"amt":xvars["rents"]["amt"],
			"amt1":xvars["rents"]["amt1"],
			"invoice_amt":xvars["rents"]["invoice_amt"],
			"tx_bill_no":xvars["rents"]["tx_bill_no"],
			"email":member_email,
			"mobile":member_mobile
        },
		error:function(xhr, ajaxOptions, thrownError)
        {
			alertify_msg(xhr.responseText);
        	console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
        },
        success:function(jdata)
        {                                                                            
			if (jdata == "ok")	
            {                              
            	alertify_msg("操作完成 ! "); 
            	show_member_tx(member_no);
            }
			else if (jdata == "invoice_fail")	
            {                              
            	alertify_msg("發票建立失敗，請補印發票"); 
			}
            else
            {
             	//alertify_msg("月租付款失敗！");
				alertify_msg("操作失敗。。" + jdata);
            }
        }
    });
    
    delete xvars["rents"];
    $('#first_rents_dialog').modal('hide'); 
}

// 補印發票
function print_tx_invoice(tx_bill_no)
{
	var station_no = $("#tx_bill_lpr_"+tx_bill_no).data("station_no");
	var member_no = $("#tx_bill_lpr_"+tx_bill_no).data("member_no");
	var member_name = $("#tx_bill_lpr_"+tx_bill_no).data("member_name");
	var member_company_no = $("#tx_bill_lpr_"+tx_bill_no).data("member_company_no");
	var company_no = $("#tx_bill_lpr_"+tx_bill_no).data("company_no");
	var invoice_amt = $("#tx_bill_lpr_"+tx_bill_no).data("invoice_amt");
	var remain_amt = $("#tx_bill_lpr_"+tx_bill_no).data("remain_amt");
	var period_3_amt = $("#tx_bill_lpr_"+tx_bill_no).data("period_3_amt");
	var amt = $("#tx_bill_lpr_"+tx_bill_no).data("amt");
	var amt1 = $("#tx_bill_lpr_"+tx_bill_no).data("amt1");
	var deposit = $("#tx_bill_lpr_"+tx_bill_no).data("deposit");
	var start_date_last = $("#tx_bill_lpr_"+tx_bill_no).data("start_date_last");
	var end_date = $("#tx_bill_lpr_"+tx_bill_no).data("end_date");
	var lpr = $("#tx_bill_lpr_"+tx_bill_no).data("lpr");
	var fee_period = $("#tx_bill_lpr_"+tx_bill_no).data("fee_period");
	var fee_period_last = $("#tx_bill_lpr_"+tx_bill_no).data("fee_period_last");
	var tx_no = $("#tx_bill_lpr_"+tx_bill_no).data("tx_no");
	
	if(invoice_amt > 0)
	{
		// 首期月租付款並建立電子發票
		xvars["rents"] = Array();             
		xvars["rents"]["tx_bill_no"] = tx_bill_no;
		xvars["rents"]["tx_no"] = tx_no;
		xvars["rents"]["station_no"] = station_no;
		xvars["rents"]["member_no"] = member_no;
		xvars["rents"]["member_company_no"] = member_company_no;
		xvars["rents"]["company_no"] = company_no;
		xvars["rents"]["fee_period"] = fee_period;
		xvars["rents"]["amt"] = parseInt(amt);
		xvars["rents"]["amt1"] = parseInt(amt1);
		xvars["rents"]["invoice_amt"] = parseInt(invoice_amt);
		xvars["rents"]["remain_amt"] = parseInt(remain_amt);
		xvars["rents"]["period_3_amt"] = parseInt(period_3_amt);

		$("#first_rents_name").text( "新會員 (" + lpr +")");
		$("#first_rents_station_name").text(st[xvars["rents"]["station_no"]]);
		$("#first_rents_start_date").text(start_date_last);						// 上期開始日
		$("#first_rents_end_date").text(end_date);								// 本期結束日
		$("#first_rents_deposit").text(deposit);
		$("#first_rents_period").text(period_name[xvars["rents"]["fee_period"]]);
		$("#first_rents_amt").text(xvars["rents"]["amt"]);
		$("#first_rents_amt1").text(xvars["rents"]["amt1"]);
		
		$("#first_rents_invoice_amt").text(xvars["rents"]["invoice_amt"]);
		$("#first_rents_remain_desc").html(get_invoice_desc(xvars["rents"]["amt"], xvars["rents"]["amt1"], xvars["rents"]["invoice_amt"], xvars["rents"]["remain_amt"], xvars["rents"]["period_3_amt"]));	// 發票說明
		
		$("#first_rents_company_no").val(xvars["rents"]["company_no"]); // 賣方統編

		if(xvars["rents"]["member_company_no"] == 0 || xvars["rents"]["member_company_no"] == '')
		{
			$("#first_rents_member_company_no").val("");
		}
		else
		{
			$("#first_rents_member_company_no").val(xvars["rents"]["member_company_no"]); // 買方統編
		}
		
		$("#first_rents_dialog").modal({backdrop:false,keyboard:false});
	}
	else
	{
		// 各期月租發票補印
		alertify_msg("流程異常：請通知總公司處理：" + tx_bill_no);
	}
}
</script>  







<!-- ----- [P.2] 繳租作業小框 ----- -->

<!-- ----- ID: 		rents_dialog ----- -->
<!-- ----- action: 	do_rents_payment() ----- -->

<div class="modal fade" id="rents_dialog">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header"><h3>繳租作業</h3></div>
<div class="modal-body">
<form id="rents_form" class="center-block">    
<div class="main">
<div class="dataTable_wrapper">
<table class="table table-striped table-bordered table-hover" style="font-size:12px;">
<tbody id="rents_list">
<tr>
<td style="text-align:right;">姓名</td>
<td style="text-align:left;" id="rents_name"></td>
</tr>  
<tr>
<td style="text-align:right;">場站</td>
<td style="text-align:left;" id="rents_station_name"></td>
</tr>
<!--tr>
<td style="text-align:right;vertical-align:middle">賣方統編</td>
<td style="text-align:left;"><input type="text" id="rents_company_no" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">賣方統編</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="rents_company_no" class="form-control" placeholder="賣方統編號碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確統編<br/>例如：80682490"
												data-validation-error-msg-container="#company_no_error_msg_rents_company_no"
												readonly/>
												<span id="company_no_error_msg_rents_company_no"></span>
                                            </td>
                                        </tr>

<tr>
<td style="text-align:right;">上期到期日</td>
<td style="text-align:left;" id="rents_end_date_last"></td>
</tr>
<tr>
<td style="text-align:right;">本期到期日</td>
<td style="text-align:left;" id="rents_end_date"></td>
</tr>   
<tr>
<td style="text-align:right;">繳期</td>
<td style="text-align:left;" id="rents_period"></td>
</tr>   
<tr>
<td style="text-align:right;">租金</td>
<td style="text-align:left;" id="rents_amt"></td>
</tr>   

<!--tr>
<td style="text-align:right;vertical-align:middle">買方統編</td>
<td style="text-align:left;"><input type="text" id="rents_member_company_no" class="form-control" style="width:100px !important;" /></td>
</tr-->

										<tr class="form-group">
                                            <td style="text-align:right;vertical-align: middle;">買方統編</td>
											<td style="text-align:left;vertical-align: middle;">
											<input type="tel" id="rents_member_company_no" class="form-control" placeholder="買方統編號碼"
												data-validation="custom"
												data-validation-optional="true"
												data-validation-regexp="^(?=.{8}$)([0-9]+)$"
												data-validation-error-msg="請輸入正確統編<br/>例如：80682490"
												data-validation-error-msg-container="#company_no_error_msg_rents_member_company_no"
												/>
												<span id="company_no_error_msg_rents_member_company_no"></span>
                                            </td>
                                        </tr>

</tbody>
</table>                    
<button type="button" class="btn btn-large btn-success pull-left" onclick="do_rents_payment();">確定</button>
&nbsp;&nbsp;
<button type="button" class="btn btn-large btn-cancel" onclick="$('#rents_dialog').modal('hide');">取消交易</button>
</div><!-- ----- end of dataTable_wrapper ----- -->  
</div><!-- ----- end of main ----- -->
</form>
</div><!-- end of modal-body --> 
</div><!-- end of modal-content --> 
</div><!-- end of modal-dialog -->
</div><!-- end of modal show -->
<!-- ----- 繳租作業小框(結束) ----- --> 

<script>
// 月租付款並建立電子發票
function do_rents_payment()
{                                    
	if($("#rents_company_no").val() == '0' || $("#rents_company_no").val() == '' || $("#rents_company_no").val().length != 8)
	{
		alertify_error('賣方統編有誤，請修正');
		return false;
	}
	
	if($("#rents_company_no").val() != '' && $("#rents_company_no").val().length != 8)
	{
		alertify_error('買方統編有誤，請修正');
		return false;
	}

	if (!confirm("確認已繳款並開立發票 ?")) return false;                   
	
	var member_no = xvars["rents"]["member_no"];
	var company_no = $("#rents_company_no").val();
	var member_company_no = $("#rents_member_company_no").val();
    if (member_company_no == "")	member_company_no = "0";	// 建立二聯式發票
    
    // 新增月租付款交易
	$.ajax
    ({
        url:APP_URL+"rents_payment", 
        dataType:"text",
        type:"post",
        data:
        {
        	"station_no":xvars["rents"]["station_no"],
        	"member_no":member_no,  
			"member_attr":xvars["rents"]["member_attr"],
			"lpr":xvars["rents"]["lpr"],
        	"member_company_no":member_company_no,
        	"company_no":company_no,
        	"fee_period":xvars["rents"]["fee_period"],
        	"fee_period_last":xvars["rents"]["fee_period_last"],
        	"amt":xvars["rents"]["amt"],
        	"amt_last":xvars["rents"]["amt_last"],
        	"end_date":xvars["rents"]["end_date"],
        	"start_date_last":$("#sdate_"+xvars["rents"]["member_no"]).text(),
        	"end_date_last":xvars["rents"]["end_date_last"]
        },
		error:function(xhr, ajaxOptions, thrownError)
        {
			alertify_msg(xhr.responseText);
        	console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
        },
        success:function(jdata)
        {                                                                            
			if (jdata == "ok")	
            {                              
            	alertify_msg("月租付款交易成功 ! "); 
            	show_member_tx(member_no);
            }
			else if (jdata == "invoice_fail")	
            {                              
            	alertify_msg("發票建立失敗，交易已取消"); 
			}
            else
            {
              	//alertify_msg("月租付款失敗 !");
				alertify_msg("操作失敗。。" + jdata);
            }   
        }
    });
	
    delete xvars["rents"];
    $('#rents_dialog').modal('hide'); 
} 
</script>  





<!-- ----- [S] 退租作業小框 ----- -->

<!-- ----- ID: 		stop_rents_dialog ----- -->
<!-- ----- action: 	do_stop_rents_payment() ----- -->

<div class="modal fade" id="stop_rents_dialog">
<div class="modal-dialog modal-xl">
<div class="modal-content">
<div class="modal-header"><h3>退租作業</h3></div>
<div class="modal-body">
<form id="stop_rents_form" class="center-block">    
<div class="main">
<div class="dataTable_wrapper">
<table class="table table-striped table-bordered table-hover" style="font-size:18px;">
<tbody id="stop_rents_list">
<tr>
<td style="text-align:right;">姓名</td>
<td style="text-align:left;" id="stop_rents_name"></td>
</tr>  
<tr>
<td style="text-align:right;">場站</td>
<td style="text-align:left;" id="stop_rents_station_name"></td>
</tr>
<tr>
<td style="text-align:right;">身份別</td>
<td style="text-align:left;" id="stop_rents_member_attr"></td>
</tr>  
<tr>
<td style="text-align:right;">繳期</td>
<td style="text-align:left;" id="stop_rents_period"></td>
</tr>
<!--tr>
<td style="text-align:right;">繳期租金</td>
<td style="text-align:left;" id="stop_rents_amt"></td>
</tr-->
<tr>
<td style="text-align:right;">到期日</td>
<td style="text-align:left;" id="stop_rents_end_date_last"></td>
</tr>   
<tr>
<td style="text-align:right;">退租日</td>
<td style="text-align:left;"><input id="stop_rents_end_date" type="datetime" name="stop_rents_end_date" class="form-control" onchange="calculate_stop_rents_amt();"/></td>
</tr>   
<tr>
<td style="text-align:right;">退租明細</td>
<td style="text-align:left;font-size:24px;color:blue;" id="stop_rents_desc"></td>
</tr>
<tr>
	<input id="stop_rents_tot_amt" type="hidden" name="stop_rents_tot_amt"/>
</tr>
</tbody>
</table>                    
<button type="button" class="btn btn-large btn-success pull-left" onclick="do_stop_rents_payment();">確定</button>
&nbsp;&nbsp;
<button type="button" class="btn btn-large btn-cancel" onclick="$('#stop_rents_dialog').modal('hide');">取消</button>
</div><!-- ----- end of dataTable_wrapper ----- -->  
</div><!-- ----- end of main ----- -->
</form>
</div><!-- end of modal-body --> 
</div><!-- end of modal-content --> 
</div><!-- end of modal-dialog -->
</div><!-- end of modal show -->
<!-- ----- 退租作業小框(結束) ----- --> 

<script>

// 退租, 試算
function calculate_stop_rents_amt()
{
	var station_no = xvars["rents"]["station_no"];
	var member_no = xvars["rents"]["member_no"];
	var stop_date = $("#stop_rents_end_date").val();
	
	$("#stop_rents_desc").html("");
	
	$.ajax
    ({
        url:APP_URL+"calculate_stop_rents_amt", 
        dataType:"json",
        type:"post",
        data:
        {             
        	"station_no":station_no, 
            "member_no":member_no, 
			"stop_date":stop_date
        },
        success:function(jdata)
        {   
			var tmp_list = [];
			var invoice_tmp_list = [];
			
			for(idx in jdata["results"])
			{
				var detail = jdata["results"][idx];
				var fee_period_last_name = (detail.fee_period_last == 0) ? '臨停費率': period_name[detail.fee_period_last] ? period_name[detail.fee_period_last] : '繳期 ' + detail.fee_period_last;
				var fee_period_name = (detail.fee_period == 0) ? '臨停費率': period_name[detail.fee_period] ? period_name[detail.fee_period] : '繳期 ' + detail.fee_period;
				var stop_rents_period_name = (detail.stop_rents_period == 0) ? '臨停費率': period_name[detail.stop_rents_period] ? period_name[detail.stop_rents_period] : '繳期 ' + detail.stop_rents_period;
				
				// 不足月
				if(detail.amt1 > 0)
				{
					tmp_list = tmp_list.concat([
							"[ ", detail.start_date_last, " 至 ", detail.end_date_last, " ] <br/>",
							"* 計費：", detail.amt1, " 元 (", fee_period_last_name, ")<br/>"]);
							
					if(detail.stop_rents_used_amt_last == detail.amt1)
					{
						tmp_list = tmp_list.concat(["**使用完畢**<br/>"]);
					}
					else if(detail.stop_rents_used_amt_last > 0)
					{
						tmp_list = tmp_list.concat([
							"* 使用：", detail.stop_used_days_last, " 天<br/>",
							"* 費用：", detail.stop_rents_used_amt_last, " 元 (臨停費率)<br/>"]);	
					}
					else
					{
						tmp_list = tmp_list.concat(["**尚未使用**<br/>"]);
					}
				}
				
				// 足月
				if(detail.amt > 0)
				{
					tmp_list = tmp_list.concat([
							"[ ", detail.start_date, " 至 ", detail.end_date, " ] <br/>",
							"* 計費：", detail.amt, " 元 (", fee_period_name, ")<br/>"]);
							
					if(detail.stop_rents_used_amt == detail.amt)
					{
						tmp_list = tmp_list.concat(["**使用完畢**<br/>"]);
					}
					else if(detail.stop_used_days > 0)
					{
						tmp_list = tmp_list.concat([
							"* 使用：", detail.stop_used_days, " 天<br/>",
							"* 費用：", detail.stop_rents_used_amt, " 元 (", stop_rents_period_name, ")<br/>"]);
					}
					else
					{						
						tmp_list = tmp_list.concat(["**尚未使用**<br/>"]);
					}
				}
				
				//發票資訊
				for(idx_i in detail.stop_rents_invoices)
				{
					var invoice = detail.stop_rents_invoices[idx_i];
					tmp_list = tmp_list.concat(["**發票: ", invoice.invoice_track, invoice.invoice_no, "**金額: ", invoice.invoice_amt]);
					if(invoice.refund_amt > 0)
					{
						tmp_list = tmp_list.concat(["<br/>", "****************待折讓發票金額：", invoice.refund_amt]);		

						invoice_tmp_list = invoice_tmp_list.concat([
							
							"**", detail.start_date, "**發票: ", invoice.invoice_track, invoice.invoice_no, "<br/>",
							"****發票金額: ", invoice.invoice_amt, "**待折讓金額：", invoice.refund_amt, "<br/>"]);			
					}
					tmp_list = tmp_list.concat(["<br/>"]);
				}	
				
				// 審核狀態
				if(detail.verify_state == 1)
				{
					// 本次結算
					if(detail.stop_rents_return_amt == 0)
					{
						// do nothing
					}
					else if(detail.stop_rents_return_amt > 0)
					{
						tmp_list = tmp_list.concat([">>本次退還金額：", detail.stop_rents_return_amt, " 元<br/>"]);		
					}
					else
					{
						tmp_list = tmp_list.concat([">>本次補繳金額：", - detail.stop_rents_return_amt, " 元<br/>"]);		
					}
				}
				else
				{
					tmp_list = tmp_list.concat(["**此交易，尚未通過審核**<br/>"]);
					if(detail.remarks)
					{
						tmp_list = tmp_list.concat(["**備註：", detail.remarks, "<br/>"]);
					}
				}
				
				tmp_list = tmp_list.concat(["<br/>"]);
			}
			
			// 總結
			tmp_list = tmp_list.concat([
						"[結算]<br/>",
						"**合約終止時間：", stop_date, " 23:59:59 <br/> "]);
			
			if(jdata["return_amt"] == 0)				
			{
				tmp_list = tmp_list.concat([
						"**總退還金額：無<br/> "]);	
			}
			else if(jdata["return_amt"] > 0)
			{		
				tmp_list = tmp_list.concat([
						"**總退還金額：", jdata["return_amt"], " 元<br/> "]);	
			}
			else
			{
				tmp_list = tmp_list.concat([
						"**需補繳金額：", - jdata["return_amt"], " 元 (臨停費率)<br/> "]);	
			}
			
			tmp_list = tmp_list.concat([
						"**押金：", jdata["return_deposit"], " 元<br/><br/>"]);	
			
			// 總結 [折讓發票]
			if(invoice_tmp_list.length > 0)
			{
				tmp_list = tmp_list.concat("[折讓發票]<br/>");	
				tmp_list = tmp_list.concat(invoice_tmp_list);
				tmp_list = tmp_list.concat("<br/><br/>");	
			}
			
			if(jdata["verify_state"])
			{
				// 設定總金額
				var tot_amt = parseInt(jdata["return_amt"]) + parseInt(jdata["return_deposit"]);
				$("#stop_rents_tot_amt").text(tot_amt);
				//console.log(station_no + "|" +  + member_no + "|" + tot_amt + "|" + stop_date);
			}
			else
			{
				// 審核未完成
				tmp_list = tmp_list.concat([
							"**審核未完成, 請通知營管處理**<br/><br/>"]);
				$("#stop_rents_tot_amt").text('GG');
			}
			
			$("#stop_rents_desc").append(tmp_list.join('')); 
    	}                                                                          
    }); 
}

// 退租
function do_stop_rents_payment()
{                                    
	if($("#stop_rents_end_date").val() == '0' || $("#stop_rents_end_date").val() == '')
	{
		alertify_error('退租日有誤，請修正！');
		return false;
	}                
	
	if($("#stop_rents_tot_amt").text() == 'GG')
	{
		alertify_error('請通知營管處理，謝謝！');
		return false;
	}
	
	var station_no = xvars["rents"]["station_no"];
	var member_no = xvars["rents"]["member_no"];
	var stop_date = $("#stop_rents_end_date").val();
	var tot_amt = $("#stop_rents_tot_amt").text();
    var lpr = xvars["rents"]["lpr"];
	
    // 新增退租交易
	$.ajax
    ({
        url:APP_URL+"stop_rents_payment", 
        dataType:"text",
        type:"post",
        data:
        {
        	"station_no":station_no,
        	"member_no":member_no,  
			"stop_date":stop_date,
			"tot_amt":tot_amt
        },
		error:function(xhr, ajaxOptions, thrownError)
        {
			alertify_msg(xhr.responseText);
        	console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
        },
        success:function(jdata)
        {                                                                            
			if (jdata == "ok")	
            {                              
            	alertify_msg("成功 ! "); 
				
				// 自動搜尋退租
				$("#q_refund_str").val(lpr);
            	$("#member_tx_refund_form").submit();
				show_item('member_tx_refund_query', 'member_tx_refund_query');
            }
			else if (jdata == "invoice_fail")	
            {                              
            	alertify_msg("失敗，已取消"); 
			}
            else
            {
              	//alertify_msg("失敗 !");
				alertify_msg("操作失敗。。" + jdata);
            }   
        }
    });
	
    delete xvars["rents"];
    $('#stop_rents_dialog').modal('hide'); 
} 
</script>  



