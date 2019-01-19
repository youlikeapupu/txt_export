<?php
	function export_txt(Request $request)
	{

	    // dd($request->name);
	    $filename = date('Ymd')."-訂單列表";

	                //$orders = Order::latest()->get();
	    $orders = Order::orderBy('created_at', 'DESC');


	    //             //日期
	    // if ($request->start_at != '') {
	    //     $orders = $orders->where('created_at', '>=', $request->start_at);
	    // }

	    // if ($request->end_at != '') {
	    //     $orders = $orders->where('created_at', '<=', $request->end_at);
	    // }

	    //             //名稱
	    // if ($request->name != '') {
	    //     $orders = $orders->where('name', 'like', "%$request->name%");
	    // }

	    //             //電話
	    // if ($request->phone != '') {
	    //     $orders = $orders->where('phone', 'like', "%$request->phone%");
	    // }

	    //             //訂單編號
	    // if ($request->order_start_at != '') {
	    //     $orders = $orders->where('orderNo', '>=', $request->order_start_at);
	    // }

	    // if ($request->order_end_at != '') {
	    //     $orders = $orders->where('orderNo', '<=', $request->order_end_at);
	    // }

	    // if (isset($request->status)) {
	    //     $orders = $orders->whereIn('status', $request->status);
	    // }

	    // if (isset($request->payment)) {
	    //     $orders = $orders->whereIn('paymentType', $request->payment);
	    // }

	    // $orders = $orders->get();

	    //dd($orders);
	    //撈門市做成陣列
	    $shop_sql = "SELECT id,shopNo FROM shops";
	    $shop_sql_r = DB::select($shop_sql);

	    $shop_id = array();
	    $shop_no = array();
	    foreach ($shop_sql_r as $sk => $sv) {
	        array_push($shop_id,$sv->id);
	        array_push($shop_no,$sv->shopNo);
	    }
	    $shop_arr = array_combine($shop_id, $shop_no);
	    //dd($shop_arr);

	    $order_obj_Arr = array();
	    foreach ($orders as $k => $v) {
	        $order_obj = new \stdClass;
	        $order_obj->shipmethods = FT_SHIP_METHODS[$v->shipMethods];
	        $order_obj->orderno = $v->orderNo;
	        $order_obj->name = $v->name;
	        $order_obj->phone = $v->phone;

	        switch ($v->shipMethods) {
	            case 0:
	            $order_obj->addr = $v->county.$v->district.$v->address;
	            break;
	            case 1:
	            $order_obj->addr = $v->ezship_data['stName'];
	            break;
	            case 4:
	            $order_obj->addr = $shop_arr[$v->shopNo];
	            break;
	            default:
	            $order_obj->addr = $v->county.$v->district.$v->address;
	        }

	        array_push($order_obj_Arr,$order_obj);
	    }

	    //dd($order_obj_Arr);

	    $txtfileName = 'uploads/txt/'.date("Y-m-d").'.txt';
	    $txt= '';
	    if (file_exists($txtfileName)) {
	       //打開檔案
	        $txtfile = fopen($txtfileName, "a") or die("Unable to open file!");
	        foreach ($order_obj_Arr as $ok => $ov) {
	            $txt .= $ov->shipmethods.','.$ov->orderno.',';
	            $txt .= $ov->name.','.$ov->phone.','.$ov->addr."\r\n";
	        }
	        fwrite($txtfile, $txt);
	        //關閉檔案
	        fclose($txtfile);
	    } else {
	        $fp = fopen($txtfileName,"w");
	        foreach ($order_obj_Arr as $ok => $ov) {
	            $txt .= $ov->shipmethods.','.$ov->orderno.',';
	            $txt .= $ov->name.','.$ov->phone.','.$ov->addr."\r\n";
	        }
	        fwrite($fp, $txt);
	        //關閉檔案
	        fclose($fp);
	    }

	    //$file = base_path().'\log\txt\test.txt';
	    //dd($file);
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.basename($txtfileName));
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: '.filesize($txtfileName));
	    readfile($txtfileName);

	            // $r_arr = array('status' => 'ERROR');
	            // echo json_encode($r_arr);

	}

?>
