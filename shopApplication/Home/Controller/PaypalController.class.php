<?php
namespace Home\Controller;
use 	  Think\Controller;
/*
*	@get IPN Message
*/
class PaypalController extends Controller {

	private $_url;					// URL
	
	/*
	*	@get the URL
	*/
	public function __construct($mode = 'live'){
		if($mode == 'live'){
			$this->_url = 'https://www.paypal.com/cgi-bin/webscr';	//Truely env
		}else{
			$this->_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';	//Test environment sandbox
		}
	}
	
	/*
	*	@Main function
	*/
	function run(){
		
		$postFiles = 'cmd=_notify-validate';
		
		foreach($_POST as $k=>$v){
			$postFiles .= "&$k=".urlencode($v);
		}
	
		$curl = curl_init();
		
		curl_setopt_array($curl,array(
			CURLOPT_URL => $this->_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $postFiles
		));

		$result = curl_exec($curl);
		$res = $this->doExecute($result);	//执行处理结果
		curl_close($curl);

		if ($res)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/*
	*	@ doExecute()处理最终结果
	*/
	private function doExecute($result)
	{
		if(strcmp($result,'VERIFIED') == 0){
			$getTotalGross 			= $_POST['mc_gross'];				// 交易金额
			$getTxnId   			= $_POST['txn_id'];					// 完成页验证信息
			$getSN  				= $_POST['invoice'];				// 订单SN
			$getOrderId				= $_POST['custom'];					// 订单ID
			$getReciverEmail 		= $_POST["receiver_email"];  		// 接受人Email
			$getPaymentStatus 		= $_POST["payment_status"]; 		// "Completed"

			
			// 判断付款状态
			if (strcmp($getPaymentStatus, 'Completed') == 0 || strcmp($getPaymentStatus, 'Pending') == 0)
			{
				// 当付款完成时
				$model = M('orders');
				$modelConfig = M('sys_config');
				$configRes = $modelConfig->where("cname = 'paypal_email'")->find();

				if ($configRes['cvalue'] != $getReciverEmail)
				{
					return TRUE;
				}

				$orderInfo = $model->field('order_sn, deail_price, pay_status')->find($getOrderId);
				if (strcmp($orderInfo['order_sn'], $getSN) == 0 && $orderInfo['deail_price'] == $getTotalGross && $orderInfo['pay_status'] != 1)
				{
					// 执行成功
					$data['pay_status'] = 1;
					$data['order_status'] = 2;
					$affectedRows = $model->where("id = '{$getOrderId}'")->data($data)->save();
					return TRUE;
				}
			}
			return TRUE;
		}else{
			//未通过认证，有可能是编码错误或非法的 POST 信息 
			// $this->error_msg = 'Invalid';
			return FALSE;
		}
	}
}