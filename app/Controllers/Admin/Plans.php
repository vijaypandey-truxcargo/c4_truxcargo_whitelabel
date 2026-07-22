<?php

namespace App\Controllers\Admin;
use App\Controllers\Admin\Secure;
use App\Models\LoginModel;
use App\Models\SupportModel;

class Plans extends Secure
{
    public function __construct()
    {
        parent::__construct();
        
        helper(['url', 'form']);
        $this->supportModel = new SupportModel();
        $this->loginModel = new LoginModel();
        $this->wconfig = $this->supportModel->find('config', 1);

        if (! session()->get('admin_login_id')) {
            return redirect()->to('/admin/login')->send();
        }

    }
    
    public function index(){ 
        $data1 = $this->supportModel->search('plan', ['title' => 'Startup']) ?? (object) [];
        $data2 = $this->supportModel->search('plan', ['title' => 'Small Business']) ?? (object) [];
        $data3 = $this->supportModel->search('plan', ['title' => 'Enterprise']) ?? (object) [];

        $data = [
            'page_title' => 'Shipping Plans',
            'body'       => 'bg-light',
            'data'       => $this->supportModel->find_col('config', 'logo', 1),
            'wconfig'    => $this->wconfig,
            'admin_user' => session()->get('admin_user'),
            'permission' => $this->permission,
            'data1'      => $data1,
            'data2'      => $data2,
            'data3'      => $data3,
        ];

        $GLOBALS['permission'] = $this->permission;

        return view('admin/header', $data)
            . view('admin/plans', $data)
            . view('admin/footer');
    }

    public function save_plans()
    {
        $plans = [
            [
                'title' => 'Startup',
                'monthly' => $this->request->getPost('m1'),
                'quarterly' => $this->request->getPost('q1'),
                'half' => $this->request->getPost('s1'),
                'yearly' => $this->request->getPost('y1'),
            ],
            [
                'title' => 'Small Business',
                'monthly' => $this->request->getPost('m2'),
                'quarterly' => $this->request->getPost('q2'),
                'half' => $this->request->getPost('s2'),
                'yearly' => $this->request->getPost('y2'),
            ],
            [
                'title' => 'Enterprise',
                'monthly' => $this->request->getPost('m3'),
                'quarterly' => $this->request->getPost('q3'),
                'half' => $this->request->getPost('s3'),
                'yearly' => $this->request->getPost('y3'),
            ],
        ];

        foreach ($plans as $plan) {
            $existing = $this->supportModel->search('plan', ['title' => $plan['title']]);

            if ($existing && ! empty($existing->id)) {
                $this->supportModel->update('plan', $plan, $existing->id);
                continue;
            }

            $this->supportModel->insert('plan', $plan);
        }

        return redirect()->to('/admin/dashboard/plans')
            ->with('error', 'Successfully Updated.')
            ->with('error_class', 'alert-success');
    }

    public function plan_request() {
         $plan = $this->input->post('plan');   
       if($this->user->wallet=='Postpaid'){
           echo '<div class="modal-header">                
                <h4 class="modal-title"><strong id="show1">'.$plan.' Business Plan</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>';
        echo ' <div class="modal-body">
                    <div class="row">                                        
                           <div class="col-md-12">
                           <h4 style="color: red; text-align: center;">Before Active the business plan clear your Overdue and convert the account into prepaid.</h4>
                           </div>                           
                     </div>              
            </div>';
       }
       else{         
        $data = $this->supportmodel->search('plan',array('title'=>$plan));
         echo '<div class="modal-header">                
                <h4 class="modal-title"><strong id="show1">'.$plan.' Business Plan</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>    
            </div>';
        echo ' <div class="modal-body"><input type="hidden" class="form-control" id="plan1" name="plan" value="'.$plan.'"> 
                    <div class="row">                                        
                           <div class="col-md-12">
                           <select class="form-control" name="amount" id="amount">
                           <option value="'.trim($data->monthly).'">Monthly : '.$data->monthly.' + GST</option>
                            <option value="'.$data->quarterly.'">Quarterly : '.$data->quarterly.' + GST</option>
                           <option value="'.$data->half.'">Semi-Yearly : '.$data->half.' + GST</option>
                           <option value="'.$data->yearly.'">Yearly : '.$data->yearly.' + GST</option>     
                           </select>
                           </div>
                            <div class="col-md-12 mt-4">
                             <input type="text" name="coupon" class="form-control" onkeyup="coupon_check()" id="coupon" placeholder="Coupon Code">  
                             <span class="red" id="error3"></span>
                               </div>
                            <div class="col-md-12 mt-4">
                             <input type="radio" name="buy"  value="Wallet" checked>  Wallet&nbsp; &nbsp; 
                             <input type="radio" name="buy" value="Online">  Online
                            </div>
                           
                           <div class="col-md-12 text-center">
                           <div class="input-group mt-4" style="margin:auto; display:block">
                             <button class="buy_now btn btn-success">Pay Now</button>
                           </div>
                          </div>
                     </div>                
            </div>';
       }      
    }
    
    public function add() {
        $plan = $this->input->post('plan'); 
        $coupon = $this->input->post('coupon');
        $amt = $this->input->post('amount');
        $per = (int) filter_var($coupon, FILTER_SANITIZE_NUMBER_INT);
        if(!empty($coupon) && str_contains($coupon, $this->wconfig->plan) && str_contains($coupon, '%') && is_numeric($per) && $per<=50) {
            $amount = $this->input->post('amount') - $this->input->post('amount')*$per/100; }
        else { $amount = $this->input->post('amount'); }
        $gst = $amount*$this->user->gstpercentage/100;
        $total = $amount + $gst; 
        if(round($this->wallet)>=round($total)){
            $data = $this->supportmodel->search('plan',array('title'=>$plan));
            if($data->monthly==$amt){$day=1; $duration = 'Monthly';}
            else if($data->quarterly==$amt){$day=3; $duration = 'Quarterly';}
            else if($data->half==$amt){$day=6; $duration = 'Semi-Yearly';}
            else {$day=12; $duration = 'Yearly';}
            
            $edate =  date('Y-m-d', strtotime('+'.$day.' months'));
            $this->supportmodel->insert('buy_plan',array('login_id'=>$this->session->userdata('login_id'),'plan'=>$plan,'date'=>date('Y-m-d'),'edate'=>$edate,'status'=>'Active','coupon'=>$coupon));
            $post['login_id']=$this->session->userdata('login_id');
            if(date('m')>3){$title = $this->wconfig->inv."/".date('Y')."-".substr((date('Y')+1),2,3)."/";}  else {  $title = $this->wconfig->inv."/".(date('Y')-1)."-".substr((date('Y')),2,3)."/";}
            $bill = $this->supportmodel->search_col('b2b_billing','invoice_no');
            $post['title'] = $title;
            $post['invoice_no'] = $bill->invoice_no+1;
            $post['end_date'] = date('Y-m-d');
            $post['due_date'] = date('Y-m-d');            
            $post['service'] = 'Subscription Of '.$plan.' Plan for '.$duration;
            $post['qty'] = 1;
            $post['price'] = $amount;
            $post['amount'] = $total;
            $post['paid'] = $total;
            $post['status'] = 'Paid';
            $post['unpaid'] = 0;
            $post['live'] = '1';
            $id= $this->supportmodel->insert('b2b_billing',$post);
            
            $wdata1['invoice_no'] =$id;
            $wdata1['amount'] = $total; 
            $wdata1['method'] = 'Wallet';
            $wdata1['notes'] = 'Received From Wallet';
            $wdata1['date'] = date('Y-m-d');
            $this->supportmodel->insert('b2b_memo_billing',$wdata1);             
            
            $wdata['login_id'] = $this->session->userdata('login_id');
            $wdata['amount'] = '-'.$total; 
            $wdata['reason'] = 'Debit For Subscription Of '.$plan.' Plan';
            $wdata['status'] = 'Confirm';
            $wdata['order_id'] = $id;
            $wdata['add_by'] = 'Self';
            $wdata['date'] = date('Y-m-d H:i:s');
            $this->supportmodel->insert('wallet',$wdata); 
           echo '<img src="'.base_url('assets/images/BusinessPlan_tq.gif').'">                   
                   <h4>Thank you for Subscription Of '.$plan.' Plan for '.$duration.' </h4>
                   <a href="'. base_url("billing/b2b_pvt_info/{$id}").'" class="btn btn-success mt-4"><i class="fa fa-download"></i> Download Invoice</a>
                 ';
        }
        else {echo '<span style="color:red">Insufficent Balance</span>'; }
    }    
    
    public function order_request() { 
        $coupon = $this->input->post('coupon'); 
        $per = (int) filter_var($coupon, FILTER_SANITIZE_NUMBER_INT);
        if (!empty($coupon) && str_contains($coupon, $this->wconfig->plan) && str_contains($coupon, '%') && is_numeric($per) && $per<=50) {
            $amount = $this->input->post('amount') - $this->input->post('amount')*$per/100; }
        else { $amount = $this->input->post('amount'); }   
        $amount1 = $amount*100;   
        $data = ['amount' => $amount1,'currency' => 'INR','receipt' => $this->rand_string(8),'payment_capture' => 1];
        $data_json = json_encode($data);
        $apiurl= 'https://prod-api-static.razorpay.com/v1/orders'; 
        $accesstoken = 'Basic '.$this->wconfig->razar_token;
        $output = $this->curl_post($apiurl,$accesstoken,$data_json);
        echo $output['id'];
    }
    
    public function pay() { 
        $plan = $this->input->post('plan'); 
        $coupon = $this->input->post('coupon');
        $amt = $this->input->post('amount');
         $per = (int) filter_var($coupon, FILTER_SANITIZE_NUMBER_INT);
        if(!empty($coupon) && str_contains($coupon, $this->wconfig->plan) && str_contains($coupon, '%') && is_numeric($per) && $per<=50) {
            $amount = $this->input->post('amount') - $this->input->post('amount')*$per/100; }
        else { $amount = $this->input->post('amount'); }
        $gst = $amount*$this->user->gstpercentage/100;
        $total = round(($amount + $gst),2); 
        $data = $this->supportmodel->search('plan',array('title'=>$plan));
        if($data->monthly==$amt){$day=1; $duration = 'Monthly';}
        else if($data->quarterly==$amt){$day=3; $duration = 'Quarterly';}
        else if($data->half==$amt){$day=6; $duration = 'Semi-Yearly';}
        else {$day=12; $duration = 'Yearly';}
        $edate =  date('Y-m-d', strtotime('+'.$day.' months'));
        $this->supportmodel->insert('buy_plan',array('login_id'=>$this->session->userdata('login_id'),'plan'=>$plan,'date'=>date('Y-m-d'),'edate'=>$edate,'status'=>'Active','coupon'=>$coupon));
        
        $post['login_id']=$this->session->userdata('login_id');
        if(date('m')>3){$title = $this->wconfig->inv."/".date('Y')."-".substr((date('Y')+1),2,3)."/";}  else {  $title = $this->wconfig->inv."/".(date('Y')-1)."-".substr((date('Y')),2,3)."/";}
        $bill = $this->supportmodel->search_col('b2b_billing','invoice_no');
        $post['title'] = $title;
        $post['invoice_no'] = $bill->invoice_no+1;
        $post['end_date'] = date('Y-m-d');
        $post['due_date'] = date('Y-m-d');
        $post['service'] = 'Subscription Of '.$plan.' Plan for '.$duration;
        $post['qty'] = 1;
        $post['price'] = $amount;
        $post['amount'] = $total;
        $post['paid'] = $total;
        $post['status'] = 'Paid';
        $post['unpaid'] = 0;
        $post['live'] = '1';
        $id= $this->supportmodel->insert('b2b_billing',$post);
            
        $wdata['invoice_no'] =$id;
        $wdata['amount'] = $total; 
        $wdata['method'] = 'Razar Pay';
        $wdata['notes'] = 'Transaction Id '.$this->input->post('razorpay_payment_id');
        $wdata['date'] = date('Y-m-d');
        $this->supportmodel->insert('b2b_memo_billing',$wdata); 
        echo '<img src="'.base_url('assets/images/thankyou.gif').'" style="width:100%">                   
                   <h4>Thank you for Subscription Of '.$plan.' Plan for '.$duration.' </h4>
                   <a href="'. base_url("billing/b2b_pvt_info/{$id}").'" class="btn btn-success mt-4"><i class="fa fa-download"></i> Download Invoice</a>';
    }

    public function rand_string( $length ) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars),0,$length);
    }    
    
    public function view($plan) {
        $this->upper($plan." Pricing List");
        if($plan=='Free'){$suffix='';}
        else if($plan=='SME'){$suffix='Small Business';}
        else{$suffix=$plan;}
        $data=array();
     
        if($this->user->panel_count==1){
          if($this->user->panel=='Delhivery Dense'){$name='Economy';}else{$name = explode(' ',$this->user->panel)[1];}
         $data['cc'][0] = array('panel'=>$this->user->panel,'name'=>$name);
        }
        else if($this->user->panel_count==2){
         $data['cc'][0] = array('panel'=>$this->user->panel,'name'=>explode(' ',$this->user->panel)[1]);
          array_push($data["cc"],array('panel'=>'Delhivery Cargo','name'=>'Cargo'));
        }
        else if($this->user->panel_count==4){
          $data['cc'][0] = array('panel'=>$this->user->panel,'name'=>explode(' ',$this->user->panel)[1]);
          array_push($data["cc"],array('panel'=>'Delhivery Dense','name'=>'Economy'));
        }
        else if($this->user->panel_count==5){
          $data['cc'][0] = array('panel'=>'Delhivery Cargo','name'=>'Cargo');
            array_push($data["cc"],array('panel'=>'Delhivery Dense','name'=>'Economy'));
        } 
        else if($this->user->panel_count==3){
          $data['cc'][0] = array('panel'=>$this->user->panel,'name'=>explode(' ',$this->user->panel)[1]);
          array_push($data["cc"],array('panel'=>'Delhivery Dense','name'=>'Economy'));
          array_push($data["cc"],array('panel'=>'Delhivery Cargo','name'=>'Cargo'));
        }  
        if($this->user->panel!='Delhivery Lite'){ array_push($data["cc"],array('panel'=>'Delhivery Lite','name'=>'Lite'));  }
       
        $data['matrix']= $this->user->matrix;
      
        $data['matrix_charge']= function($panel){          
          return $this->divisor($panel,$this->session->userdata('login_id'));
        };
        $data['price']= function($panel,$table){
         $num = $this->supportmodel->getRows($table,array('login_id'=> $this->session->userdata('login_id'),'panel'=>$panel));
         if($num>0){$condition = "login_id='".$this->session->userdata('login_id')."'  and panel='".$panel."'";}
         else {$condition ="panel='".$panel."' and login_id=''";}
         return $this->supportmodel->show_condition($table,'ASC',$condition);
        };
      
        $data['dfuel'] = '';
        $data['minimum']= function($panel){
         $min = $this->mincost($panel,$this->session->userdata('login_id'));
         return $min;      
        };     
        $data['suffix']= $suffix;
        $data['plan']= $plan;
        $data['cost'] = 0;    
       
        $this->load->view('plan_rate',$data);
        $this->load->view('footer');
    }
    public function compare(){  
        $title ='Compare Pricing';      
        $this->upper($title);   
        $this->load->view('rate_compare');     
        $this->load->view('footer'); 
    }   
    public function submit() { 
        $pincode1 = $this->input->post('origin');
        $pincode2 = $this->input->post('destination');
        $weight1  = $this->input->post('weight');
        $mode  = $this->input->post('mode');
        $cod_amount  = $this->input->post('cod_amount');
        $profit  = $this->input->post('profit');
        $invoice_value  = $this->input->post('invoice');  
        $insurance_type  = $this->input->post('insurance'); 
        $abc_type  = $this->input->post('abd'); 
        $fm_type  = $this->input->post('selfdrop'); 
        $cs1 = $this->getState($pincode1);        
        $city1 = $cs1['city'];
        $state1 = $cs1['state'];         
        $cs2 =  $this->getState($pincode2);
        $city2 = $cs2['city'];
        $state2 = $cs2['state'];   
        
        $dim_wt=$qty=0;
        for($i=0;$i<sizeof($this->input->post('width'));$i++){
            $w = (float)$this->input->post('width')[$i]*(float)$this->input->post('length')[$i] * (float)$this->input->post('height')[$i]/4500;
            $dim_wt = (float)$this->input->post('count')[$i]*$w + $dim_wt;  
            $qty = (float)$this->input->post('count')[$i] + $qty;      
        }
        if($weight1>$dim_wt){$al_wt=$weight1;}else{$al_wt=$dim_wt;}
        
        if(!empty($state1) && !empty($state2) && round($al_wt,2)>2){
           if($this->user->panel_count==1){$ct= "'".$this->user->panel."'";}
           else if($this->user->panel_count==2){$ct = "'".$this->user->panel."','Delhivery Cargo'";}
           else if($this->user->panel_count==4){$ct = "'".$this->user->panel."','Delhivery Dense'";}
           else if($this->user->panel_count==5){$ct = "'Delhivery Cargo','Delhivery Dense'";}
           else {$ct = "'".$this->user->panel."','Delhivery Dense','Delhivery Cargo'";}
          
           if($this->user->dtdc=='Yes'){$ct.=",'DTDC B2B'";}
            $cc= "title IN (".$ct.",'Oxyzen','Delhivery Lite','Gati','Smartr','Ekart B2B')";
          
           $b2bpartner = $this->supportmodel->select_rows('b2b-partner','title,id,apikey','ASC',$cc); 
           foreach ($b2bpartner as $b2bpartner){
            $slab = array('',' Startup',' Small Business',' Enterprise');
            $suborders= array();
            foreach($slab as $slab){   
            $result = $this->b2bmatrix($b2bpartner->title);
            $matrix = $result['matrix'];
            $pname = $result['pname'];
            $pp = $result['pp'];   
             $b2bpanel= $b2bpartner->title.$slab;
           
            $info = $this->divisor($b2bpanel,$this->session->userdata('login_id'));
            $oda_charge = explode(',',$info->oda);
            $rov_charge = explode(',',$info->rov);               
            $hand_charge = explode(',',$info->handling);  
            $abc_charge = explode(',',$info->abc);  
            $fm_charge = explode(',',$info->fm);  
            $res='';
            if($b2bpartner->title=='Gati'){
                $url = $this->gatiurl.'GKEPincodeserviceablity.jsp?reqid='.$b2bpartner->apikey.'&pincode='.$pincode2;        
                $res = $this->curl_post($url, '',''); 
            }
            $service = $this->b2bservice($pincode1,$pincode2, $pp,$res,$state2,$pname,$this->user->bihar); 
            if($service=='Yes' && ($b2bpartner->title=='Gati' || $b2bpartner->title=='Oxyzen' || $b2bpartner->title=='Ekart B2B' || $b2bpartner->title=='DTDC B2B' || $b2bpartner->title=='Smartr') && $mode!='Prepaid'){
                $service='No';
                if($b2bpartner->title=='Gati' && $this->user->id=='1992'){$service='Yes';}
            }  
            if($service=='Yes'){ 
               $weight2=$handling=0;
              for($i=0;$i<sizeof($this->input->post('width'));$i++){
                $w = (float)$this->input->post('width')[$i]*(float)$this->input->post('length')[$i] * (float)$this->input->post('height')[$i]/$info->divisor;
                $weight2 = (float)$this->input->post('count')[$i]*$w + $weight2;     
               
                if($pp=='Gati' || $pp=='Ekart B2B'){$hand_charge2=$hand_charge[2];}
                else {$hand_charge2=0;}
                $handling =  $handling + $this->handling($w,$pname,(float)$this->input->post('count')[$i],$hand_charge[0],$hand_charge[1],$hand_charge2);
              }
              $zon = $this->b2b_zone($state1, $state2,$city1,$city2,$b2bpartner->title,$matrix,$pp,$pincode1,$pincode2,$this->session->userdata('login_id'));     
               $zone1= $zon['zone1']; $zone2= $zon['zone2'];  
              if(!empty($zone1) && !empty($zone2)){ 
                  $price = $this->price($zone1, $zone2,$b2bpanel,$zon['table'],$this->session->userdata('login_id'));          
                
                if($weight1>$weight2){$weight3 = $weight1;} else {$weight3 = $weight2;} 
               
                if($weight3>$info->weight){$weight = ceil($weight3);} else {$weight = $info->weight;}
              
                if($handling==0 || $qty==1){
                   $w= $weight/$qty; 
                   if($pp=='Gati' || $pp=='Ekart B2B'){$hand_charge2=$hand_charge[2];}
                   else {$hand_charge2=0;}
                   $handling1 = $this->handling($w,$pname,$qty,$hand_charge[0],$hand_charge[1],$hand_charge2);
                    if($handling1>$handling){$handling=$handling1;}
                }
               $charge = $price*$weight;            
               $fuel = $charge*$info->fsc/100;               
               if($b2bpartner->title=='Gati'){
                   $risk = $invoice_value/$weight;
                   if($risk>10000 && $risk<25000){ $rov_charge = explode(',',$info->insurance); }
                   else if($risk>25000){$rov_charge =array(11,250); }
               }                
               if($insurance_type=='Yes'){
                  $ins_charge = explode(',',$info->insurance); 
                  $rov = $invoice_value*$ins_charge[0]/100;
                  if(sizeof($ins_charge)>1 && !empty($ins_charge[1]) && $ins_charge[1]>$rov){$rov=$ins_charge[1];}
               }
               else {
                 $rov = $invoice_value*$rov_charge[0]/100;
                 if(sizeof($rov_charge)>1 && !empty($rov_charge[1]) && $rov_charge[1]>$rov){$rov=$rov_charge[1];}
               } 
               if($fm_type=='No' && sizeof($fm_charge)>0 && !empty($fm_charge[0])){ 
                  $fm = $weight*$fm_charge[0];
                  if(sizeof($fm_charge)>1 && !empty($fm_charge[1]) && $fm_charge[1]>$fm){$fm=$fm_charge[1];}
                }
               else { $fm=0; }
             
               if($abc_type=='Yes' && sizeof($abc_charge)>0 && !empty($abc_charge[0])){
                  $abc = $weight*$abc_charge[0];
                  if(sizeof($abc_charge)>1 && !empty($abc_charge[1]) && $abc_charge[1]>$abc){$abc=$abc_charge[1];}
                }
               else { $abc=0; }
               
               if($b2bpartner->title=='Rivigo' && substr($pincode2, 0,2)=='11'){
                   if($weight<150){ $green=150;} else {$green= round($weight);}                   
               }
               else if($b2bpartner->title=='Gati' && (substr($pincode2, 0,2)=='11' || $state2=='Nagaland' || $state2=='Manipur' || $state2=='Jammu and Kashmir' || $state2=='Mizoram' || $state2=='Arunachal Pradesh' || $state2=='Meghalaya' || $state2=='Tripura' || $state2=='Guwahati' || $state2=='Assam')){
                    $green=100;                
               }
               else {$green=0;} 
               $oda= $this->b2boda($pincode2,$pp, $weight,$oda_charge,$res,$pname);
               if($oda>0 && $b2bpartner->title!='Gati'){ $od=' <i class="fa fa-inr"></i> '.$oda_charge[0].'/kg or min: '.$oda_charge[1];}
               else if($b2bpartner->title=='Gati' || $b2bpartner->title=='DTDC B2B'){$od='';}
               else {$od='<i class="fa fa-inr"></i> 0/kg';}
               if($b2bpartner->title=='Rivigo'){
                    if($charge<$info->oda1){$charge=$info->oda1;}
                   $fuel = ($charge + $rov  + $info->docket + $handling + $green)*$info->fsc/100;
                }                
                if($b2bpartner->title=='Smartr' ){
                   if($weight<=100){$fm=100;}
                   else{$fm=$weight;}
                }
                
               $total = $charge + $fuel + $rov + $oda + $info->docket + $handling + $green + $fm + $abc;
             
               if($mode!='Prepaid'){
                   $topay = $info->topay;
                   if(empty($cod_amount)){ $cod= $total+$total*$this->user->gstpercentage/100; if($mode=='Franchise-ToPay'){$cod = $cod + $profit;} }
                   else { $cod=$cod_amount;}
                   if($b2bpartner->title=='Oxyzen'){$per=1.5;}
                   else {$per=0.2;}   
                   $tp= $cod*$per/100;
                   if($tp>$topay){$topay=$tp;}    
                   $total = $total + $topay;
                }
                $min = $this->mincost($b2bpanel,$this->session->userdata('login_id'));                  
                
                if($total<$min){$total=$min;}
                if($this->user->gstpre=='No'){$gst=0;} else{$gst = $total*$this->user->gstpercentage/100;}             
                $grand=  $total + $gst;
              
                if($mode=='COD'){$cod1= $cod_amount;} else if($mode=='To-Pay'){$cod1= $grand;} else if($mode=='Franchise-ToPay'){$cod1= $grand+$profit;} else {$cod1=$cod_amount;}
                 $item1 =   array ('slab' => $slab,'total' => (float)$grand);
                 array_push($suborders, $item1);
              }
            }
            }
             if($service=='Yes'){
                echo '<div class="accordion-item" >
                    <div class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#'.$pp.'" aria-expanded="false" aria-controls="'.$pp.'">
                            <div class="d-flex justify-content-between gap-5">
                                <div class="wraper-shiping">
                                    <img src="'.base_url().'assets/images/'.$pp.'.png"  style="width: 70px; height:70px" ;>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between gap-5">
                                       <div> <h4 class="mt-2">'.$pname.'</h4></div>
                                       <div>   <p>Charged Wt : '.$weight.' Kg</p></div>
                                    </div>
                                    <div class="row">' ;
                                        for($s=0;$s<sizeof($suborders);$s++){ 
                                            if(empty($suborders[$s]['slab'])){ $panel = 'Standard'; $color="black";}
                                            else if($suborders[$s]['slab']==' Small Business'){ $panel = 'SME';  $color="orange";}
                                            else { $panel = $suborders[$s]['slab']; if($suborders[$s]['slab']==' Startup'){ $color="green";} else { $color="blue";} }
                                            echo '<div class="col-lg-12 col-sm-12">                       
                                                 <h5 class="mt-2 mb-0 w-100 pull-left" style="color:'.$color.'">'.$panel.' : <span class="pull-right"';
                                                 if($this->plan!=trim($suborders[$s]['slab']) && !empty($this->plan)){ echo 'style="text-decoration:line-through"';}
                                                 echo '><i class="fa fa-inr"></i> '.round($suborders[$s]['total'],2).'</span></h5>';
                                             echo '</div>';}
                               echo '</div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>'; 
             }   
           }
        }
    }    
}
