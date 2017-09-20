			<!-- ----- 場站設定 ----- -->  
            <div data-items="station_setting" class="row" style="display:none;"><!-- 場站設定 -->
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div id="member_data_type" class="panel-heading">
							目前場站設定
							&nbsp;<button id='reload_station_setting_btn' class="btn btn-large btn-success pull-right" style="font-size:20px;" onclick='reset_station_setting();'>重新載入</button>
						</div><!-- 資料顯示區灰色小表頭 -->
                        <div class="panel-body">
                            <div data-rows class="row">
                                <div class="col-lg-6">
                                    <!--form id="station_setting" role="form" method="post" data-src="action::APP_URL::station_setting"-->  
                                    <form id="station_setting" role="form" method="post" data-src="/carpark.html/station_setting">  
                                        <div class="form-group">
                                            <label style="font-size:22px">場站名稱</label>
                                            <input id="ss_station_name" name="station_name" class="form-control"  style="font-size:28px" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label style="font-size:22px">場站編號（若為多場站共用，以 ',' 隔開。）</label>
                                            <input id="ss_station_no" name='station_no' class="form-control"  style="font-size:28px" readonly>
                                        </div> 
										<div class="form-group">
                                            <label style="font-size:22px">場站 IP</label>
                                            <input id="ss_station_ip" name='station_ip' class="form-control"  style="font-size:28px" readonly>
                                        </div> 
										<!--button type="submit" class="btn btn-default">存檔</button>
                                        <button type="reset" class="btn btn-default">重填</button-->
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- ----- 場站設定(結束) ----- --> 
			
<script>  
	
// 重新載入
function reset_station_setting()
{
	event.preventDefault();
           
	$.ajax
        ({
        	url: "<?=APP_URL?>station_setting_query",
            type: "post", 
            dataType:"json",
			data:{ 'reload': 1 },
			error:function(xhr, ajaxOptions, thrownError)
			{
				alertify_msg(xhr.responseText);
				console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
				return false;
			},
            success: function(jdata)
            {       
				if(jdata == 'fail')
				{
					$("#ss_station_name").val('未設定');
					$("#ss_station_no").val('');
					$("#ss_station_ip").val(jdata['station_ip']);
					alertify_error('載入失敗。。');		
					return false;
				}
				
				$("#ss_station_name").val(jdata['station_name']);
				$("#ss_station_no").val(jdata['station_no']);
				$("#ss_station_ip").val(jdata['station_ip']);
				alertify_success('完成。。');	
            }
        }); 
}
	

// 載入目前設定
function reload_station_setting(type)
{
	$.ajax
        ({
        	url: "<?=APP_URL?>station_setting_query",
            type: "post", 
            dataType:"json",
			data:{ 'reload': 0 },
			error:function(xhr, ajaxOptions, thrownError)
			{
				alertify_msg(xhr.responseText);
				console.log("error:"+xhr.responseText+"|"+ajaxOptions+"|"+thrownError);  
				return false;
			},
            success: function(jdata)
            {       
				if(jdata == 'fail')
				{
					$("#ss_station_name").val('未設定');
					$("#ss_station_no").val('');
					$("#ss_station_ip").val(jdata['station_ip']);
					alertify_error('載入失敗。。');		
					return false;
				}
				
				$("#ss_station_name").val(jdata['station_name']);
				$("#ss_station_no").val(jdata['station_no']);
				$("#ss_station_ip").val(jdata['station_ip']);
				alertify_success('完成。。');	
            }
        }); 
}
	
</script>