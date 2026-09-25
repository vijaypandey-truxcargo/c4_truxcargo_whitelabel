<?php

namespace App\Controllers;

use App\Models\SupportModel;

class Jobstatus extends BaseController
{
    protected $supportmodel;

    public function __construct()
    {
        helper(['url', 'form', 'common', 'download']);
        $this->supportmodel = new SupportModel();
    }
    public function index(){  
      $num = $this->supportmodel->getRows('order_waybills',array('status'=>'Processing')); 
      if($num>0){  
        $data = $this->supportmodel->select_rows_limit('order_waybills','id,job_id,panel','Desc','30',array('status'=>'Processing'));  
        foreach ($data as $data){  
           $id = $data->id;
           $job_id = $data->job_id;
           $api = $this->supportmodel->search_col('b2b-partner','jwt',array('title'=>$data->panel)); 
           $token = $api->jwt;
           $result = $this->job_status($job_id, $token);    
         
          if(!empty($result) && $result['status']=='Complete' &&  $result['lrnum']!='R'){
            $post['lrnum'] = $result['lrnum'];
            $post['status'] = $result['status'];
            $post['waybills'] = $result['waybills']; 
            $this->supportmodel->update('order_waybills',$post, $id);          
            $reason ='Debit For Order Creation LR No : '.$post['lrnum'];
            $this->supportmodel->update_condition('wallet',array('reason'=>$reason), array('order_id'=>$job_id));
            $this->supportmodel->update('xprestoken',array('token'=>'jobstatus '.date('Y-m-d h:i:s')), 5);
          }
          if($result['lrnum']=='D' || $result['lrnum']=='R'){
                 $this->supportmodel->delete('order_waybills', $id);
                 $this->supportmodel->delete_condition('wallet', array('order_id'=>$job_id));
          }
        }
      } 
    }
    

   public function token_generate(){
      $apiurl='https://ltl-clients-api.delhivery.com/ums/login';
      $cond = "title like '%Delhivery%'";
       $detail = $this->supportmodel->select_rows('b2b-partner','username,password,id','ASC',$cond);
       foreach($detail as $detail){
          $data = array('username'=>$detail->username, 'password'=>$detail->password);
          $data_json = json_encode($data);
          $output = $this->curl_post($apiurl, '',$data_json);
        //      echo '<pre>';
        //  print_r($output);
           if(array_key_exists("success",$output) && $output['success']==1){
                 echo '<br>'.$detail->username.'<br>';
          echo   $jwt= $output['data']['jwt'];
             $this->supportmodel->update('b2b-partner',array('jwt'=>$jwt),$detail->id);
           }
           else { echo '<br>'.$detail->username.'<br>';}
      }
       $this->supportmodel->update('xprestoken',array('token'=>'token_generate '.date('Y-m-d h:i:s')), 8);
      $this->bluedart_token();
    }
    
     public function bluedart_token(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://apigateway.bluedart.com/in/transportation/token/v1/login',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array('ClientID: B59yOhVJuF9mEs9HqtODmDzGLrLcAGRG', 'clientSecret: ni134hGOiv6e1g5Z'),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $output = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true );  
        echo  $jwt= $output['JWTToken'];  
        //  $this->supportmodel->update_condition('b2b-partner',array('jwt'=>$jwt),"title like '%Bluedart%'"); 
        $this->supportmodel->update_condition( 'b2b-partner', array('jwt' => $jwt), "(title LIKE '%Bluedart%' OR title LIKE '%BLUEDART%')" ); 
    }
    public function label_expire(){ 
        $data = $this->supportmodel->select_rows('lr_lable', 'id,date','ASC');
        foreach($data as $data){
            $dayinpass = $data->date;
            $today = time();
            $dayinpass= strtotime($dayinpass);
            $min = round(abs($today - $dayinpass) / 60,2);
            if($min>60){
                $this->supportmodel->delete('lr_lable', $data->id);
            }
        }
    }
     public function pdf_request() {  
        $data = $this->supportmodel->select_rows('lr_lable', '*','ASC',"pdf=''");
        foreach($data as $data){
            $lrnum=$data->lrnum;
            $size= $data->size;
            $order = $this->supportmodel->search_col('order_waybills','panel',array('lrnum'=>$lrnum));
            $panel=$order->panel;
            $api = $this->supportmodel->search_col('b2b-partner','jwt',array('title'=>$panel)); 
            $token = $api->jwt;
            $accesstoken = 'Bearer '.$token;
            $uri ='https://ltl-clients-api.delhivery.com/generate/shipping_label/status/'.$data->job_id;
            $res = $this->curl_get($uri, $accesstoken); 
            if(array_key_exists("success",$res) && $res['success']){ 
               if($res['data']['job_status']=='success'){
                   $pdf= $res['data']['presigned_url'];
                   $this->supportmodel->update_condition('lr_lable',array('pdf'=>$pdf),array('job_id'=>$data->job_id));
               }
            } 
        }
    }
    public function smartr_token(){      
        $res = $this->supportmodel->search_col('b2b-partner','username,password,id',"title like '%Smartr%'");
        $url='http://api.smartr.in/api/v1/get-token/';
        $data = array('username'=>$res->username, 'password'=>$res->password);
        $data_json = json_encode($data);              
        $output = $this->curl_post($url, '',$data_json);   
        if($output['success']){
          echo  $jwt= $output['data']['access_token'];  
            $this->supportmodel->update('b2b-partner',array('jwt'=>$jwt),$res->id);
        } 
        else {echo $res->username;}
    }
    
    public function xpress_token(){
        $url='https://shipment.xpressbees.com/api/users/login';
        $detail = $this->supportmodel->select_rows('partner','username,password','ASC',"partner like '%Xpressbees%' and client='Prepaid'");   
        foreach($detail as $detail){  
            $data= array('email'=>$detail->username,'password'=>$detail->password);
            $data_json = json_encode($data);              
            $output = $this->curl_post($url, '',$data_json); 
            $jwt = $output['data'];  
            $this->supportmodel->update_condition('xprestoken',array('token'=>$jwt),array('title'=>$detail->username));
       }
        $this->xpress_token1();
    }
    
    public function xpress_token1(){
        $url='https://userauthapis.xbees.in/api/auth/generateToken';
        $detail = $this->supportmodel->select_rows('partner','username,password,api','ASC',"partner like '%Xpressbees%' and client='Postpaid'");   
        foreach($detail as $detail){             
          $data= array('username'=>$detail->username,'password'=>$detail->password,'secretkey'=>$detail->api);
          $data_json = json_encode($data);              
          $output = $this->curl_post($url, '',$data_json);  
          $jwt = $output['token'];
          $this->supportmodel->update_condition('xprestoken',array('token'=>$jwt),array('title'=>$detail->username));
        }
        $this->indiapost_token();
    }
   
    public function indiapost_token(){   
        $detail = $this->supportmodel->search_col('partner','username,password',"partner like '%India Post%'");   
        $url='https://gateway.cept.gov.in/auth/keycloak/token';
        $data = array('username'=>$detail->username,'password'=>$detail->password);
        $data_json = json_encode($data);              
        $output = $this->curl_post($url, '',$data_json);  
        $jwt= $output['access_token'];
        $this->supportmodel->update_condition('xprestoken',array('token'=>$jwt),array('title'=>$detail->username));
    }
    
    public function amazon_token(){
       $wconfig = $this->supportmodel->find_col('config','client_id,refresh_token,client_secret',1);  
       $header = array();
       $header[] = 'Content-type: application/x-www-form-urlencoded';
       $curl = curl_init(); 
       curl_setopt_array($curl, array(
       CURLOPT_URL => 'https://api.amazon.com/auth/o2/token',
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'POST',
       CURLOPT_POSTFIELDS => 'grant_type=refresh_token&refresh_token='.$wconfig->refresh_token.'&client_id='.$wconfig->client_id.'&client_secret='.$wconfig->client_secret,
       CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded')
       )); 
       $response = curl_exec($curl);
       $out = json_decode($response); 
       print_r($out);
       $jwt = $out->access_token;  
       $this->supportmodel->update('xprestoken',array('token'=>$jwt),array('title'=>'Amazon'));
    }
    
    public function ekart_token(){   
       $detail = $this->supportmodel->select_rows('partner','client,api','ASC',"partner like '%Ekart%'");   
        foreach($detail as $detail){  
          $curl = curl_init();
          curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.ekartlogistics.com/auth/token',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_HTTPHEADER => array('HTTP_X_MERCHANT_CODE: '.$detail->client, 'Authorization: Basic '.$detail->api),
          ));
          $response = curl_exec($curl);
          curl_close($curl);
          $out = json_decode($response);  
          $jwt = $out->Authorization; 
          $this->supportmodel->update_condition('xprestoken',array('token'=>$jwt),array('title'=>$detail->client));
        }
    }
    
    public function maruti_token(){
        $detail = $this->supportmodel->search_col('partner','username,password',"partner like '%Ekart%'");   
        $url ='https://apis.delcaper.com/auth/login';
        $data= array('email'=>$detail->username,'password'=>$detail->password,'vendorType'=>'SELLER');
        $data_json = json_encode($data);              
        $output = $this->curl_post($url, '',$data_json);  
        if($output['status']==200){
          $jwt= $output['data']['accessToken'];  
          $this->supportmodel->update_condition('xprestoken',array('token'=>$jwt),array('title'=>$detail->username));
       }       
    }
    
    public function user_type(){
        $user1 = $this->supportmodel->select_rows('registration', 'id','ASC',array('status'=>1) );
        $dead =$rare = $active = 0;
        foreach($user1 as $user1){
          $b2b = $this->supportmodel->getRows('order_waybills', array('awb_status!='=>'Not Picked','login_id'=>$user1->id)); 
          $b2c = $this->supportmodel->getRows('b2c_waybills', array('status!='=>'Not Picked','login_id'=>$user1->id)); 
          
          if($b2b<10 && $b2c<10 ){$dead=$dead+1; }
          else if($b2b>100 || $b2c>100){ $active = $active+1; }
          else {$rare=$rare+1;}
        }
        $data['dead'] = $dead;
        $data['rare'] = $rare;
        $data['active'] = $active;  
        $this->supportmodel->update('customer_type', $data,2); 
    }
    
    public function status($log=0) {
         if(!empty($log)){$condition= "lrnum='".$log."'";}
         else { $condition = " awb_status NOT IN ('Delivered','Not Picked','RTO','LOST') and status='Complete'";}
        $data = $this->supportmodel->select_rows('order_waybills','id,login_id,lrnum,awb_status,waybills,delivered,panel,d_mode,total,lastpay,pickupDate,topay,ftopay,pickup,cod,gst','asc' ,$condition);  
        foreach ($data as $view){
            $user =$this->supportmodel->find_col('registration','wallet,gstpercentage',$view->login_id);
            $wallet_type = $user->wallet;  
            $id = $view->id;
            $awb_status = $status= $view->awb_status;
            $total = $view->total;
            $topay = $view->topay;
            $ftopay = $view->ftopay;
            $panel = $view->panel; 
            $post=array();
            $output= $this->curl_get(base_url("api/truxapi/tracking/{$view->lrnum}"), '');
            if(!empty($output) && $output['status']){
              echo $view->lrnum;   echo ' - '.$view->awb_status.' - '; echo  $status= $output['data']['status'];  echo '<br>'; 
                if(sizeof($output['data']['scaninfo'])>0){
                   $post['edd'] = date('d-m-Y',strtotime($output['data']['promisedDate']));
                   $post['remark'] = $output['data']['scaninfo'][0]['remark'];
                   $post['statusLocation'] = $output['data']['scaninfo'][0]['location'];
                    $post['statusDate'] = date('d-m-Y',strtotime($output['data']['scaninfo'][0]['date']));
                  if($status=='Delivered' && !empty($output['data']['deliveredDate'])  && date('Y-m-d',strtotime($output['data']['deliveredDate']))!='1970-01-01'  && empty($view->delivered)){ $post['delivered'] = date('Y-m-d',strtotime($output['data']['deliveredDate']));}
                  if(!empty($output['data']['pickupDate']) && empty($view->pickupDate)){$post['pickupDate'] = $output['data']['pickupDate'];}
                  else if(!empty($output['data']['pickupDate']) && !empty($view->pickupDate) && $view->pickupDate!=$output['data']['pickupDate']){ $post['pickupDate'] = $output['data']['pickupDate'];}
                }
                if($awb_status!=$status){ 
                  $post['awb_status'] = $status;
                  if($awb_status=='Not Picked' && $wallet_type=='Prepaid'){
                    $wnum = $this->supportmodel->getRows('wallet',"reason like '%Refund For Order Cancellation LR No : ".$view->lrnum."%'");
                    $wnum1 = $this->supportmodel->getRows('wallet',"reason like '%Debit For Order Connect Again LR No : ".$view->lrnum."%'");
                    if($wnum==1 && $wnum1==0){ 
                        $wal['login_id']=$view->login_id;
                        $wal['amount']= '-'.$total;
                        $wal['reason']=  'Debit For Order Connect again LR No : '.$view->lrnum;
                        $wal['date']= date('Y-m-d H:i:s');
                        $wal['status']= 'Confirm';
                        $wal['add_by']= 'Auto';
                        $this->supportmodel->insert('wallet',$wal);
                    }
                  } 
                  if(($post['awb_status']=='Returned' || $post['awb_status']=='RTO') && !empty($total) && $wallet_type=='Prepaid'){
                    if($view->d_mode=='CoD'){
                        $post['cod']=0;
                        $new_amt= $view->lastpay - $view->gst - $view->cod;
                        $post['gst'] = $new_amt*$user->gstpercentage/100;
                        $post['lastpay']= $post['total']= $new_amt + $post['gst']; 
                        $total=$post['total'];
                    }
                   
                    if($topay=='Yes' || $ftopay=='Yes'){$debit = 2*$total;}
                    else{$debit=$total- $view->cod - $view->cod*$user->gstpercentage/100;}
                 
                    if($panel=='Rivigo'){$debit = $debit + 150;}
                   $num = $this->supportmodel->getRows('wallet',array('reason'=>'Debit For Order Return LR No : '.$view->lrnum));
                   $amt=0;
                   $num1 = $this->supportmodel->getRows('weight_recon',"awb='$view->lrnum' and status!='Close'");
                   if($num1>0){
                      $amt = str_replace('-','',$this->supportmodel->search_col('wallet','amount',array('reason'=>'Debit For Weight Reconciliation of LR No : '.$view->lrnum))->amount);
                   }
                   $debit = $debit +$amt;
                   if($debit>0 && $num==0){
                     $wal['login_id']=$view->login_id;
                     $wal['amount']= '-'.$debit;
                     $wal['reason']=  'Debit For Order Return LR No : '.$view->lrnum;
                     $wal['date']= date('Y-m-d H:i:s');
                     $wal['status']= 'Confirm';
                     $wal['add_by']= 'Auto';
                     $this->supportmodel->insert('wallet',$wal);                      
                   }       
                 }
                 if($post['awb_status']=='Not Picked' && !empty($total) && $topay!='Yes' && $ftopay!='Yes'  && $wallet_type=='Prepaid'){
                  $wal['reason']=  'Refund For Order Cancellation LR No : '.$view->lrnum;
                  $rr = 'Debit For Order Creation LR No : '.$view->lrnum;
                  $cnum = $this->supportmodel->getRows('wallet',array('reason'=>$rr));
                  $rnum = $this->supportmodel->getRows('wallet',array('reason'=>$wal['reason']));
                  if($cnum==1 && $rnum==0){ 
                    $wal['date']= date('Y-m-d H:i:s');
                    $wal['status']= 'Confirm';
                    $wal['add_by']= 'Auto';                    
                    $wal['login_id']=$view->login_id;
                    $wal['amount']= $total;                
                    $this->supportmodel->insert('wallet',$wal);                    
                 }
               }
             } 
             if(!empty($post)){$this->supportmodel->update('order_waybills',$post,$id);}
             if($view->awb_status=='Manifested' && $status=='In Transit'){$status='Picked';}
             if($status=='Dispatched' || $status=='Picked'){ $this->sms($id,$status); } 
            }  
        }
        $this->supportmodel->update('xprestoken',array('token'=>'status '.date('Y-m-d h:i:s')), 6);
    }
     
    public function b2c_status($awb=''){
        if(!empty($awb)){ $condition = "waybill='".$awb."'";}
        else { $condition = "status NOT IN ('Not Picked','Delivered','Cancel','RTO','LOST') and ship_with>16 and ship_with!='46'";}
        $data = $this->supportmodel->select_rows('b2c_waybills','id,login_id,status,ship_with,waybill,topay,ftopay,ship_with,pickup,city,order_pk,payment_mode,cod_amount,rto,total', 'ASC' ,$condition);  
        foreach ($data as $view){
            $user = $this->supportmodel->find_col('registration','b2c_wallet,gstpercentage',$view->login_id);
            $wallet_type = $user->b2c_wallet;  
            $id = $view->id;
            $status = $view->status;
            $ship_with = $view->ship_with;
            $waybill = $view->waybill;
            $topay = $view->topay;
            $ftopay = $view->ftopay;
            $partner = $this->supportmodel->find_col('partner','percentage,charge',$ship_with);      
            $status1= $view->status;
            $post =array();
           
            $output= $this->curl_get(base_url("api/truxapi/tracking/{$waybill}"), ''); 
            if(!empty($output) && $output['status']){
                $status= $output['data']['status'];
                if(sizeof($output['data']['scaninfo'])>0){
                   $post['remark'] = $output['data']['scaninfo'][0]['remark'];
                   $post['statusLocation'] = $output['data']['scaninfo'][0]['location']; 
                   $post['statusDate'] = date('d-m-Y',strtotime(explode(',',$output['data']['scaninfo'][0]['date'])[0]));
                   if(!empty($output['data']['pickupDate']) && empty($view->pickupDate) && $status!='Manifested' && $status!='Not Picked'){ $post['pickupDate'] = $output['data']['pickupDate'];}
                   else if($status=='Manifested' || $status=='Not Picked'){$post['pickupDate'] ='';}
                   if($post['remark']=='Seller cancelled the order (UD)'){$status='Not Picked';}
                }
                if($status!=$status1){
                  $post['status'] = $status;
                  echo  $waybill.' '.$status.'<br>';
                  
                  if($wallet_type=='Prepaid'){  
                    if($status!='RTO' && $view->status=='RTO'){ 
                        $reason1=  'Debit For Order Return AWB NO : '.$waybill;
                        $this->supportmodel->delete_condition('wallet',array('reason'=>$reason1));
                    }
                    
                    if($status=='RTO' || $status=='Returned'){
                        if($view->payment_mode=='Prepaid'){$minus=0;}
                        else{
                            $cod = $view->cod_amount*$partner->percentage/100;
                            if($cod>$partner->charge){$minus=$cod;}else{$minus=$partner->charge;}
                        }
                        $rto = $view->rto + $view->rto*$user->gstpercentage/100 - $minus;
               
                       if($view->ftopay=='Yes' || $view->topay=='Yes'){$rto = $view->total + $rto;}
                       $wal['reason']=  'Debit For Order Return AWB NO : '.$waybill;
                       $num = $this->supportmodel->getRows('wallet',array('reason'=>$wal['reason']));
                       $num1 = $this->supportmodel->getRows('weight_recon',"awb='$waybill' and status!='Close'");
                       $amt=0;
                       if($num1>0){
                           $amt = str_replace('-','',$this->supportmodel->search_col('wallet','amount',array('reason'=>'Debit For Weight Reconciliation of AWB No : '.$waybill))->amount);
                        }
                        $rto = $rto +$amt;
                       
                       if($rto>0 && $num==0 && $wallet_type=='Prepaid'){ 
                           $wal['login_id'] = $view->login_id;
                           $wal['amount']= '-'.$rto; 
                           $wal['date']= date('Y-m-d H:i:s');
                           $wal['status']= 'Confirm';
                           $wal['add_by']= 'Auto';
                           $this->supportmodel->insert('wallet',$wal);
                        }
                    }
                    if($status=='Not Picked' && $topay!='Yes' && $ftopay!='Yes'){
                        $wal['reason']=  'Refund For Order cancellation AWB NO : '.$waybill;
                        $num = $this->supportmodel->getRows('wallet',array('reason'=>$wal['reason']));
                        if($num==0){
                            $wal['login_id']=$view->login_id;
                            $wal['amount']= $view->total;
                            $wal['date']= date('Y-m-d H:i:s');
                            $wal['status']= 'Confirm';
                            $wal['add_by']= 'Auto';
                            $this->supportmodel->insert('wallet',$wal); 
                       }
                       $this->supportmodel->delete_condition('wallet',array('reason'=>'Debit For Order Connect Again AWB NO :'.$waybill));
                        $this->supportmodel->delete_condition('wallet',array('reason'=>'Debit For Order Return AWB NO : '.$waybill));
                    }
                    if($view->status=='Not Picked' && $topay!='Yes' && $ftopay!='Yes'){
                       $reason = 'Refund For Order cancellation AWB NO : '.$waybill;
                       $rcon = "reason like '%".$reason."%'";
                       $wnum = $this->supportmodel->getRows('wallet',$rcon);
                       $wal['reason']=  'Debit For Order Connect Again AWB NO :'.$waybill;
                       $wnum1 = $this->supportmodel->getRows('wallet',array('reason'=>$wal['reason'],'login_id'=>$view->login_id));
                       if($wnum==1 && $wnum1==0){
                         $wal['login_id']=$view->login_id;
                         $wal['amount']= '-'.$view->total;
                         $wal['date']= date('Y-m-d H:i:s');
                         $wal['status']= 'Confirm';
                         $wal['add_by']= 'Auto';  
                         $this->supportmodel->insert('wallet',$wal);
                       }  
                    }
                  }
                  if($status1=='Manifested' && $status=='In Transit'){$status='Picked';}
                   if($status=='Dispatched' || $status=='Picked'){ $this->sms_b2c($id,$status); }
              }
              if(!empty($post)){  $this->supportmodel->update('b2c_waybills',$post,$id);}
            }    
        }
        $this->supportmodel->update('xprestoken',array('token'=>'b2c_status'.date('Y-m-d h:i:s')), 7);
    }
    
    public function b2c_easyecom(){
        $condition = "status NOT IN ('Not Picked','Delivered','Cancel','RTO','LOST') and channel='Easyecom'";
        $data = $this->supportmodel->select_rows('b2c_waybills','id,login_id,status,ship_with,waybill,topay,ftopay,ship_with,pickup,city,order_pk,payment_mode,cod_amount,rto,total', 'ASC' ,$condition);  
        foreach ($data as $view){
            $user = $this->supportmodel->find_col('registration','b2c_wallet,gstpercentage',$view->login_id);
            $wallet_type = $user->b2c_wallet;  
            $id = $view->id;
            $status = $view->status;
            $ship_with = $view->ship_with;
            $waybill = $view->waybill;
            $topay = $view->topay;
            $ftopay = $view->ftopay;
            $partner = $this->supportmodel->find_col('partner','percentage,charge',$ship_with);      
            $status1= $view->status;
            $post =array();
             $output= $this->curl_get(base_url("api/truxapi/tracking/{$waybill}"), ''); 
            if(!empty($output) && $output['status']){
                $status= $output['data']['status'];
                if(sizeof($output['data']['scaninfo'])>0){
                   $post['remark'] = $output['data']['scaninfo'][0]['remark'];
                   $post['statusLocation'] = $output['data']['scaninfo'][0]['location']; 
                   $post['statusDate'] = date('d-m-Y',strtotime(explode(',',$output['data']['scaninfo'][0]['date'])[0]));
                   if(!empty($output['data']['pickupDate']) && empty($view->pickupDate) && $status!='Manifested' && $status!='Not Picked'){ $post['pickupDate'] = $output['data']['pickupDate'];}
                   else if($status=='Manifested' || $status=='Not Picked'){$post['pickupDate'] ='';}
                   
                   if($post['remark']=='Seller cancelled the order (UD)'){$status='Not Picked';}
                }
                if($status!=$status1){
                  $post['status'] = $status;
                  
                  if($wallet_type=='Prepaid'){   
                   
                    if($status!='RTO' && $view->status=='RTO'){ 
                        $reason1=  'Debit For Order Return AWB NO : '.$waybill;
                        $this->supportmodel->delete_condition('wallet',array('reason'=>$reason1));
                    }
                    
                    if($status=='RTO' || $status=='Returned'){
                        if($view->payment_mode=='Prepaid'){$minus=0;}
                        else{
                            $cod = $view->cod_amount*$partner->percentage/100;
                            if($cod>$partner->charge){$minus=$cod;}else{$minus=$partner->charge;}
                        }
                        $rto = $view->rto + $view->rto*$user->gstpercentage/100 - $minus;
               
                       if($view->ftopay=='Yes' || $view->topay=='Yes'){$rto = $view->total + $rto;}
                       $wal['reason']=  'Debit For Order Return AWB NO : '.$waybill;
                       $num = $this->supportmodel->getRows('wallet',array('reason'=>$wal['reason']));
                       $num1 = $this->supportmodel->getRows('weight_recon',"awb='$waybill' and status!='Close'");
                       $amt=0;
                       if($num1>0){
                           $amt = str_replace('-','',$this->supportmodel->search_col('wallet','amount',array('reason'=>'Debit For Weight Reconciliation of AWB No : '.$waybill))->amount);
                        }
                        $rto = $rto +$amt;
                       
                       if($rto>0 && $num==0 && $wallet_type=='Prepaid'){ 
                           $wal['login_id'] = $view->login_id;
                           $wal['amount']= '-'.$rto; 
                           $wal['date']= date('Y-m-d H:i:s');
                           $wal['status']= 'Confirm';
                           $wal['add_by']= 'Auto';
                           $this->supportmodel->insert('wallet',$wal);
                        }
                    }
                    if($status=='Not Picked' && $topay!='Yes' && $ftopay!='Yes'){
                        $wal['reason']=  'Refund For Order cancellation AWB NO : '.$waybill;
                        $num = $this->supportmodel->getRows('wallet',array('reason'=>$wal['reason']));
                        if($num==0){
                            $wal['login_id']=$view->login_id;
                            $wal['amount']= $view->total;
                            $wal['date']= date('Y-m-d H:i:s');
                            $wal['status']= 'Confirm';
                            $wal['add_by']= 'Auto';
                            $this->supportmodel->insert('wallet',$wal); 
                       }
                       $this->supportmodel->delete_condition('wallet',array('reason'=>'Debit For Order Connect Again AWB NO :'.$waybill));
                        $this->supportmodel->delete_condition('wallet',array('reason'=>'Debit For Order Return AWB NO : '.$waybill));
                    }
                    if($view->status=='Not Picked' && $topay!='Yes' && $ftopay!='Yes'){
                       $reason = 'Refund For Order cancellation AWB NO : '.$waybill;
                       $rcon = "reason like '%".$reason."%'";
                       $wnum = $this->supportmodel->getRows('wallet',$rcon);
                       $wal['reason']=  'Debit For Order Connect Again AWB NO :'.$waybill;
                       $wnum1 = $this->supportmodel->getRows('wallet',array('reason'=>$wal['reason'],'login_id'=>$view->login_id));
                       if($wnum==1 && $wnum1==0){
                         $wal['login_id']=$view->login_id;
                         $wal['amount']= '-'.$view->total;
                         $wal['date']= date('Y-m-d H:i:s');
                         $wal['status']= 'Confirm';
                         $wal['add_by']= 'Auto';  
                         $this->supportmodel->insert('wallet',$wal);
                       }  
                    }
                  }
                  if($status1=='Manifested' && $status=='In Transit'){$status='Picked';}
                   if($status=='Dispatched' || $status=='Picked'){ $this->sms_b2c($id,$status); }
              }
              if(!empty($post)){  $this->supportmodel->update('b2c_waybills',$post,$id);}
            }    
        }
    }
       
    public function sms($id,$status) {
        $wconfig = $this->supportmodel->find_col('config','whatsapp',1);  
        $view = $this->supportmodel->find_col('order_waybills','lrnum,customer_id,awb_status,description',$id);  
        $waybill = $view->lrnum;
        $customer_id = $view->customer_id;
        $user = $this->supportmodel->find_col('address','phone,name',$customer_id); 
        $phone = $user->phone;
        $product = $view->description;
        if($phone!='9999999999' && !empty($phone)&&  $phone!='9999999990'){
           $event=$status;
          if(!empty($event) && !empty($wconfig->whatsapp)){
             $params = array(
                'phone'=> '+91'.$phone,
                'event'=>$event,
                'name'=> $user->name,
                'tracking'=> $waybill,
                'product'=> $product
              );
              $data_json = json_encode($params); 
              $wurl = $wconfig->whatsapp;
              $output = $this->curl_post($wurl, '', $data_json); 
          }      
        }
    }
      
    public function sms_b2c($id,$status) {    
        $wconfig = $this->supportmodel->find_col('config','whatsapp',1);  
        
        $view = $this->supportmodel->find_col('b2c_waybills','waybill,phone,products_desc,name',$id);
        $waybill = $view->waybill;
        $phone = $view->phone;
        $product = $view->products_desc;
        $event='';
        if($phone!='9999999999' && !empty($phone)&&  $phone!='9999999990'){
          $event=$status;
          if(!empty($event) && !empty($wconfig->whatsapp)){
            $params = array(
                'phone'=> '+91'.$phone,
                'event'=>$event,
                'name'=> $view->name,
                'tracking'=> $waybill,
                'product'=> $product
              );
              $data_json = json_encode($params); 
              $wurl =$wconfig->whatsapp;
              $output = $this->curl_post($wurl, '', $data_json);  
          }      
        } 
    }
    
    public function token($username,$password){         
        $data = array('username'=>$username, 'password'=>$password);
        $token_key = $this->supportmodel->search_col('b2b-partner','jwt',$data);
        $jwt = $token_key->jwt;
        return $jwt;
     }
     
    public function b2c_invoice($i=0){           
        ////------ Step-1 ----///////
        $b2b = $this->supportmodel->search_col('b2c_billing','invoice_no',"1=1");
        echo $rnum = $b2b->invoice_no;  echo '<br>';
        $start = $post['start_date'] = '2024-11-27';
        $post['end_date']= date("Y-m-d", strtotime("+93 days $start")); 
        
        ////------ Step-2 ----///////   
        $date1=$post['start_date']; $date2= $post['end_date'];
        echo  $rdate1 = date('d-m-Y',strtotime($date1)); echo ' - '; 
        echo $rdate2 = date('d-m-Y',strtotime($date2)); echo '<br>';  
        $customer = $this->supportmodel->show_limit_col('registration','id,gstpercentage,first,last,company,state,pincode,address,gst',300,$i,'DESC',array('gstpre'=>'Yes')); 
       // $customer = $this->supportmodel->select_rows('registration','id,gstpercentage,first,last,company,state,pincode,address,gst','DESC',array('gstpre'=>'Yes'));
        foreach ($customer as $customer){
           $amount=$paid=0;  $waybill = ''; 
           $login_id = $customer->id;
           $condition = "login_id ='".$login_id."' and status IN ('Delivered','RTO') and  STR_TO_DATE(statusDate, '%d-%m-%Y') between STR_TO_DATE('".$rdate1."', '%d-%m-%Y') and STR_TO_DATE('".$rdate2."', '%d-%m-%Y')"; 
           $delivered = $this->supportmodel->select_rows('b2c_waybills','id,status,charged_weight,freight,waybill,payment_mode,topay,ftopay,total,ship_with,rto,cod_amount','ASC',$condition);
           
           foreach($delivered as $delivered){ $total=0;
               $wnum = $this->supportmodel->getRows('weight_recon',array('awb'=>$delivered->waybill));
               $post1['charged_weight'] = $delivered->charged_weight;
               if($wnum>0){
                 $wdata = $this->supportmodel->search_col('weight_recon','charged_weight',array('awb'=>$delivered->waybill));
                 $post1['charged_weight']=  round($wdata->charged_weight);  
               }
                $con  = "login_id='".$login_id."' and reason like '%".$delivered->waybill."%'";
                $sum = str_replace('-','',$this->supportmodel->wallet($con)); 
                $del_num = $this->supportmodel->getRows('wallet',$con); 
                if($delivered->status=='RTO'){
                   $partner = $this->supportmodel->find_col('partner','percentage,charge',$delivered->ship_with);      
                   if($delivered->payment_mode=='Prepaid'){$minus=0;}
                   else{
                       $cod = $delivered->cod_amount*$partner->percentage/100;
                       if($cod>$partner->charge){$minus=$cod;}else{$minus=$partner->charge;}
                   }
                   if($delivered->topay=='Yes' || $delivered->ftopay=='Yes'){$sum= $delivered->total + $delivered->rto + $delivered->rto*$customer->gstpercentage/100 - $minus;}
                   else if(empty($sum)){$sum=0;}  
                   
                   $total = $delivered->total + $delivered->rto + $delivered->rto*$customer->gstpercentage/100 - $minus;
                   if($sum>$total){$total=$sum;}
                }
                else{
                  if($delivered->topay=='Yes' || $delivered->ftopay=='Yes' || $del_num==0){$sum=$delivered->total;}
                  else if(empty($sum)){$sum=0;}
                  if($sum>$delivered->total){$total=$sum;} else {$total = $delivered->total;}
               }
               $dnum =  $this->supportmodel->getRows('b2c_invoice',"awb like '%".$delivered->waybill."%'"); 
               $dnum1 =  $this->supportmodel->getRows('b2c_billing',"awb like '%".$delivered->waybill."%'"); 
               if($dnum==0 && $dnum1==0){    
                    $amount = $amount + $total;  
                    $paid = $paid + $sum; 
                    if(empty($waybill)){$waybill = $delivered->waybill; }
                    else {$waybill = $waybill.','.$delivered->waybill;  }
                    if(round($total,2)!=round($sum,2)){ echo $delivered->waybill.' ->'.$total.' -'.$sum.'<br>';}
                } 
                $post1['lastpay']=  round($total,2);
                $this->supportmodel->update('b2c_waybills',$post1,$delivered->id);
          }    
           
          if($amount>0){  
              $post['start_date'] = '2025-02-16';
              $rnum = $rnum +1;
              $post['login_id'] = $login_id;
              $post['awb'] = $waybill;
              $post['invoice_no'] = $rnum; 
              $post['amount'] = round($amount,2);
              $post['paid'] = round($paid,2);
              $post['unpaid'] = round($amount-$paid,2);
              if($post['unpaid']<=0){$s='Paid';} else{$s='Unpaid';}
              $post['status'] = $s;
              $post['title'] = 'TC/2024-25/';
              $post['company'] = $customer->company;
              $post['state'] = $customer->state;
              $post['address'] = $customer->address.', '.$customer->state.'- '.$customer->pincode;
              $post['gst'] = $customer->gst;
              if($post['unpaid']>0){  print_r($post); echo '<br><br>';}
             $this->supportmodel->insert('b2c_billing',$post);
          }
       } 
    }
   
    public function b2b_invoice($new=0){           
            $b2b = $this->supportmodel->search('b2b_billing');  
            echo  $rnum = $b2b->invoice_no;  echo '<br>';
            $start = $post['start_date'] = '2024-11-25';
            $post['end_date']= date("Y-m-d", strtotime("+95 days $start"));
            
            ////------ Step-2 ----///////  
            echo $date1=$post['start_date'];  echo ' - ';echo $date2= $post['end_date']; echo '<br>'; 
            $rdate1 = date('d-m-Y',strtotime($date1));
            $rdate2 = date('d-m-Y',strtotime($date2));
          
           $customer = $this->supportmodel->show_limit_col('registration','id,gstpercentage,first,last,company,state,pincode,address,gst',400,$new,'DESC' ,array('gstpre'=>'Yes')); 
          //   $customer = $this->supportmodel->select_rows('registration','id,gstpercentage,first,last,company,state,pincode,address,gst','DESC',array('gstpre'=>'Yes'));
            $k=0; 
            foreach ($customer as $customer){ 
               $login_id = $customer->id;
               $condition = "login_id='".$login_id."' and gst>0  and STR_TO_DATE(statusDate, '%d-%m-%Y') between STR_TO_DATE('".$rdate1."', '%d-%m-%Y') and STR_TO_DATE('".$rdate2."', '%d-%m-%Y') and awb_status IN ('Delivered','RTO')"; 
               $order = $this->supportmodel->show_condition('order_waybills','ASC',$condition);
         
               $amount=0;  $lr = ''; $paid=0;   $unpaid=0;  
               foreach ($order as $order){ 
                   if($order->topay=='Yes' && !empty($order->consignee_gst_tin)){$include='No';}
                   else if($order->ftopay=='Yes' && !empty($order->consignee_gst_tin)){$include='No';}
                   else{$include='Yes';}
                   
               if($include=='Yes'){
                  $total = trim($order->lastpay);
                  if($order->awb_status=='Delivered'){$sum = $total;}
                  if($order->awb_status=='RTO'){$sum = 2*$total; if($order->panel=='Rivigo'){$sum = $sum+150;}} 
                  else {$sum = $total;}
                 
                  $con = "login_id='".$order->login_id."' and amount<0 and reason like '%".$order->lrnum."'";
                  $psum = str_replace('-','',$this->supportmodel->wallet($con));
                  if(empty($psum)){$psum=0;}
                 
                  $wnum=   $this->supportmodel->getRows('weight_recon',"awb like '%".$order->lrnum."%'"); 
                  if($wnum>0){
                      $wdata =   $this->supportmodel->search_col('weight_recon','charged_weight',"awb like '%".$order->lrnum."%'");
                      $charged_weight = $wdata->charged_weight;
                      $panel = $order->panel;  
                      $suffix= $this->suffix_date($order->login_id,$order->date);
                      $info = $this->divisor($panel." ".$suffix,$order->login_id); 
                      
                      $res['cweight'] = $charged_weight;
                      $res['freight'] = $charged_weight*$order->rate;
                      $res['fsc'] = $res['freight']*$info->fsc/100;
                      if(!empty($order->fm)){ 
                          $fm_charge = explode(',',$info->fm); 
                          $fm = $charged_weight*$fm_charge[0];
                          if(sizeof($fm_charge)>1 && !empty($fm_charge[1]) && $fm_charge[1]>$fm){$fm=$fm_charge[1];}
                      }
                      else{$fm=0;}
                      if(!empty($order->abc)){ 
                        $abc_charge = explode(',',$info->abc); 
                        $abc = $charged_weight*$abc_charge[0];
                        if(sizeof($abc_charge)>1 && !empty($abc_charge[1]) && $abc_charge[1]>$abc){$abc=$abc_charge[1];}
                      }
                      else{$abc=0;}
                      
                      $res['fm'] = $fm;
                      $res['abc'] = $abc;
                      
                      $last =  $res['freight'] + $res['fsc'] + $order->rov + $order->docket + $order->cod + $order->oda + $fm + $abc + $order->green + $order->handling + $order->lm - $order->discount;
                      $min = $this->mincost($panel." ".$suffix, $order->login_id);
                      
                      if($min>$last){$last= $min;}
        
                      $gst = $last*$customer->gstpercentage/100;
                      $end = round($last,2) + round($gst,2);
                      $res['gst'] = round($gst,2);
                      $res['lrnum'] =$order->lrnum;
                      $res['total'] = $res['lastpay'] = round($end);
                      $this->supportmodel->update('order_waybills',$res,$order->id);
                     
                      if($order->awb_status=='RTO'){$sum = 2*$end;} 
                      else{$sum = $end;}
                  }
                  $inum=   $this->supportmodel->getRows('b2b_invoice',"lr like '%".$order->lrnum."%'");
                  $inum1=   $this->supportmodel->getRows('b2b_billing',"lr like '%".$order->lrnum."%'");
                  if($inum==0 && $inum1==0){ 
                    $amount = $amount + $sum;
                    $paid = $paid + $psum;    
                    if(empty($lr)){$lr = $order->lrnum; } else {$lr = $lr.','.$order->lrnum; }
                 }
              }
            }
            if($amount>0){   $k++;
               $unpaid= round($amount)-round($paid);
               if($unpaid<=0){$status='Paid';} else {$status='Unpaid';}
               echo $login_id.' - amount : '.round($amount).'- paid: '.round($paid).' - unpaid : '.$unpaid.'- status: '.$status.'<br>'; 
               $rnum = $rnum + 1;
               $post['start_date'] = '2025-02-19';
               $post['login_id'] = $login_id;
               $post['lr'] = $lr;
               $post['invoice_no'] = $rnum; 
               $post['amount'] = round($amount);
               $post['paid'] = round($paid);
               $post['unpaid'] = round($unpaid);
               $post['status'] = $status;
               $post['title'] = 'TC/2024-25/';
               $post['due_date']= date("Y-m-d", strtotime("+15 days $date2"));
               $post['company'] = $customer->company;
               $post['state'] = $customer->state;
               $post['address1'] = $customer->address.', '.$customer->state.'- '.$customer->pincode;
               $post['gst'] = $customer->gst;
             $this->supportmodel->insert('b2b_billing',$post);
            }
        }
          echo  '<br>'.$k;
    }
    
    public function invoice_third($new=0){           
        $b2b = $this->supportmodel->search('b2c_billing');  
        echo  $rnum = $b2b->invoice_no;  echo '<br>';
        $start = $post['start_date'] = '2024-11-25';
        $post['end_date']= date("Y-m-d", strtotime("+95 days $start"));
            
        ////------ Step-2 ----///////  
        echo $date1=$post['start_date'];  echo ' - '; echo $date2= $post['end_date']; echo '<br>'; 
        $rdate1 = date('d-m-Y',strtotime($date1));
        $rdate2 = date('d-m-Y',strtotime($date2)); 
        $customer = $this->supportmodel->select_rows('registration','id,gstpercentage,first,last,company,state,pincode,address,gst','DESC',array('gstpre'=>'Yes'));
        //$customer = $this->supportmodel->show_limit_col('registration','id,gstpercentage,first,last,company,state,pincode,address,gst',500,0,'DESC',array('gstpre'=>'Yes')); 
        foreach ($customer as $customer){ 
            $login_id = $customer->id;
            $condition = "login_id='".$login_id."' and gst>0  and STR_TO_DATE(statusDate, '%d-%m-%Y') between STR_TO_DATE('".$rdate1."', '%d-%m-%Y') and STR_TO_DATE('".$rdate2."', '%d-%m-%Y') and awb_status IN ('Delivered','RTO')"; 
            $order = $this->supportmodel->show_condition('order_waybills','ASC',$condition);
         
            foreach ($order as $order){ 
                if($order->topay=='Yes' && !empty($order->consignee_gst_tin)){$include='No';}
                else if($order->ftopay=='Yes' && !empty($order->consignee_gst_tin)){$include='No';}
                else{$include='Yes';}
                // if($include=='No'){echo $order->lrnum.'-'.$include.'-'.$order->consignee_gst_tin.'<br>';}
                if($include=='No'){
                    $total = trim($order->lastpay);
                    if($order->awb_status=='Delivered'){$sum = $total;}
                    if($order->awb_status=='RTO'){$sum = 2*$total;} 
                    else {$sum = $total;}
                 
                    $con = "login_id='".$order->login_id."' and amount<0 and reason like '%".$order->lrnum."'";
                    $psum = str_replace('-','',$this->supportmodel->wallet($con));
                    if(empty($psum)){$psum=0;}
                 
                    $wnum=   $this->supportmodel->getRows('weight_recon',"awb like '%".$order->lrnum."%'"); 
                    if($wnum>0){
                       $wdata =   $this->supportmodel->search_col('weight_recon','charged_weight',"awb like '%".$order->lrnum."%'");
                       $charged_weight = $wdata->charged_weight;
                       $panel = $order->panel;
                       $suffix= $this->suffix_date($order->login_id,$order->date);
                       $info = $this->divisor($panel." ".$suffix,$order->login_id); 
                     
                       $res['cweight'] = $charged_weight;
                       $res['freight'] = $charged_weight*$order->rate;
                       $res['fsc'] = $res['freight']*$info->fsc/100;
                        if(!empty($order->fm)){ 
                          $fm_charge = explode(',',$info->fm); 
                          $fm = $charged_weight*$fm_charge[0];
                          if(sizeof($fm_charge)>1 && !empty($fm_charge[1]) && $fm_charge[1]>$fm){$fm=$fm_charge[1];}
                      }
                      else{$fm=0;}
                      if(!empty($order->abc)){ 
                        $abc_charge = explode(',',$info->abc); 
                        $abc = $charged_weight*$abc_charge[0];
                        if(sizeof($abc_charge)>1 && !empty($abc_charge[1]) && $abc_charge[1]>$abc){$abc=$abc_charge[1];}
                      }
                      else{$abc=0;}
                      
                      $res['fm'] = $fm;
                      $res['abc'] = $abc;
                       $last =  $res['freight'] + $res['fsc'] + $order->rov + $order->docket + $order->cod + $order->oda + $fm + $abc + $order->green +  $order->handling + $order->lm - $order->discount;
                       $min = $this->mincost($panel." ".$suffix, $order->login_id);
                     
                      if($min>$last){$last= $min;}
                       $gst = $last*$customer->gstpercentage/100;
                       $end = round($last,2) + round($gst,2);
                       $res['gst'] = round($gst,2);
                       $res['lrnum'] =$order->lrnum;
                       $res['total'] = $res['lastpay'] = round($end);
                       $this->supportmodel->update('order_waybills',$res,$order->id);
                     
                       if($order->awb_status=='RTO'){$sum = 2*$end;  if($order->panel=='Rivigo'){$sum = $sum+150;}} 
                       else{$sum = $end;}
                    }
                    $inum=   $this->supportmodel->getRows('b2b_invoice',"lr like '%".$order->lrnum."%'"); 
                    $inum1=   $this->supportmodel->getRows('b2b_billing',"lr like '%".$order->lrnum."%'"); 
                    if($inum==0 && $inum1==0){ 
                        $amount =  $sum;
                        $paid =  $psum;    
                        $lr = $order->lrnum; 
                        if($amount>0){
                            $rcount = $this->supportmodel->getRows('b2b_invoice',array('login_id'=>$order->login_id,'start_date'=>$post['start_date'],'end_date'=>$post['end_date'],'gst'=>$order->consignee_gst_tin,'note'=>'Third Party'));
                            $unpaid= round($amount)-round($paid);
                            if($rcount==0){
                                 if($unpaid<=0){$status='Paid';} else {$status='Unpaid';}
                                 $rnum = $rnum + 1;
                                 $post['start_date'] = '2025-02-19';
                                 $post['login_id'] = $login_id;
                                 $post['lr'] = $lr;
                                 $post['invoice_no'] = $rnum; 
                                 $post['amount'] = round($amount);
                                 $post['paid'] = round($paid);
                                 $post['unpaid'] = round($unpaid);
                                 $post['status'] = $status;
                                 $post['title'] = 'TC/2024-25/';
                                 $post['note'] = 'Third Party';
                                 $post['address'] = $order->customer_id;
                                 $post['gst'] = $order->consignee_gst_tin;
                                 $post['due_date']= date("Y-m-d", strtotime("+15 days $date2"));
                                 
                                 $add = $this->supportmodel->find('address',$order->customer_id);
                                 $company = $add->company;
                                 $name = $add->name;
                                 $address1 = $add->address.', '.$add->state.'- '.$add->pincode;
                                 $state = $add->state;
                                 if(empty($company)){$company=$name;}
                                 $post['company'] = $company;
                                 $post['address1'] = $address1;
                                 $post['state'] = $state;
                                 $this->supportmodel->insert('b2b_billing',$post);
                            }
                            else {
                                 $show = $this->supportmodel->search('b2b_invoice',array('login_id'=>$order->login_id,'start_date'=>$post['start_date'],'end_date'=>$post['end_date'],'gst'=>$order->consignee_gst_tin,'note'=>'Third Party'));
                                 $id = $show->id;
                                 $upost['lr'] = $show->lr.','.$lr;
                                 $upost['amount'] = $show->amount + round($amount);
                                 $upost['paid'] = $show->paid + round($paid);
                                 $upost['unpaid'] = $show->unpaid + round($unpaid);
                                 if($upost['unpaid']<=0){$status='Paid';} else {$status='Unpaid';}
                                 $upost['status'] = $status;
                                 $this->supportmodel->update('b2b_billing',$upost,$id);
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function lr_delete($lr){  
          echo  $this->supportmodel->delete_condition('order_waybills',array('lrnum'=>$lr));
    }

    public function deleteAddress(){
        $key = $this->supportmodel->show_limit_col('address','id',5000,11000,'asc' ,"1=1");   $i=0;   
        foreach($key as $key){
           $customer_id= $key->id;
           $num=   $this->supportmodel->getRows('order_waybills',array('customer_id'=>$customer_id));  
           $num1=   $this->supportmodel->getRows('b2b_invoice',array('address'=>$customer_id)); 
           $num2=   $this->supportmodel->getRows('b2b_billing',array('address'=>$customer_id)); 
           if($num==0 && $num1==0 && $num2==0){ $i++; echo $i.'---'.$key->id.'<br>';
             $this->supportmodel->delete('address',$key->id);
           }
        }
    }
    
    public function deletelrnumber(){
        $key = $this->supportmodel->show('lrnumber');      
        foreach($key as $key){
           $lr= $key->lr;
           $num=   $this->supportmodel->getRows('order_waybills',array('lrnum'=>$lr)); 
           if($num==1){ echo $key->id.'<br>';
             $this->supportmodel->delete('lrnumber',$key->id);
           }
        }
    }
     
    public function not_pick_b2b($count=0) {
        $condition = "awb_status='Not Picked' AND panel NOT IN ('Gati')";
        $data = $this->supportmodel->show_limit_col('order_waybills','lrnum,login_id',100,$count,'DESC' ,$condition);  
        foreach ($data as $view){
            $lrnum = $view->lrnum; 
            $output = $this->curl_get(base_url("api/truxapi/tracking/{$lrnum}"), ''); 
            if(array_key_exists("status",$output) && $output['status']){
               $awb_status= $output['data']['status']; 
               if($awb_status!='Not Picked'){ echo $view->login_id.' - '.$awb_status.' '.$lrnum.' <a href="'.base_url("jobstatus/status/{$lrnum}").'" target="_blank">Track</a><br>';} 
            }
        } 
    }
    
    public function b2c_status_notpicked($count=0){
        $condition = "status='Not Picked'";
        $data = $this->supportmodel->show_limit_col('b2c_waybills','id,login_id,waybill,ship_with,status,order_pk',100,$count, 'DESC' ,$condition);  
        foreach ($data as $view){
            $status = $view->status;
           $waybill = $view->waybill;
            $output= $this->curl_get(base_url("api/truxapi/tracking/{$waybill}"), ''); 
            if($output['status']){
                $status1 = $output['data']['status']; 
                if($status!=$status1 && $status1!='Manifested' && $status1!=''){
                   echo $view->login_id.' - '.$waybill.' '.$status1.' <a href="'.base_url("jobstatus/status/{$waybill}").'" target="_blank">Track</a><br>';
                }
            }
        }
    }
   
    public function mincharge() {
        $condition = "panel='Delhivery'";
        $data = $this->supportmodel->select_rows('mincharge','id,login_id','DESC',$condition);  
        foreach ($data as $view){
             $id = $view->id;
             $user = $this->supportmodel->find_col('registration','panel',$view->login_id);
           echo  $post['panel'] = $user->panel;
            $this->supportmodel->update('mincharge',$post,$id);
        }
    }

    public function rivigo_token(){
         $wconfig = $this->supportmodel->find_col('config','rivigo_token',1);
        $apiurl='https://client-integration-api.rivigo.com/oauth/token';
        $cond = "title like '%Rivigo%'";
        $detail = $this->supportmodel->search_col('b2b-partner','id',$cond);   
        $token = 'Basic '.$wconfig->rivigo_token;
        $output = $this->curl_post($apiurl, $token,'');  
        echo $jwt= $output['payload']['access_token'];  
         $this->supportmodel->update('b2b-partner',array('jwt'=>$jwt),$detail->id); 
    }
      
    public function remit(){
         echo $date = date('Y-m-d',strtotime("-1 days")); echo '<br>';
      echo $this->supportmodel->update_condition('remittance',array('status'=>'Processed','due_date'=>$date), array('status'=>'Processing'));
       echo $this->supportmodel->update_condition('remittance_franchise',array('status'=>'Processed','due_date'=>$date), array('status'=>'Processing')); 
    }
    
    public function cn_pro(){
         $wconfig = $this->supportmodel->find_col('config','email,password,company,phone,whatsapp',1);
        $data = $this->supportmodel->distinct_rows('cn_pro','invoice_no','ASC','1=1'); 
        foreach ($data as $data){ 
            $con = "invoice_no='".$data->invoice_no."'";
            $amount = $this->supportmodel->invoice('cn_pro','amount',$con);
            if($amount>0){
                $res = $this->supportmodel->select_rows('cn_pro','lr','ASC',$con );
                $lr='';
                foreach($res as $res){ if(empty($lr)){$lr=$res->lr;}else {$lr.=','.$res->lr;}}
                if(date('m')>3){ $prefix = "CN/".date('Y')."-".substr((date('Y')+1),2,3)."/";}
                else {  $prefix = "CN/".(date('Y')-1)."-".substr((date('Y')),2,3)."/";}
                $b2b = $this->supportmodel->search_col('b2b_memo_billing','cno',array('method'=>'Credit Note','prefix!='=>''));
                $b2c = $this->supportmodel->search_col('b2c_memo_billing','cno',array('method'=>'Credit Note','prefix!='=>''));
                if($b2b->cno>$b2c->cno){$cno=$b2b->cno;}else {$cno=$b2c->cno;}
                $cn = $cno +1;
                
                $invoice =  $this->supportmodel->find_col('b2b_billing','id,login_id,unpaid,paid,status,title,invoice_no',$data->invoice_no);        
                $user = $this->supportmodel->find_col('registration','gstpercentage,gstpre,wallet,first,last,email,phone',$invoice->login_id); 
                if($user->wallet=='Postpaid' && $invoice->status!='Paid'){
                    $post['paid'] = $invoice->paid + $amount;
                    $post['unpaid'] = round($invoice->unpaid) - round($amount);
                    if($post['unpaid']==0){$post['status']='Paid';}
                    $this->supportmodel->update('b2b_billing',$post,$invoice->id);    
                }
                $memo['invoice_no'] = $invoice->id;
                $memo['date'] = date('Y-m-d');
                $memo['amount'] = round($amount);
                $memo['method'] = 'Credit Note';
                $memo['notes'] =  'Weight Settlement For LR No :'.$lr;
                $memo['cno'] = $cn;
                $memo['prefix'] = $prefix;
              
                $this->supportmodel->insert('b2b_memo_billing',$memo);
                $this->supportmodel->delete_condition('cn_pro',$con);
            
            $this->load->library('email'); 
          
            $config = array(
                'protocol'  => 'ssmtp',
                'smtp_host' => 'ssl://ssmtp.googlemail.com',
                'smtp_port' => 465,
                'smtp_user' => $wconfig->email,
                'smtp_pass' => $wconfig->password,
                'mailtype'  => 'html',
                'charset'   => 'utf-8',
                'starttls'  => true,
                'newline'   => "\r\n"
            );
            $this->email->initialize($config);
            $this->email->set_mailtype("html");
            $subject = 'CN against Invoice NO :'.$invoice->title.$invoice->invoice_no;
            $htmlContent = '<p>Dear '.$user->first.',</p>';
            $htmlContent .= '<p>We have issued CN number '.$prefix.$cn.' for the sum of Rs. '.$amount.' with reference to invoice number '.$invoice->title.$invoice->invoice_no.'.</p>';
             $htmlContent .= '<p>You are requested to acknowledge the receipt of the letter.</p><p>Please feel free to contact us @ +91-'.$wconfig->phone.'</p><br><p>Thank You,<br> Warm Regards,<br> '.$wconfig->company.' TEAM</p>';

            $this->email->to($user->email);
            $this->email->from($wconfig->email,$wconfig->company);
            $this->email->subject($subject);
            $this->email->message($htmlContent); 
            if($this->email->send()){$msg1 = 'Successfully  Created CN and mail too !'; }
           
            $contacts=$user->phone;
            if($contacts!='9999999999' && !empty($contacts) &&  $contacts!='9999999990'){
              $event='CN';
              if(!empty($event) && !empty($wconfig->whatsapp)){
                 $params = array(
                 'phone'=> '+91'.$contacts,
                 'event'=>$event,
                 'name'=> $user->first,
                 'cn'=> $prefix.$cn,
                 'price'=> $amount
                );
                $data_json = json_encode($params); 
                $wurl = $wconfig->whatsapp;
                $output = $this->curl_post($wurl, '', $data_json); 
              }      
            }  
          }
        }
    }     
         
    public function b2b_ndr() {
        $condition = "panel like '%Delhivery%' and status='Complete' and awb_status IN ('Pending','Dispatched')";
        $data = $this->supportmodel->select_rows('order_waybills','login_id,lrnum,waybills,panel,d_mode,topay,ftopay','asc' ,$condition);  
        foreach ($data as $view){
            $panel = $view->panel; 
            $waybill = explode(',',$view->waybills)[0];
            $token = $this->token_key($panel);
            $apiurl = 'https://track.delhivery.com/api/v1/packages/json/?waybill='.trim($waybill).'&verbose=2&token='.$token;  
            $fetch =  $this->curl_get($apiurl, ''); 
            if(array_key_exists("ShipmentData",$fetch)){
             $status = $fetch['ShipmentData'][0]['Shipment']['Status']['Status'];
             if($status=='Pending'){
                if($view->topay){$mode='To-Pay';}
                else if($view->ftopay){$mode='Franchise To-Pay';}
                else { $mode=$view->d_mode;}
                
                $post['login_id'] = $view->login_id;
                $post['awb'] = $view->lrnum;
                $post['date'] = date('Y-m-d');
                $post['status'] = 'Open';
                $post['panel'] = $panel;
                $post['mode'] = $mode;
                $post['reason'] = $fetch['ShipmentData'][0]['Shipment']['Status']['Instructions'];
                $post['count'] =$fetch['ShipmentData'][0]['Shipment']['DispatchCount'];
                $post['last'] = $fetch['ShipmentData'][0]['Shipment']['Status']['StatusLocation'].' '.date('d-m-Y H:i',strtotime($fetch['ShipmentData'][0]['Shipment']['Status']['StatusDateTime']));;
                $num = $this->supportmodel->getRows('ndr',array('awb'=>$view->lrnum));
               if($num){ $this->supportmodel->update_condition('ndr',array('reason'=>$post['reason'],'count'=>$post['count'],'last'=> $post['last'],'panel'=> $post['panel'],'mode'=> $post['mode']),array('awb'=>$view->lrnum));}
               else {$this->supportmodel->insert('ndr',$post);}
             }
             else {$this->supportmodel->delete_condition('ndr',array('awb'=>$view->lrnum)); }
            } 
        }
        $this->bluedart_ndr();
    }
    
    public function bluedart_ndr() {
        $condition = "panel like '%Bluedart%' and status='Complete' and awb_status IN ('Pending','Dispatched')";
        $data = $this->supportmodel->select_rows('order_waybills','login_id,lrnum,waybills,panel,d_mode,topay,ftopay','asc' ,$condition);  
        foreach ($data as $order){
            $api =$this->supportmodel->search_col('b2b-partner','username,password,jwt',array('title'=>$order->panel));  
            $url = 'https://api.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=BOM93254&awb=awb&numbers='.$order->lrnum.'&format=json&lickey=urguig2prhposn0tnpqnjjeroortgvn9&verno=1&scan=1';
            $htoken = "JWTToken: ".$api->jwt;
            $fetch = $this->curl_get($url,'',$htoken);
            if(!empty($fetch) && array_key_exists("Shipment",$fetch['ShipmentData'])){
                $output = $fetch['ShipmentData']['Shipment'][0];
                $st1=$output['StatusType'];
                if($st1!='NF'){
                    $scode = $output['Scans'][0]['ScanDetail']['ScanCode'];
                    $num = $this->supportmodel->getRows('bluedart_ndr',array('code'=>$scode));
                    if($num){
                        if($order->topay){$mode='To-Pay';}
                        else if($order->ftopay){$mode='Franchise To-Pay';}
                        else { $mode=$order->d_mode;}
                
                        $post['login_id'] = $order->login_id;
                        $post['awb'] = $order->lrnum;
                        $post['date'] = date('Y-m-d');
                        $post['status'] = 'Open';
                        $post['panel'] = $order->panel;
                        $post['mode'] = $mode;
                        $post['reason'] = $output['Scans'][0]['ScanDetail']['Scan'].'-'.$output['Scans'][0]['ScanDetail']['Status'];
                        $post['count'] = $output['Scans'][0]['ScanDetail']['ScanGroupType'].'-'.$scode;
                        $post['last'] = $output['Scans'][0]['ScanDetail']['ScannedLocation'].' '.$output['Scans'][0]['ScanDetail']['ScanDate'].' '.$output['Scans'][0]['ScanDetail']['ScanTime'];
                        $num = $this->supportmodel->getRows('ndr',array('awb'=>$order->lrnum));
                        if($num){ $this->supportmodel->update_condition('ndr',array('reason'=>$post['reason'],'count'=>$post['count'],'last'=> $post['last'],'panel'=> $post['panel'],'mode'=> $post['mode']),array('awb'=>$order->lrnum));}
                        else {$this->supportmodel->insert('ndr',$post);}
                     }
                     else {$this->supportmodel->delete_condition('ndr',array('awb'=>$order->lrnum)); }
                           
                }
            }
        }
    }
    
    public function b2c_ndr(){
        $condition = "partner like '%Delhivery%'";
        $partner = $this->supportmodel->show_condition('partner','desc',$condition);  
        foreach ($partner as $partner){
          $data = $this->supportmodel->select_rows('b2c_waybills','login_id,ship_with,waybill,payment_mode,topay,ftopay', 'ASC' ,array('ship_with'=>$partner->id));  
          foreach ($data as $view){
            $waybill = $view->waybill;
            $token = $partner->api;
            $panel  = $partner->partner;
            $url = 'https://track.delhivery.com/api/v1/packages/json/?waybill='.trim($waybill).'&verbose=2&token='.$token;  
            $fetch = $this->curl_get($url, ''); 
            if(array_key_exists("ShipmentData",$fetch)){
              $status = $fetch['ShipmentData'][0]['Shipment']['Status']['Status'];
              if($status=='Pending'){
                if($view->topay){$mode='To-Pay';}
                else if($view->ftopay){$mode='Franchise To-Pay';}
                else { $mode=$view->payment_mode;}       
                $post['login_id'] = $view->login_id;
                $post['awb'] = $waybill;
                $post['date'] = date('Y-m-d');
                $post['status'] = 'Open';
                $post['panel'] = $panel;
                $post['mode'] = $mode;
                $post['reason'] = $fetch['ShipmentData'][0]['Shipment']['Status']['Instructions'];
                $post['count'] =$fetch['ShipmentData'][0]['Shipment']['DispatchCount'];
                $post['last'] = $fetch['ShipmentData'][0]['Shipment']['Status']['StatusLocation'].' '.date('d-m-Y H:i',strtotime($fetch['ShipmentData'][0]['Shipment']['Status']['StatusDateTime']));;
                $num = $this->supportmodel->getRows('ndr',array('awb'=>$waybill));
               if($num){ $this->supportmodel->update_condition('ndr',array('reason'=>$post['reason'],'count'=>$post['count'],'last'=> $post['last'],'panel'=> $post['panel'],'mode'=> $post['mode']),array('awb'=>$waybill));}
               else {$this->supportmodel->insert('ndr',$post);}
             }
             else {$this->supportmodel->delete_condition('ndr',array('awb'=>$waybill)); }
            }
          }
        }
    }
    
    public function ndr() {
        $data = $this->supportmodel->select_rows('ndr','awb,id','asc');  
        foreach ($data as $view){
            $status='';
            $awb= $view->awb;
             $res = $this->supportmodel->search_col('b2c_waybills','status', array('waybill'=>$awb));  
             if($res){$status=$res->status;}
             else { $res = $this->supportmodel->search_col('order_waybills','awb_status', array('lrnum'=>$awb));  if($res){$status=$res->awb_status;}  }
             if(!empty($status) && $status!='Pending'){
                 $this->supportmodel->delete('ndr',$view->id);
             }
        }
    }     
    
    public function one_day_rem() {
        $date2= date('d-m-Y', strtotime('-1 days'));
        $date1 = date('d-m-Y', strtotime('-5 days'));
        $condition = "kyc='1' and cod_plan='1_Day'";
        $post['start_date'] = $post['due_date']= date('Y-m-d', strtotime('-1 days'));
        $rate=$this->supportmodel->search_col('cod_plan','rate',array('plan'=>'1_Day'));  
        $data = $this->supportmodel->select_rows('registration','id,','asc' ,$condition);  
        foreach ($data as $data){
            $amt=0;$waybill='';
            $login_id= $data->id; echo '<br>';
            $condition1 = "login_id=".$login_id." and status='Delivered' and payment_mode='COD' and topay!='Yes' and ftopay!='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2c = $this->supportmodel->select_rows('b2c_waybills','waybill,cod_amount,statusDate','ASC',$condition1); 
            foreach($b2c as $b2c){
                $num = $this->supportmodel->getRows('remittance',"waybill like '%".$b2c->waybill."%'");
                if($num==0){ 
                    if(empty($waybill)){ $waybill=$b2c->waybill;}
                    else {$waybill.= ','.$b2c->waybill;}
                    $amt= $amt + $b2c->cod_amount;
                }
            }
            $condition2 = "login_id=".$login_id." and awb_status='Delivered' and d_mode='CoD' and topay!='Yes' and ftopay!='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2b = $this->supportmodel->select_rows('order_waybills','lrnum,amount,statusDate','ASC',$condition2); 
            foreach($b2b as $b2b){
                $num1 = $this->supportmodel->getRows('remittance',"waybill like '%".$b2b->lrnum."%'");
                if($num1==0){ 
                    if(empty($waybill)){ $waybill=$b2b->lrnum;}
                    else {$waybill.= ','.$b2b->lrnum;}
                    $amt= $amt + $b2b->amount;
                }
            }
            if($amt>0){
              $charge= $amt*$rate->rate/100;
              $tax = $charge*.18;
              $data1 = $this->supportmodel->search_col('remittance','rnumber');  
              $post['login_id'] = $login_id;
              $post['status'] = 'Processed';
              $post['prefix'] = 'TR';
              $post['rnumber'] = $data1->rnumber+1;
              $post['amount'] = $amt;
              $post['waybill'] = $waybill;
              $post['charges'] = round(($charge+$tax),2);
             print_r($post);
            $this->supportmodel->insert('remittance',$post);
            }
        }
    }
    
    public function two_day_rem() {
        $date2= date('d-m-Y', strtotime('-2 days'));
        $date1 = date('d-m-Y', strtotime('-2 days'));
        $condition = "kyc='1' and cod_plan='2_Days'";
        $post['start_date'] = $post['due_date']= date('Y-m-d', strtotime('-1 days'));
        $rate=$this->supportmodel->search_col('cod_plan','rate',array('plan'=>'2_Days'));  
        $data = $this->supportmodel->select_rows('registration','id,','asc' ,$condition);  
        foreach ($data as $data){
            $amt=0;$waybill='';
            $login_id= $data->id; echo '<br>';
            $condition1 = "login_id=".$login_id." and status='Delivered' and payment_mode='COD' and topay!='Yes' and ftopay!='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2c = $this->supportmodel->select_rows('b2c_waybills','waybill,cod_amount,statusDate','ASC',$condition1); 
            foreach($b2c as $b2c){
                $num = $this->supportmodel->getRows('remittance',"waybill like '%".$b2c->waybill."%'");
                if($num==0){ 
                    if(empty($waybill)){ $waybill=$b2c->waybill;}
                    else {$waybill.= ','.$b2c->waybill;}
                    $amt= $amt + $b2c->cod_amount;
                }
            }
            $condition2 = "login_id=".$login_id." and awb_status='Delivered' and d_mode='CoD' and topay!='Yes' and ftopay!='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2b = $this->supportmodel->select_rows('order_waybills','lrnum,amount,statusDate','ASC',$condition2); 
            foreach($b2b as $b2b){
                $num1 = $this->supportmodel->getRows('remittance',"waybill like '%".$b2b->lrnum."%'");
                if($num1==0){ 
                    if(empty($waybill)){ $waybill=$b2b->lrnum;}
                    else {$waybill.= ','.$b2b->lrnum;}
                    $amt= $amt + $b2b->amount;
                }
            }
            if($amt>0){
              $charge= $amt*$rate->rate/100;
              $tax = $charge*.18;
              $data1 = $this->supportmodel->search_col('remittance','rnumber');  
              $post['login_id'] = $login_id;
              $post['status'] = 'Processed';
              $post['prefix'] = 'TR';
              $post['rnumber'] = $data1->rnumber+1;
              $post['amount'] = $amt;
              $post['waybill'] = $waybill;
              $post['charges'] = round(($charge+$tax),2);
            print_r($post);
            $this->supportmodel->insert('remittance',$post);
            }
        }
    }
    
    public function three_day_rem() {
        $date2= date('d-m-Y', strtotime('-3 days'));
        $date1 = date('d-m-Y', strtotime('-3 days'));
        $condition = "kyc='1' and cod_plan='3_Days'";
        $post['start_date'] = $post['due_date']= date('Y-m-d', strtotime('-1 days'));
        $rate=$this->supportmodel->search_col('cod_plan','rate',array('plan'=>'3_Days'));  
        $data = $this->supportmodel->select_rows('registration','id,','asc' ,$condition);  
        foreach ($data as $data){
            $amt=0;$waybill='';
            $login_id= $data->id; echo '<br>';
            $condition1 = "login_id=".$login_id." and status='Delivered' and payment_mode='COD' and topay!='Yes' and ftopay!='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2c = $this->supportmodel->select_rows('b2c_waybills','waybill,cod_amount,statusDate','ASC',$condition1); 
            foreach($b2c as $b2c){
                $num = $this->supportmodel->getRows('remittance',"waybill like '%".$b2c->waybill."%'");
                if($num==0){ 
                    if(empty($waybill)){ $waybill=$b2c->waybill;}
                    else {$waybill.= ','.$b2c->waybill;}
                    $amt= $amt + $b2c->cod_amount;
                }
            }
            $condition2 = "login_id=".$login_id." and awb_status='Delivered' and d_mode='CoD' and topay!='Yes' and ftopay!='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2b = $this->supportmodel->select_rows('order_waybills','lrnum,amount,statusDate','ASC',$condition2); 
            foreach($b2b as $b2b){
                $num1 = $this->supportmodel->getRows('remittance',"waybill like '%".$b2b->lrnum."%'");
                if($num1==0){ 
                    if(empty($waybill)){ $waybill=$b2b->lrnum;}
                    else {$waybill.= ','.$b2b->lrnum;}
                    $amt= $amt + $b2b->amount;
                }
            }
            if($amt>0){
              $charge= $amt*$rate->rate/100;
              $tax = $charge*.18;
              $data1 = $this->supportmodel->search_col('remittance','rnumber');  
              $post['login_id'] = $login_id;
              $post['status'] = 'Processed';
              $post['prefix'] = 'TR';
              $post['rnumber'] = $data1->rnumber+1;
              $post['amount'] = $amt;
              $post['waybill'] = $waybill;
              $post['charges'] = round(($charge+$tax),2);
               print_r($post);
            $this->supportmodel->insert('remittance',$post);
            }
        }
    }
    
    public function one_day_franchise() {
        $date2= date('d-m-Y', strtotime('-1 days'));
        $date1 = date('d-m-Y', strtotime('-5 days'));
        $condition = "kyc='1' and cod_plan='1_Day'";
        $post['start_date'] = $post['due_date']= date('Y-m-d', strtotime('-1 days'));
        $rate=$this->supportmodel->search_col('cod_plan','rate',array('plan'=>'1_Day'));  
        $data = $this->supportmodel->select_rows('registration','id,','asc' ,$condition);  
        foreach ($data as $data){
            $amt=0;$waybill='';
            $login_id= $data->id; echo '<br>';
            $condition1 = "login_id=".$login_id." and status='Delivered' and payment_mode='COD' and ftopay='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2c = $this->supportmodel->select_rows('b2c_waybills','waybill,profit,statusDate','ASC',$condition1); 
            foreach($b2c as $b2c){
                $num = $this->supportmodel->getRows('remittance_franchise',"waybill like '%".$b2c->waybill."%'");
                if($num==0){ 
                    if(empty($waybill)){ $waybill=$b2c->waybill;}
                    else {$waybill.= ','.$b2c->waybill;}
                    $amt= $amt + $b2c->profit;
                }
            }
            $condition2 = "login_id=".$login_id." and awb_status='Delivered' and d_mode='CoD' and ftopay='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2b = $this->supportmodel->select_rows('order_waybills','lrnum,profit,statusDate','ASC',$condition2); 
            foreach($b2b as $b2b){
                $num1 = $this->supportmodel->getRows('remittance_franchise',"waybill like '%".$b2b->lrnum."%'");
                if($num1==0){ 
                    if(empty($waybill)){ $waybill=$b2b->lrnum;}
                    else {$waybill.= ','.$b2b->lrnum;}
                    $amt= $amt + $b2b->profit;
                }
            }
            if($amt>0){
              $charge= $amt*$rate->rate/100;
              $tax = $charge*.18;
              $data1 = $this->supportmodel->search_col('remittance_franchise','rnumber');  
              $post['login_id'] = $login_id;
              $post['status'] = 'Processed';
              $post['prefix'] = 'TFR';
              $post['rnumber'] = $data1->rnumber+1;
              $post['amount'] = $amt;
              $post['waybill'] = $waybill;
              $post['charges'] = round(($charge+$tax),2);
              print_r($post);
            
            $this->supportmodel->insert('remittance_franchise',$post);
            }
        }
         $this->supportmodel->update('xprestoken',array('token'=>'jobstatus'.date('Y-m-d h:i:s')), 5);
    }
    
    public function two_day_franchise() {
        $date2= date('d-m-Y', strtotime('-2 days'));
        $date1 = date('d-m-Y', strtotime('-2 days'));
        $condition = "kyc='1' and cod_plan='2_Days'";
        $post['start_date'] = $post['due_date']= date('Y-m-d', strtotime('-1 days'));
        $rate=$this->supportmodel->search_col('cod_plan','rate',array('plan'=>'2_Days'));  
        $data = $this->supportmodel->select_rows('registration','id,','asc' ,$condition);  
        foreach ($data as $data){
            $amt=0;$waybill='';
            $login_id= $data->id; echo '<br>';
            $condition1 = "login_id=".$login_id." and status='Delivered' and payment_mode='COD' and ftopay='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2c = $this->supportmodel->select_rows('b2c_waybills','waybill,profit,statusDate','ASC',$condition1); 
            foreach($b2c as $b2c){
                $num = $this->supportmodel->getRows('remittance_franchise',"waybill like '%".$b2c->waybill."%'");
                if($num==0){ 
                    if(empty($waybill)){ $waybill=$b2c->waybill;}
                    else {$waybill.= ','.$b2c->waybill;}
                    $amt= $amt + $b2c->profit;
                }
            }
            $condition2 = "login_id=".$login_id." and awb_status='Delivered' and d_mode='CoD' and ftopay='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2b = $this->supportmodel->select_rows('order_waybills','lrnum,profit,statusDate','ASC',$condition2); 
            foreach($b2b as $b2b){
                $num1 = $this->supportmodel->getRows('remittance_franchise',"waybill like '%".$b2b->lrnum."%'");
                if($num1==0){ 
                    if(empty($waybill)){ $waybill=$b2b->lrnum;}
                    else {$waybill.= ','.$b2b->lrnum;}
                    $amt= $amt + $b2b->profit;
                }
            }
            if($amt>0){
              $charge= $amt*$rate->rate/100;
              $tax = $charge*.18;
              $data1 = $this->supportmodel->search_col('remittance_franchise','rnumber');  
              $post['login_id'] = $login_id;
              $post['status'] = 'Processed';
              $post['prefix'] = 'TFR';
              $post['rnumber'] = $data1->rnumber+1;
              $post['amount'] = $amt;
              $post['waybill'] = $waybill;
              $post['charges'] = round(($charge+$tax),2);
              print_r($post);
            
            $this->supportmodel->insert('remittance_franchise',$post);
            }
        }
    }
    
    public function three_day_franchise() {
        $date2= date('d-m-Y', strtotime('-3 days'));
        $date1 = date('d-m-Y', strtotime('-3 days'));
        $condition = "kyc='1' and cod_plan='3_Days'";
        $post['start_date'] = $post['due_date']= date('Y-m-d', strtotime('-1 days'));
        $rate=$this->supportmodel->search_col('cod_plan','rate',array('plan'=>'3_Days'));  
        $data = $this->supportmodel->select_rows('registration','id,','asc' ,$condition);  
        foreach ($data as $data){
            $amt=0;$waybill='';
            $login_id= $data->id; echo '<br>';
            $condition1 = "login_id=".$login_id." and status='Delivered' and payment_mode='COD' and ftopay='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2c = $this->supportmodel->select_rows('b2c_waybills','waybill,profit,statusDate','ASC',$condition1); 
            foreach($b2c as $b2c){
                $num = $this->supportmodel->getRows('remittance_franchise',"waybill like '%".$b2c->waybill."%'");
                if($num==0){ 
                    if(empty($waybill)){ $waybill=$b2c->waybill;}
                    else {$waybill.= ','.$b2c->waybill;}
                    $amt= $amt + $b2c->profit;
                }
            }
            $condition2 = "login_id=".$login_id." and awb_status='Delivered' and d_mode='CoD' and ftopay='Yes' and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2b = $this->supportmodel->select_rows('order_waybills','lrnum,profit,statusDate','ASC',$condition2); 
            foreach($b2b as $b2b){
                $num1 = $this->supportmodel->getRows('remittance_franchise',"waybill like '%".$b2b->lrnum."%'");
                if($num1==0){ 
                    if(empty($waybill)){ $waybill=$b2b->lrnum;}
                    else {$waybill.= ','.$b2b->lrnum;}
                    $amt= $amt + $b2b->profit;
                }
            }
            if($amt>0){
              $charge= $amt*$rate->rate/100;
              $tax = $charge*.18;
              $data1 = $this->supportmodel->search_col('remittance_franchise','rnumber');  
              $post['login_id'] = $login_id;
              $post['status'] = 'Processed';
              $post['prefix'] = 'TFR';
              $post['rnumber'] = $data1->rnumber+1;
              $post['amount'] = $amt;
              $post['waybill'] = $waybill;
              $post['charges'] = round(($charge+$tax),2);
              print_r($post);
            
            $this->supportmodel->insert('remittance_franchise',$post);
            }
        }
    }
    
    public function early_cod() {
        $date2= date('d-m-Y', strtotime('-1 days'));
        $date1 = date('d-m-Y', strtotime('-4 days'));
        $condition = "kyc='1' and cod_plan!='Weekly'";
        $data = $this->supportmodel->select_rows('registration','id,','asc' ,$condition);  
        foreach ($data as $data){
            $login_id= $data->id; 
            $condition2 = "login_id=".$login_id." and awb_status='Delivered' and d_mode='CoD' and topay!='Yes'  and  STR_TO_DATE(statusDate, '%d-%m-%Y') between  STR_TO_DATE('".$date1."', '%d-%m-%Y') and STR_TO_DATE('".$date2."', '%d-%m-%Y')"; 
            $b2b = $this->supportmodel->select_rows('order_waybills','lrnum,amount,statusDate,ftopay','ASC',$condition2); 
            foreach($b2b as $b2b){
                if($b2b->ftopay=='Yes'){$table='remittance_franchise'; $mode='franchise';}else {$table='remittance'; $mode='COD';}
                $num1 = $this->supportmodel->getRows($table,"waybill like '%".$b2b->lrnum."%'");
                if($num1==0){ 
                     echo $b2b->lrnum.' '.$mode.'<br>';
                }
            }
        }
    }
    
    public function plan_expire() {
        $data = $this->supportmodel->select_rows('buy_plan','*','asc',"edate like '%".date('Y-m-d')."%'");  
        foreach ($data as $data){
          $plan= $data->plan;
          if($plan=='Startup'){$ton=5;}
          else if($plan=='Small Business'){$ton=15;}
          else {$ton=30;}
          $m= date('m',strtotime($data->edate))-date('m',strtotime($data->date));
          if($m==1){$duration='monthly'; $month=1;}
          else if($m==3){$duration='quarterly'; $month=3;}
          else if($m==6){$duration='half'; $month=6;} else {$duration='yearly'; $month=12;}
          $target= $month*$ton;
          $b2b = $this->supportmodel->invoice('order_waybills','cweight',"awb_status!='Not Picked' and login_id='".$data->login_id."' and date between '".$data->date."' and '".$data->edate."'"); 
          $b2c = $this->supportmodel->invoice('b2c_waybills','charged_weight',"status!='Not Picked' and login_id='".$data->login_id."' and date between '".$data->date."' and '".$data->edate."'"); 
          
          $load = $b2b/1000 + $b2c/1000000;
           
          if($load>=$target){
              $res = $this->supportmodel->search_col('plan',$duration,array('title'=>$plan)); 
              $amt = $res->$duration;
              if(!empty($data->coupon)){$amt = $amt/2;}
              $total = $amt + $amt*.18;
             
              $wal['login_id']= $data->login_id;
              $wal['amount'] = $total;
              $wal['reason']=  'Refund Of '.$plan.' Plan for target achieved';
              $wal['date']= date('Y-m-d H:i:s');
              $wal['status']= 'Confirm';
              $wal['add_by']= 'Auto';
              $this->supportmodel->insert('wallet',$wal);
          }
          $this->supportmodel->update('buy_plan',array('status'=>'Expire'),$data->id);  
          
          ///--------------- registration plan -------------------//
           $data1 = $this->supportmodel->select_rows('registration','id','asc',"expire like '%".date('Y-m-d')."%'");  
           foreach ($data1 as $data1){
               $this->supportmodel->update('registration',array('plan'=>'','expire'=>''),$data1->id);  
                ///--------------- manual rate -------------------//
               $num = $this->supportmodel->getRows('air_price',"login_id='".$data1->id."'");  
               if($num){
                    $this->supportmodel->delete_condition('air_price', array('login_id'=>$data1->id));
                    $this->supportmodel->delete_condition('dtdc_price', array('login_id'=>$data1->id));
                    $this->supportmodel->delete_condition('gati', array('login_id'=>$data1->id));
                    $this->supportmodel->delete_condition('matrix_charge', array('login_id'=>$data1->id));
                   $this->supportmodel->delete_condition('mincharge', array('login_id'=>$data1->id));
                   $this->supportmodel->delete_condition('price', array('login_id'=>$data1->id));
               }
           }
            ///--------------- capping -------------------//
           $data2 = $this->supportmodel->select_rows('registration','id','asc',"capping>0 and capping_expire like '%".date('Y-m-d')."%'");  
           foreach ($data2 as $data2){
               $this->supportmodel->update('registration',array('capping'=>0),$data2->id);    
           }
        }
    } 
    
     public function b2b_b2b_warehouse(){
        $b2b = $this->supportmodel->show_limit('pickup',100,0,'ASC',"status='On'" );  
        $apiurl='https://ltl-clients-api.delhivery.com/client-warehouse/create/';
        $api = $this->supportmodel->find_col('b2b-partner','jwt','3'); 
        $token = $api->jwt;
        $accesstoken = 'Bearer '.$token;
        foreach ($b2b as $b2b){    
           $data = array (  
            'pin_code' => $b2b->pincode,   
            'city' => $b2b->city,
            'state' =>  $b2b->state,
            'country' => 'INDIA',
            'address_details' =>  array (
                'address' => $b2b->address,
                'contact_person' => $b2b->name,
                'phone_number' => $b2b->phone,
               ),
            'same_as_fwd_add' => true,
            'name' => $b2b->nickname  
           );
	      $data_json = json_encode($data);
          $output = $this->curl_post($apiurl,$accesstoken,$data_json);
          print_r($output); echo '<br>';
        }
    } 
    
    public function checkNdr()
    {
        $condition = "awb_status NOT IN ('Manifested','Delivered','Not Picked','RTO','LOST')
        AND status = 'Complete'
        AND ticketing_ndr_status = 0
        AND STR_TO_DATE(edd, '%d-%m-%Y') < CURDATE()";
    
        $get_order = $this->supportmodel->select_rows_limit(
            'order_waybills',
            'id,login_id,lrnum,edd,panel,d_mode',
            'DESC',
            40,
            $condition
        );
    
        if (!empty($get_order)) {
    
            foreach ($get_order as $row) {
    
                $customer = $this->supportmodel->find('registration', $row->login_id);
    
                $customer_name = !empty($customer) ? $customer->first : '';
    
                $checkExist = $this->supportmodel->search(
                    'tracking_exception_report',
                    [
                        'awb_no' => $row->lrnum,
                        'entry_type' => 'EDD'
                    ]
                );
    
                if (empty($checkExist)) {
    
                    $insertData = [
                        'awb_no' => $row->lrnum,
                        'login_id' => $row->login_id,
                        'customer_name' => $customer_name,
                        'vendor_name' => $row->panel,
                        'event_code' => '',
                        'event_desc' => 'EDD Breached',
                        'event_datetime' => date('d/m/Y h:i A'),
                        'expected_date' => $row->edd,
                        'delivery_date' => '',
                        'status' => 'Open',
                        'comments' => '',
                        'entry_type' => 'EDD',
                        'created_date' => date('Y-m-d H:i:s')
                    ];
    
                    $this->supportmodel->insert('tracking_exception_report', $insertData);
    
                    $update_order = [
                        'ticketing_ndr_status' => 1
                    ];
    
                   $this->supportmodel->update('order_waybills',$update_order,$row->id);
                   
                }
            }
    
            //echo "EDD Records Inserted Successfully.";
    
        } 
        // else {
    
        //     echo "No records found.";
        // }
    }
    
    public function ndr_exception()
    {
        $condition = "panel like '%Bluedart%' and status='Complete' and ticketing_exception = 0";
    
        $data = $this->supportmodel->select_rows_limit( 'order_waybills', 'id,login_id,lrnum,waybills,panel,d_mode,ticketing_exception,edd', 'asc', 500, $condition );

    
        if (!empty($data)) {
    
            foreach ($data as $order) {
    
                $customer = $this->supportmodel->find( 'registration', $order->login_id );
    
                $customer_name = '';
    
                if (!empty($customer)) {
    
                    $customer_name =  $customer->first;
                }
    
                $api = $this->supportmodel->search_col( 'b2b-partner', 'username,password,jwt', ['title' => $order->panel] );
    
                $url = 'https://api.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=BOM93254&awb=awb&numbers='.$order->lrnum.'&format=json&lickey=urguig2prhposn0tnpqnjjeroortgvn9&verno=1&scan=1';
    
                $htoken = "JWTToken: ".$api->jwt;
    
                $fetch = $this->curl_get($url,'',$htoken);
    
                if (!empty($fetch) && array_key_exists("Shipment",$fetch['ShipmentData'])) {
    
                    $shipment = $fetch['ShipmentData']['Shipment'][0];
    
                    if (!empty($shipment['Scans'])) {
    
                        foreach ($shipment['Scans'] as $scan) {
    
                            if (empty($scan['ScanDetail'])) {
                                continue;
                            }
    
                            $scanDetail = $scan['ScanDetail'];
    
                            $scanCode = trim($scanDetail['ScanCode']);
    
                            $exception = $this->supportmodel->search( 'exception_master', [ 'status_code' => $scanCode, 'is_ndr' => 1 ] );
    
                            if (!empty($exception)) {
    
                                $checkNdr = $this->supportmodel->search(
                                    'tracking_exception_report',
                                    [
                                        'awb_no' => $order->lrnum,
                                        'event_code' => $scanCode
                                    ]
                                );
    
                                if (empty($checkNdr)) {
                                    $insertData = [
                                        'awb_no' => $order->lrnum,
                                        'login_id' => $order->login_id,
                                        'customer_name' => $customer_name,
                                        'vendor_name' => $order->panel,
                                        'event_code' => $scanCode,
                                        'entry_type'=>'NDR',
                                        'event_desc' => $exception->desc,
                                        'event_datetime' => date('d/m/Y h:i A',strtotime($scanDetail['ScanDate'].' '.$scanDetail['ScanTime'])),
                                        'expected_date' => $order->edd,
                                        'delivery_date' => '',
                                        // 'tracking_head' => $scanDetail['Scan'],
                                       'created_date' => date('Y-m-d H:i:s')
                                    ];
    
                                   $this->supportmodel->insert('tracking_exception_report',$insertData);
    
                                    if ($exception->create_ticket == 1) {

                                        $checkTicket = $this->supportmodel->search( 'ticket', [ 'login_id'=>$order->login_id, 'detail'=>$order->lrnum, 'subject'=>$exception->desc ] );

                                        if (empty($checkTicket)) {
    
                                            $ticketData = [
                                                'login_id'=>$order->login_id,
                                                'tno'=>rand(100000,999999),
                                                'category'=>$exception->ticket_department,
                                                'sub'=>$exception->ticket_priority,
                                                'subject'=>$exception->desc,
                                                 'detail'=>'LRNum - '.$order->lrnum,
                                                'status'=>'Open',
                                                'date'=>date('Y-m-d H:i:s')
                                            ];
    
                                            $this->supportmodel->insert('ticket',$ticketData);
                                        }
                                    }
                                    
                                    $updateData = ['ticketing_exception' => 1];
    
                                    $this->supportmodel->update_condition( 'order_waybills', $updateData, ['id' => $order->id] );
                                    
                                }
                                
                                
                            }
                        }
                    }
                }
            }
        }
        
        $this->ndr_exception_critical_log();
    
    }
    
    public function ndr_exception_critical_log()
    {

        $condition = "panel like '%Critical Log%' and status='Complete' and ticketing_exception = 0";
    
        $data = $this->supportmodel->select_rows_limit( 'order_waybills', 'id,login_id,lrnum,waybills,panel,d_mode,ticketing_exception,edd', 'asc', 500, $condition );
    
        if (!empty($data)) {
    
            foreach ($data as $order) {
    
                $customer = $this->supportmodel->find('registration', $order->login_id);
                $customer_name = !empty($customer) ? $customer->first : '';
    
                $api = $this->supportmodel->search_col( 'b2b-partner', 'apikey', ['title' => $order->panel] );
    
                $auth   = $api->apikey;
                $docket = $order->lrnum;
    
                // ================= API CALL =================
                $curl = curl_init();
    
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://www.ecritica.co/eFreightLive/api/tracking.php',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS => json_encode([
                        "Auth" => $auth,
                        "DocketNo" => $docket
                    ]),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));
    
                $response = curl_exec($curl);
    
                if (curl_errno($curl)) {
                    echo 'Curl Error: ' . curl_error($curl);
                    continue;
                }
    
                curl_close($curl);
    
                $fetch = json_decode($response, true);
    
                // ================= RESPONSE HANDLE =================
    
                if (empty($fetch) || $fetch['code'] != 1) {
                    continue; // invalid docket skip
                }
    
                $details = $fetch['data']['docketDetails'];
    
                if (empty($details['traversal'])) {
                    continue;
                }
    
                foreach ($details['traversal'] as $scan) {
    
                    $scanCode = $scan['CodeNumber']; 
                    $remark   = isset($scan['Remark']) ? $scan['Remark'] : $scan['Code'];
    
                    // ================= EXCEPTION MATCH =================
                    $exception = $this->supportmodel->search(
                        'exception_master',
                        [
                            'status_code' => $scanCode,
                            'is_ndr' => 1
                        ]
                    );
    
                    if (!empty($exception)) {
    
                        // ================= DUPLICATE CHECK =================
                        $checkNdr = $this->supportmodel->search(
                            'tracking_exception_report',
                            [
                                'awb_no' => $order->lrnum,
                                'event_code' => $scanCode
                            ]
                        );
    
                        if (empty($checkNdr)) {
    
                            $insertData = [
                                'awb_no' => $order->lrnum,
                                'login_id' => $order->login_id,
                                'customer_name' => $customer_name,
                                'vendor_name' => $order->panel,
                                'event_code' => $scanCode,
                                'event_desc' => $remark,
                                'entry_type'=>'NDR',
                                'event_datetime' => date('d/m/Y h:i A', strtotime($scan['EventDate'])),
                                'expected_date' => $order->edd,
                                'delivery_date' => $details['DeliveredDate'] ?? '',
                               'created_date' => date('Y-m-d H:i:s')
                            ];
    
                            $this->supportmodel->insert('tracking_exception_report', $insertData);
                            
                            if ($exception->create_ticket == 1) {

                                $checkTicket = $this->supportmodel->search( 'ticket', [ 'login_id'=>$order->login_id, 'detail'=>$order->lrnum, 'subject'=>$exception->desc ] );

                                if (empty($checkTicket)) {

                                    $ticketData = [
                                        'login_id'=>$order->login_id,
                                        'tno'=>rand(100000,999999),
                                        'category'=>$exception->ticket_department,
                                        'sub'=>$exception->ticket_priority,
                                        'subject'=>$exception->desc,
                                        'detail'=>'LRNum - '.$order->lrnum,
                                        'status'=>'Open',
                                        'date'=>date('Y-m-d H:i:s')
                                    ];

                                    $this->supportmodel->insert('ticket',$ticketData);
                                }
                            }

                            $this->supportmodel->update_condition( 'order_waybills', ['ticketing_exception' => 1], ['id' => $order->id] );
                        }
                    }
                }
            }
        }
    }
    
    public function pod_details_get($awb = '')
    {
       
        if(empty($awb))
        {
            echo json_encode([
                'status' => false,
                'message' => 'AWB Number Required'
            ]);
            exit;
        }

        $link = '';
        
        // Existing POD Logic
        $order = $this->supportmodel->search('order_waybills',array('lrnum'=>$awb)); 
        if(empty($order))
        {
            echo json_encode([
                'status' => false,
                'message' => 'AWB Not Found'
            ]);
            exit;
        }
        
       
        if(strpos($order->panel,'Critical Log') !== false){
            $api = $this->supportmodel->search_col('b2b-partner','apikey',array('title'=>$order->panel)); 
           
            $url = 'https://www.ecritica.co/eFreightLive/api/tracking.php';

            $params = [
                "Auth" => $api->apikey,
                "DocketNo" => $awb
            ];

            $output = $this->curl_post( $url, '', json_encode($params), '' );

            if(
                !empty($output['data']['docketDetails']['pod_base64_encoded'])
                && $output['data']['docketDetails']['pod_base64_encoded'] != 'dfhkdsfshfkjhdkjfhsdh='
            ){

                $fileName = 'uploads/epod/'.$awb.'.jpg';

                if(!is_dir(FCPATH.'uploads/epod')){
                    mkdir(FCPATH.'uploads/epod',0777,true);
                }

                file_put_contents(
                    FCPATH.$fileName,
                    base64_decode($output['data']['docketDetails']['pod_base64_encoded'])
                );

                $link = base_url($fileName);
            }
        }
        else{
        $api = $this->supportmodel->search_col('b2b-partner','jwt,apikey',array('title'=>$order->panel));
        $link='';
        $token = $api->jwt ?? '';

        $accesstoken = 'Bearer '.$token;

            $apiurl = 'https://ltl-clients-api.delhivery.com/document/download?lrn='
                    .$awb.
                    '&doc_type=LM_POD&audo_download=false&version=latest';

            $output = $this->curl_get($apiurl, $accesstoken);

            if(!empty($output['success'])){

                $link = $output['data']['files'][0]['url'];
            }
        }

        if(!empty($link))
        {
            $ext = pathinfo(parse_url($link, PHP_URL_PATH), PATHINFO_EXTENSION);

            if(empty($ext)){
                $ext = 'pdf';
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="POD_'.$awb.'.'.$ext.'"');
            header('Pragma: public');

            readfile($link);
            exit;
        }
        else
        {
            helper('download');

            $pdf_path = FCPATH.'uploads/epod/pod_not_available.pdf';

            force_download(
                'POD_NOT_AVAILABLE.pdf',
                file_get_contents($pdf_path)
            );
            exit;
        }
    }

    protected function curl_get(string $url, string $accessToken = '', string $extraHeader = ''): array
    {
        $headers = [];

        if ($accessToken !== '') {
            $headers[] = str_contains($accessToken, ':') ? $accessToken : 'Authorization: ' . $accessToken;
        }

        if ($extraHeader !== '') {
            $headers[] = $extraHeader;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($headers !== []) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode((string) $response, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function curl_post(string $url, string $accessToken = '', string $dataJson = '', string $extraHeader = ''): array
    {
        $headers = ['Content-Type: application/json'];

        if ($accessToken !== '') {
            $headers[] = str_contains($accessToken, ':') ? $accessToken : 'Authorization: ' . $accessToken;
        }

        if ($extraHeader !== '') {
            $headers[] = $extraHeader;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $dataJson,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode((string) $response, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function job_status($jobId, $token): array
    {
        return function_exists('job_status') ? job_status($jobId, $token) : [];
    }
}
