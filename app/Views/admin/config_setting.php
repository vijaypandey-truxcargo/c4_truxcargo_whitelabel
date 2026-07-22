<style> .disabled{    background: #f1f1f1;} .title{color: #007bff}
    </style>
<div id="page-wrapper">
        <div class="col-md-12 graphs">
	   <div class="xs">
  	    <h3 class="pull-left">Configuration</h3>
		<div class="col-lg-12">
		  <?php $error=session()->getFlashdata('error');
                $error_class=session()->getFlashdata('error_class');
                if($error):?>
                  <div class="alert alert-dismissible <?= $error_class;?>">
                      <strong><?= $error;?></strong>
                  </div>
                <?php endif;?>
              </div>
	 <div class="clearfix"></div>
  	    <div class="well1 white">
               <?php if(isset($upload_error)){  echo "<span class='alert-danger'>".$upload_error."</span>";   }?>  
            <?= form_open_multipart("admin/dashboard/save_config/{$data->id}",['class'=>'form-floating'])?>              
           <fieldset>     
               <h4 class="title">Company Info</h4>
               <div class="col-lg-6">
                   <div class="form-group">
                    <label class="control-label">Company Name</label>
                     <input type="text" name="company" value="<?= set_value('company',$data->company)?>" class="form-control1" required="">
                    </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Company Logo</label>
              <input type="file" name="logo" class="form-control1" >
               <?php  if(!empty($data->logo)){ echo '<img src="'.base_url().'uploads/profile/'.$data->logo.'" width="150">';}?>
              </div>
               </div>
               <div class="col-lg-6">
                   <div class="form-group">
                    <label class="control-label">Director Name</label>
                     <input type="text" name="name" value="<?= set_value('name',$data->name)?>" class="form-control1" required="">
                    </div>
               </div>
               <div class="col-lg-6">
                   <div class="form-group">
                    <label class="control-label">Website URL</label>
                     <input type="text" name="website" value="<?= set_value('website',$data->website)?>" class="form-control1" required="">
                    </div>
               </div>
               
               <div class="col-lg-6">
                   <div class="form-group">
                       <label class="control-label"> Email ID<span>(for Sending Email)</span></label>
                     <input type="text" name="email" value="<?= set_value('email',$data->email)?>" class="form-control1" required="">
                    </div>
               </div>
              <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Email Id Password</label>
              <input type="text" name="password" value="<?= set_value('password',$data->password)?>" onkeyup="checkInput(this)" class="form-control1" required="">
              </div>
              </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Address</label>
              <input type="text" name="address" class="form-control1" value="<?= set_value('address',$data->address)?>" required="">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">State</label>
              <input type="text" name="state" class="form-control1" value="<?= set_value('state',$data->state)?>" required="">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">CIN</label>
              <input type="text" name="cin" class="form-control1" value="<?= set_value('cin',$data->cin)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">PAN</label>
              <input type="text" name="pan" class="form-control1" value="<?= set_value('pan',$data->pan)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">GST NO</label>
              <input type="text" name="gst" class="form-control1" value="<?= set_value('gst',$data->gst)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">TAN</label>
              <input type="text" name="tan" class="form-control1" value="<?= set_value('tan',$data->tan)?>">
              </div>
               </div>
               <div class="clearfix"></div>
               <hr>
               <h4 class="title">Bank Info</h4>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Bank Account holder Name</label>
              <input type="text" name="holder_name" class="form-control1" value="<?= set_value('holder_name',$data->holder_name)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Bank Account No</label>
              <input type="text" name="bank" class="form-control1" value="<?= set_value('bank',$data->bank)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">IFSC</label>
              <input type="text" name="ifsc" class="form-control1" value="<?= set_value('ifsc',$data->ifsc)?>">
              </div>
               </div>              
               
               
               <div class="clearfix"></div>
               <hr>
               <h4 class="title">Shipping Partner</h4>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Delhivery Dense Master Waybill (3 Digits)</label>
              <input type="text" name="dense" class="form-control1" value="<?= set_value('dense',$data->dense)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Delhivery Cargo Master Waybill (3 Digits)</label>
              <input type="text" name="cargo" class="form-control1" value="<?= set_value('cargo',$data->cargo)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Delhivery Lite Master Waybill (3 Digits)</label>
              <input type="text" name="lite" class="form-control1" value="<?= set_value('lite',$data->lite)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Rivigo Client Code</label>
              <input type="text" name="rivigo_code" class="form-control1" value="<?= set_value('rivigo_code',$data->rivigo_code)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Rivigo Basic Token</label>
              <input type="text" name="rivigo_token" class="form-control1" value="<?= set_value('rivigo_token',$data->rivigo_token)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Amazon Refresh Token</label>
              <input type="text" name="refresh_token" class="form-control1" value="<?= set_value('refresh_token',$data->refresh_token)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Amazon Client_ID</label>
              <input type="text" name="client_id" class="form-control1" value="<?= set_value('client_id',$data->client_id)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Amazon Client_Secret</label>
              <input type="text" name="client_secret" class="form-control1" value="<?= set_value('client_secret',$data->client_secret)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">India Post contract number</label>
              <input type="text" name="india" class="form-control1" value="<?= set_value('india',$data->india)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">India Post x-request-id</label>
              <input type="text" name="xrequest" class="form-control1" value="<?= set_value('xrequest',$data->xrequest)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Borzo Token</label>
              <input type="text" name="borzo" class="form-control1" value="<?= set_value('borzo',$data->borzo)?>">
              </div>
               </div>
               <div class="clearfix"></div>
               <hr>
               <h4 class="title">KYC & SMS</h4>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">KYC Token(Surepass)</label>
              <input type="text" name="kyc" class="form-control1" value="<?= set_value('kyc',$data->kyc)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">SMS Key</label>
              <input type="text" name="sms_key" class="form-control1" value="<?= set_value('sms_key',$data->sms_key)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">SMS Header</label>
              <input type="text" name="sms_header" class="form-control1" value="<?= set_value('sms_header',$data->sms_header)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Whatsapp URL</label>
              <input type="text" name="whatsapp" class="form-control1" value="<?= set_value('whatsapp',$data->whatsapp)?>">
              </div>
               </div>
               <div class="clearfix"></div>
               <hr>
               <h4 class="title">Plugin</h4>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Magento Plugin URL</label>
              <input type="text" name="magento" class="form-control1" value="<?= set_value('magento',$data->magento)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Woocommerce Plugin URL</label>
              <input type="text" name="woocommerce" class="form-control1" value="<?= set_value('woocommerce',$data->woocommerce)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Easyecom Plugin URL</label>
              <input type="text" name="easyecom" class="form-control1" value="<?= set_value('easyecom',$data->easyecom)?>">
              </div>
               </div>
               
               
               <div class="clearfix"></div>
               <hr>
               <h4 class="title">Payment Gateway</h4>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Razarpay Token</label>
              <input type="text" name="razar_token" class="form-control1" value="<?= set_value('razar_token',$data->razar_token)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Razarpay Key</label>
              <input type="text" name="razar_key" class="form-control1" value="<?= set_value('razar_key',$data->razar_key)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Paytm MID</label>
              <input type="text" name="paytm_mid" class="form-control1" value="<?= set_value('paytm_mid',$data->paytm_mid)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Paytm Key</label>
              <input type="text" name="paytm_key" class="form-control1" value="<?= set_value('paytm_key',$data->paytm_key)?>">
              </div>
               </div>               
               
               <div class="clearfix"></div>
               <hr>
               <h4 class="title">Other Info</h4>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Shipping Plan Discount Code Prefix (TRUX20%)</label>
              <input type="text" name="plan" class="form-control1" value="<?= set_value('plan',$data->plan)?>" placeholder="TRUX">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Invoice Series Prefix</label>
              <input type="text" name="inv" class="form-control1" value="<?= set_value('inv',$data->inv)?>">
              </div>
               </div>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">UserID Prefix</label>
              <input type="text" name="uprefix" class="form-control1" value="<?= set_value('uprefix',$data->uprefix)?>">
              </div>
               </div>
               
              <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Google Map Api</label>
              <input type="text" name="gmap" class="form-control1" value="<?= set_value('gmap',$data->gmap)?>">
              </div>
               </div>
               <div class="clearfix"></div>
               <hr>
               <h4 class="title">Gmail Signup</h4>
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Google App Name</label>
              <input type="text" name="gapp" class="form-control1" value="<?= set_value('gapp',$data->gapp)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Google oauth2_client_id</label>
              <input type="text" name="oauth2_client_id" class="form-control1" value="<?= set_value('oauth2_client_id',$data->oauth2_client_id)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Google oauth2_client_secret</label>
              <input type="text" name="oauth2_client_secret" class="form-control1" value="<?= set_value('oauth2_client_secret',$data->oauth2_client_secret)?>">
              </div>
               </div>
               
               <div class="col-lg-6">
              <div class="form-group">
              <label class="control-label">Google oauth2_redirect_uri</label>
              <input type="text" name="oauth2_redirect_uri" class="form-control1" value="<?= set_value('oauth2_redirect_uri',$data->oauth2_redirect_uri)?>">
              </div>
               </div>
               
               
               
            <div class="col-lg-12">
            <div class="form-group">
               <?= form_submit(['name'=>'submit','value'=>'Save','class'=>'btn btn-primary'])?>
             </div>
            </div>
          </fieldset>
         <?= form_close();?>
      </div>
    </div>
     
