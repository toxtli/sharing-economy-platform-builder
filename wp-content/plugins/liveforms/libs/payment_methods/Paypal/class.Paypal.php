<?php
if(!class_exists('Liveforms_Paypal')){
global $methods_set;
$methods_set['liveforms_paypal'] = 'PayPal';

class Liveforms_Paypal {
    var $TestMode;

    public $GatewayUrl = "https://www.Paypal.com/cgi-bin/webscr";
    public $GatewayUrl_TestMode = "https://www.sandbox.Paypal.com/cgi-bin/webscr";
    var $Business;
    var $ReturnUrl;
    var $NotifyUrl;
    var $CancelUrl;    
    var $Custom;
    var $Enabled;
    var $Currency;
    var $Ship_method;
    var $Ship_amount;
    var $Ship_currency;
    var $order_id;
    
    
    function Liveforms_Paypal($TestMode = 0){ 
        $this->TestMode = $TestMode;                
        if($TestMode==1)
			$this->GatewayUrl = $this->GatewayUrl_TestMode;
                   
        //$this->GatewayUrl = $this->GatewayUrl_TestMode;
    }
    
    
    public function ConfigOptions($fieldprefix, $cache = array()){    
       $enabled = 'checked="checked"';
	   $op_mode = (isset($cache['mode']) ? $cache['mode'] : '');

       $data='
 
<div class="form-group">
<label>'.__("Paypal Mode:","liveform").'&nbsp;&nbsp;<label>
<label><input name="'.$fieldprefix.'[liveforms_paypal][mode]" type="radio" '.($op_mode == "live"?"checked='checked'":"").' value="live" /> Live</label>&nbsp; 
<label><input name="'.$fieldprefix.'[liveforms_paypal][mode]" type="radio" '.($op_mode == "sandbox"?"checked='checked'":"").' value="sandbox" /> Test</label>
</div>
<div class="form-group">
<label for="pemail">PayPal Email:</label>
<input class="form-control" type="text" name="'.$fieldprefix.'[liveforms_paypal][email]" value="'.(isset($cache['email']) ? $cache['email'] : "").'" />
</div>
<div class="form-group">
<label for="pemail">Notify URL:</label>
<input class="form-control" type="text" name="'.$fieldprefix.'[liveforms_paypal][notify_url]" value="'.(isset($cache['notify_url']) ? $cache['notify_url'] : '').'" />
</div>
<div class="form-group">
<label for="pemail">Return URL:</label>
<input class="form-control" type="text" name="'.$fieldprefix.'[liveforms_paypal][return_url]" value="'.(isset($cache['return_url']) ? $cache['return_url'] : "").'" />
</div>
 
';
        return $data;
    }
    
	/**
	 * Payment form for Paypal. Fired after form submission 
	 * @param type $submission : Holds submitted form related data
	 * @param type $AutoSubmit : Whether or not redirect to the payment gateway automatically
	 * @return string : HTML for the Form for Paypal
	 */
    function ShowPaymentForm($submission, $AutoSubmit = 0){
        if($AutoSubmit==1) $hide = "display:none;'";
        $Paypal = plugins_url().'/wpdm-premium-packages/images/Paypal.png';
		$invoice_no = uniqid();
		if ($submission['methodparams']['mode'] == 'sandbox') {
			$this->GatewayUrl = $this->GatewayUrl_TestMode;
		}
		$amount = number_format($submission['amount'], 2); // two decimal points
		$custom = base64_encode($submission['extraparams'].'|'.$submission['methodparams']['Paypal_mode']);
        $email = $submission['methodparams']['email'] != ''?$submission['methodparams']['email']:'payment@liveforms.org';
        $returnurl = $submission['methodparams']['return_url'] != ''?$submission['methodparams']['return_url']:home_url('/');
        $cancelurl = $submission['methodparams']['cancel_url'] != ''?$submission['methodparams']['cancel_url']:home_url('/');
        $notify_url = add_url_fragment( get_site_url(), array('paymethod' => 'Liveforms_paypal') );
        $Form = " 
                    <form method='post' style='margin:0px;' name='_wpdm_bnf_{$invoice_no}' id='_wpdm_bnf' target='_parent' action='{$this->GatewayUrl}'>

                    <input type='hidden' name='business' value='{$email}' />

                    <input type='hidden' name='cmd' value='_xclick' />
                    <!-- the next three need to be created -->
                    <input type='hidden' name='return' value='{$returnurl}' />
                    <input type='hidden' name='cancel_return' value='{$cancelurl}' />
                    <input type='hidden' name='notify_url' value='{$notify_url}' />
                    <input type='hidden' name='rm' value='2' />
                    <input type='hidden' name='currency_code' value='{$submission['currency']}' />
                    <input type='hidden' name='lc' value='US' />
                    <input type='hidden' name='bn' value='toolkit-php' />

                    <input type='hidden' name='cbt' value='Continue' />
                    
                    <!-- Payment Page Information -->
                    <input type='hidden' name='no_shipping' value='' />
                    <input type='hidden' name='no_note' value='1' />
                    <input type='hidden' name='cn' value='Comments' />
                    <input type='hidden' name='cs' value='' />
                    
                    <!-- Product Information -->
                    <input type='hidden' name='item_name' value='' />
                    <input type='hidden' name='amount' value='{$amount}' />

                    <input type='hidden' name='quantity' value='1' />
                    <input type='hidden' name='item_number' value='{$invoice_no}' />
                    <input type='hidden' name='email' value='' />
                    <input type='hidden' name='custom' value='{$custom}' />
                    
                    <!-- Shipping and Misc Information -->
                     
                    <input type='hidden' name='invoice' value='{$invoice_no}' />

                    <noscript><p>Your browser doesn't support Javscript, click the button below to process the transaction.</p>
                    <a style=\"{$hide}\" href=\"#\" onclick=\"jQuery('#_wpdm_bnf').submit();return false;\">Buy Now&nbsp;<img align=right alt=\"Paypal\" src=\"$Paypal\" /></a>                    </noscript>
                    </form>
         
        
        ";
        
        if($AutoSubmit==1)
        $Form .= "Proceeding to Paypal....<script language=javascript>setTimeout('jQuery(\"#_wpdm_bnf\").submit();',2000);</script>";
        
        return $Form;
        
        
    }
    
    
    function VerifyPayment() {

          // parse the Paypal URL
		if ($this->params == 'sandbox')
          $this->GatewayUrl = $this->GatewayUrl_TestMode;        

          $url_parsed=parse_url($this->GatewayUrl);        

          // generate the post string from the _POST vars aswell as load the
          // _POST vars into an arry so we can play with them from the calling
          // script.
          //print_r($_POST);
          
          $this->InvoiceNo = esc_attr($_POST['invoice']);
          
          $post_string = '';    
          foreach ($_POST as $field=>$value) { 
             $this->ipn_data["$field"] = $value;
             $post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
          }
          $post_string.="cmd=_notify-validate"; // append ipn command


         if(function_exists('curl_init')){
             $ch = curl_init($this->GatewayUrl);
             curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
             curl_setopt($ch, CURLOPT_POST, 1);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
             curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
             curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
             $this->ipn_response = curl_exec($ch);
             curl_close($ch);

           } else {

          // open the connection to Paypal
          $fp = fsockopen($url_parsed[host],"80",$err_num,$err_str,30); 
          if(!$fp) {
              
             // could not open the connection.  If loggin is on, the error message
             // will be in the log.
             $this->last_error = "fsockopen error no. $errnum: $errstr";
             $this->log_ipn_results(false);       
             return false;
             
          } else { 
     
             // Post the data back to Paypal
             fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
             fputs($fp, "Host: $url_parsed[host]\r\n"); 
             fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
             fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
             fputs($fp, "Connection: close\r\n\r\n"); 
             fputs($fp, $post_string . "\r\n\r\n"); 

             // loop through the response from the server and append to variable
             while(!feof($fp)) { 
                $this->ipn_response .= fgets($fp, 1024); 
             } 

             fclose($fp); // close connection

          }}

		  return $this->ipn_response;

          if (strpos($this->ipn_response, "ERIFIED")) {
      
             // Valid IPN transaction.             
             return true;       
             
          } else {
      
             // Invalid IPN transaction.  Check the log for details.
             $this->VerificationError = 'IPN Validation Failed.';             
             return false;
         
      }
      
   }
   
   function VerifyNotification($method_added_params = ''){
       
       if($_POST){
           $this->order_id = esc_attr($_POST['invoice']);
		   $submission_id = esc_attr($_POST['custom']);
			$this->params = $method_added_params;
           return $this->VerifyPayment();
       }
       else die("Problem occured in payment.");
   }

	public function GetExtraParams() {
	   if (isset($_POST) && isset($_POST['custom'])) {
		   $custom_params = explode('|',base64_decode(esc_attr($_POST['custom'])));
		  return $custom_params[0];
	   }
	   return null;
   }

   public function GetCustomVars() {
	   if (isset($_POST) && isset($_POST['custom'])) {
		   $custom_params = explode('|',base64_decode(esc_attr($_POST['custom'])));
		  return $custom_params[1];
	   }
	   return null;
   }
    
    
}
}
?>