<?php
class Liveforms_Payment{
    
    var $Processor;
     
    function Liveforms_Payment(){
        
    }
    
    function InitiateProcessor($MethodID){                     
        $MethodClass = $MethodID;               
        $this->Processor = new $MethodClass();        
    }
    
    function ProcessPayment(){
        
    }
    
    function ListMethods() {
         global $wpdb;
         $methods = $wpdb->get_results("select * from {$wpdb->prefix}mp_payment_methods where enabled='1'",ARRAY_A);                  
         return $methods;
    } 
    
    function CountMethods(){
         global $wpdb;
         return $wpdb->get_var("select count(*) from {$wpdb->prefix}mp_payment_methods where enabled='1'");                  
    }
    
    function PaymentMethodDropDown(){
        $methods = $this->ListMethods();
        if(count($methods)>1){
        foreach($methods as $method){
            $html .= "<option value='{$method[class_name]}'>{$method[title]}</option>\r\n";
        }} else
        $html = $method[class_name];
        return $html;
    }
    
    function getMonthOptions(){
            return 
                '<option value="01">January</option>\r\n'.
                '<option value="02">February</option>\r\n'.
                '<option value="03">March</option>\r\n'.
                '<option value="04">April</option>\r\n'.
                '<option value="05">May</option>\r\n'.
                '<option value="06">June</option>\r\n'.
                '<option value="07">July</option>\r\n'.
                '<option value="08">August</option>\r\n'.
                '<option value="09">September</option>\r\n'.
                '<option value="10">October</option>\r\n'.
                '<option value="11">November</option>\r\n'.
                '<option value="12">December</option>\r\n';
    }
    
    function getYearOptions(){
            $start = date("Y");
            $fin = $start + 25;
            $options = "";
            for($i=$start; $i<$fin; $i++){
                $options .='<option value="'.$i.'>'.$i.'</option>\r\n';
            }
            return $options;
        }
       
    
    
    
}


class CommonVers{
    var $Currency = 'USD';
    var $OrderTitle;
    var $Amount;
    var $InvoiceNo;
    var $Settings;
    var $VerificationError;
}

?>