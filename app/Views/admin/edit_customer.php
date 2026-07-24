<style>
  .disabled{
    background: #f1f1f1;
  }
  .error-border {
    border: 2px solid red;
    padding: 5px;
    border-radius: 6px;
  }
  .required:after {
        content:" *";
        color:red;
        font-weight:bold;
    }
</style>
<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
  	    <h3 class="pull-left">Edit Customer</h3>
            <?= anchor('admin/customer/all','View All Customer',['class'=>'btn btn-success pull-right']);?>
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
            <?= form_open_multipart("admin/customer/update_customer/{$data->id}",['class'=>'form-floating'])?>
             <fieldset>
                 <h3>Primary Info</h3>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Username</label>
                     <?= form_input(['name'=>'username', 'class'=>'form-control1 disabled', 'value'=>set_value('username',$data->username),'readonly'=>'readonly']);?>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="required">First Name</label>
                     <?= form_input(['name'=>'first', 'class'=>'form-control1', 'value'=>set_value('first',$data->first),'required'=>'required']);?>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="required">Last Name</label>
                     <?= form_input(['name'=>'last', 'class'=>'form-control1', 'value'=>set_value('last',$data->last),'required'=>'required']);?>
                 </div>
               </div>
               <!-- <div class="col-lg-4">
                 <div class="form-group">
                    <label class="required">Customer Code</label>
                     <?= form_input(['name'=>'code', 'class'=>'form-control1', 'value'=>set_value('code',$data->code)]);?>
                    
                 </div>
               </div> -->
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="required">Phone</label>
                     <?= form_input(['name'=>'phone', 'class'=>'form-control1', 'value'=>set_value('phone',$data->phone),'required'=>'required']);?>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="required">Email Id</label>
                     <?= form_input(['name'=>'email', 'class'=>'form-control1', 'value'=>set_value('email',$data->email),'required'=>'required']);?>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="required">Company Name</label>
                     <!-- <?= form_input(['name'=>'company', 'class'=>'form-control1', 'value'=>set_value('company',$data->company),'required'=>'required']);?>     -->
                     <select class="form-control1" name="company" id="company_id" required>
                        <option value="">SELECT Anyone</option>
                        <?php foreach($company as $row){ ?>
                            <option value="<?= $row->id ?>"
                                <?= ($row->id == $data->company) ? 'selected' : '' ?>>
                                <?= $row->name ?>
                            </option>
                        <?php } ?>
                    </select>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Password</label>
                     <?= form_input(['name'=>'password', 'class'=>'form-control1', 'value'=>set_value('password',$data->password),'required'=>'required']);?>
                 </div>
               </div>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Aadhar No</label>
                     <?= form_input(['name'=>'aadhar', 'class'=>'form-control1', 'value'=>set_value('aadhar',$data->aadhar)]);?>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">PAN Card</label>
                     <?= form_input(['name'=>'pan', 'class'=>'form-control1', 'value'=>set_value('pan',$data->pan),'oninput'=>"this.value = this.value.toUpperCase()"]);?>
                 </div>
               </div>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">KYC</label>
                     <select name="kyc" class="form-control readonly">
                          <option value="1" <?php if(set_value('kyc',$data->kyc)=='1'){ echo 'selected="selected"';}?>>Verified KYC</option>
                        <option value="0" <?php if(set_value('kyc',$data->kyc)=='0'){ echo 'selected="selected"';}?>>Incomplet KYC</option>
                     </select>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">GST Percentage.</label>
                     <?= form_input(['name'=>'gstpercentage', 'class'=>'form-control1', 'value'=>set_value('gstpercentage',$data->gstpercentage)]);?>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">GST Apply</label>
                    <select name="gstpre" class="form-control">
                         <option value=""></option>
                        <option value="Yes" <?php if(set_value('gstpre',$data->gstpre)=='Yes'){ echo 'selected="selected"';}?>>Yes</option>
                       <option value="No" <?php if(set_value('gstpre',$data->gstpre)=='No'){ echo 'selected="selected"';}?>>No</option>
                    </select>
                  </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">User Type</label>
                    <select name="type" class="form-control">
                        <option value="" <?php if(set_value('type',$data->type)==''){ echo 'selected="selected"';}?>>None</option>
                        <option value="Customer" <?php if(set_value('type',$data->type)=='Customer'){ echo 'selected="selected"';}?>>Customer</option>
                       <option value="Transporter" <?php if(set_value('type',$data->type)=='Transporter'){ echo 'selected="selected"';}?>>Transporter</option>
                    </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label class="required">Hub</label>
                     <select class="form-control1" name="hub" id="company_id" required>
                      <option value="">SELECT Anyone</option>
                      <?php foreach($hub as $c){ ?>
                          <option value="<?= $c->id ?>"
                              <?= ($data->hub == $c->id) ? 'selected' : '' ?>>
                              <?= $c->name ?>
                          </option>
                      <?php } ?>
                  </select>
                  </div>
                </div>
                 <div class="col-lg-4">
                  <div class="form-group">
                     <label>Divisor</label>
                      <?= form_input(['name'=>'divisor',  'value'=>set_value('phone',$data->divisor), 'class'=>'form-control1']);?>
                  </div>
                   </div>
                 <div class="clearfix"></div>
                 <hr>
                 <h3>Billing Info</h3>
                  <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">GST NO.</label>
                     <?= form_input(['name'=>'gst', 'class'=>'form-control1', 'value'=>set_value('gst',$data->gst), 'oninput'=>"this.value = this.value.toUpperCase()"]);?>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Address</label>
                     <?= form_input(['name'=>'address', 'class'=>'form-control1', 'value'=>set_value('address',$data->address)]);?>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">City</label>
                     <?= form_input(['name'=>'city', 'class'=>'form-control1', 'value'=>set_value('city',$data->city)]);?>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">State</label>
                    <select name="state" class="form-control">
                        <option value="">Select Any State</option>
                         <?php foreach ($state as $state1){?>
                        <option value="<?= $state1->state;?>" <?php if(set_value('state',$data->state)==$state1->state){ echo 'selected="selected"';}?>><?= $state1->state;?></option>
                         <?php }?>
                     </select>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Pincode</label>
                     <?= form_input(['name'=>'pincode', 'class'=>'form-control1', 'value'=>set_value('pincode',$data->pincode),'onkeyup'=>'checkInput(this)']);?>
                 </div>
                </div>


                <div class="col-lg-4">
                  <div class="form-group">
                     <label>Bank Account Number</label>
                     <input type="text" name="bank_account_number" class="form-control1" value="<?= $data->bank_account_number ?>">
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Bank IFSC Code</label>
                     <input type="text" name="bank_ifsc_code" class="form-control1" value="<?= $data->bank_ifsc_code ?>">
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>TDS Percentage</label>
                     <input type="text" name="tds_percentage" class="form-control1" value="<?= $data->tds_percentage ?>">
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Sales Man</label>
                     <select name="sales_man" class="form-control">
                        <option value="">Select</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u->id ?>" <?= ($data->sales_man == $u->id) ? 'selected' : '' ?>>
                                <?= $u->userName ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>CS Person</label>
                        <select name="cs_person" class="form-control">
                          <option value="">Select</option>
                          <?php foreach ($users as $u): ?>
                              <option value="<?= $u->id ?>" <?= ($data->cs_person == $u->id) ? 'selected' : '' ?>>
                                  <?= $u->userName ?>
                              </option>
                          <?php endforeach; ?>
                      </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Billing Person</label>
                     <select name="billing_person" class="form-control">
                        <option value="">Select</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u->id ?>" <?= ($data->billing_person == $u->id) ? 'selected' : '' ?>>
                                <?= $u->userName ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Child code</label>
                     <input type="text" name="child_code" class="form-control1" value="<?= $data->child_code ?>">
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Contrext ID</label>
                     <input type="text" name="context_id" class="form-control1" value="<?= $data->context_id ?>">
                  </div>
               </div>


                <div class="clearfix"></div>
                 <hr>
                 <h3>Preference</h3>

               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">B2B Wallet </label>
                   <select name="wallet" class="form-control">
                        <option value="Prepaid" <?php if(set_value('wallet',$data->wallet)=='Prepaid'){ echo 'selected="selected"';}?>>Prepaid</option>
                       <option value="Postpaid" <?php if(set_value('wallet',$data->wallet)=='Postpaid'){ echo 'selected="selected"';}?>>Postpaid</option>
                    </select>
                  </div>
               </div>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">B2C Wallet</label>
                   <select name="b2c_wallet" class="form-control">
                        <option value="Prepaid" <?php if(set_value('b2c_wallet',$data->b2c_wallet)=='Prepaid'){ echo 'selected="selected"';}?>>Prepaid</option>
                       <option value="Postpaid" <?php if(set_value('b2c_wallet',$data->b2c_wallet)=='Postpaid'){ echo 'selected="selected"';}?>>Postpaid</option>
                    </select>
                  </div>
               </div>
               
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Credit Limit</label>
                     <?= form_input(['name'=>'capping', 'class'=>'form-control1', 'value'=>set_value('capping',$data->capping)]);?>
                 </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Aging Limit</label>
                     <?= form_input(['name'  => 'aging_limit', 'class' => 'form-control1', 'value' => set_value('aging_limit', $data->aging_limit)]); ?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                      <label>Payment type</label>
                      <select name="payment_type" class="form-control">
                          <option value="">Select All</option>
                          <?php
                              $payment_type = $data->payment_type ?? "";
                              $options = [
                                  "CASH", "CHEQUE", "ONLINE", "CREDIT", "FOC", "COD", "TO PAY", "CARD", "PREPAID", "CASH ON DELIVERY", "CHEQUE ON DELIVERY", "TO PAY + COD", "TOTAL PREPAID", "PENDING"
                              ];
                              foreach ($options as $opt){ ?>
                              <option value="<?= $opt ?>" <?= ($payment_type == $opt) ? 'selected' : '' ?>>
                                  <?= $opt ?>
                              </option>
                          <?php } ?>
                      </select>
                  </div>
                </div>
                <?php
                    $invoice_cycle  = $data->invoice_cycle;
                    $invoice_head   = $data->invoice_head;
                ?>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Invoice Cycle</label>
                        <select name="invoice_cycle" class="form-control">
                          <option value="">Select All</option>
                          <option value="WEEKLY"      <?= ($invoice_cycle == "WEEKLY") ? "selected" : ""; ?> >WEEKLY</option>
                          <option value="FORTNIGHTLY" <?= ($invoice_cycle == "FORTNIGHTLY") ? "selected" : ""; ?> >FORTNIGHTLY</option>
                          <option value="MONTHLY"     <?= ($invoice_cycle == "MONTHLY") ? "selected" : ""; ?> >MONTHLY</option>
                          <option value="Daily"       <?= ($invoice_cycle == "DAILY") ? "selected" : ""; ?> >DAILY</option>
                        </select>
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                        <label>Invoice Head</label>
                        <select name="invoice_head" class="form-control">
                          <option value="">Select All</option>
                          <option value="TAX INVOICE"<?= ($invoice_head == "TAX INVOICE") ? "selected" : ""; ?>>TAX INVOICE</option>
                          <option value="TAX INVOICE - FWD NO"<?= ($invoice_head == "TAX INVOICE - FWD NO") ? "selected" : ""; ?>>TAX INVOICE - FWD NO</option>
                        </select>
                    </div>
                  </div>

               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Capping Activation Date</label>
                     <div class="input-group" style="width:94%">
                          <input  name="capping_date" id="pdate" type="text" value="<?= $data->capping_date?>" placeholder="Capping Activation Date" class="form-control">
                          <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      </div>
                 </div>
               </div>
                <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Capping Expire Date</label>
                     <div class="input-group" style="width:94%">
                          <input  name="capping_expire" id="datepicker1" type="text" value="<?= $data->capping_expire?>" placeholder="Capping Expire Date" class="form-control">
                          <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      </div>
                 </div>
               </div>

                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Commission Percentage</label>
                     <?= form_input(['name'=>'commission', 'class'=>'form-control1', 'value'=>set_value('commission',$data->commission), 'onkeyup'=>'checkInput(this)']);?>
                 </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                      <label class="control-label">Remittance</label>
                      <select name="preference" class="form-control">
                        <option value="Wallet" <?php if(set_value('preference',$data->preference)=='Wallet'){ echo 'selected="selected"';}?>>Wallet</option>
                        <option value="Bank" <?php if(set_value('preference',$data->preference)=='Bank'){ echo 'selected="selected"';}?>>Bank</option>
                      </select>
                    </div>
               </div>

               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" <?php if(set_value('status',$data->status)==1){ echo 'selected="selected"';}?>>Active</option>
                       <option value="0" <?php if(set_value('status',$data->status)==0){ echo 'selected="selected"';}?>>Inactive</option>
                    </select>
                  </div>
               </div>

               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">B2B Panel</label>
                     <select name="panel" class="form-control">
                         <?php foreach ($key as $key){?>
                        <option value="<?= $key->title;?>" <?php if(set_value('panel',$data->panel)==$key->title){ echo 'selected="selected"';}?>><?= $key->title;?></option>
                         <?php }?>
                     </select>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">B2B Panel Count</label>
                     <select name="panel_count" class="form-control">
                         <option value="1" <?php if(set_value('panel_count',$data->panel_count)==1){ echo 'selected="selected"';}?>>Single Panel</option>
                         <option value="2" <?php if(set_value('panel_count',$data->panel_count)==2){ echo 'selected="selected"';}?>>B2B & Cargo</option>
                         <option value="4" <?php if(set_value('panel_count',$data->panel_count)==4){ echo 'selected="selected"';}?>>B2B & Dense</option>
                         <option value="5" <?php if(set_value('panel_count',$data->panel_count)==5){ echo 'selected="selected"';}?>>Dense & Cargo</option>
                         <option value="3" <?php if(set_value('panel_count',$data->panel_count)==3){ echo 'selected="selected"';}?>>All Panels</option>
                      </select>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">B2B Matrix</label>
                     <select name="matrix" class="form-control">
                         <option value="5" <?php if(set_value('matrix',$data->matrix)==5){ echo 'selected="selected"';}?>>5 Matrix</option>
                         <option value="16" <?php if(set_value('matrix',$data->matrix)==16){ echo 'selected="selected"';}?>>16 Matrix</option>
                         <option value="24" <?php if(set_value('matrix',$data->matrix)==24){ echo 'selected="selected"';}?>>24 Matrix</option>
                      </select>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Plan Tpe</label>
                     <select name="plan" class="form-control">
                         <option value="" <?php if(set_value('plan',$data->plan)==''){ echo 'selected="selected"';}?>>None</option>
                         <option value="Startup" <?php if(set_value('plan',$data->plan)=='Startup'){ echo 'selected="selected"';}?>>Startup</option>
                         <option value="Small Business" <?php if(set_value('plan',$data->plan)=='Small Business'){ echo 'selected="selected"';}?>>Small Business</option>
                         <option value="Enterprise" <?php if(set_value('plan',$data->plan)=='Enterprise'){ echo 'selected="selected"';}?>>Enterprise</option>
                      </select>
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Plan Expire Date</label>
                     <div class="input-group" style="width:94%">
                          <input  name="expire" id="pickup_date" type="text" value="<?= $data->expire?>" placeholder="Plan Expire Date" class="form-control">
                          <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      </div>
                 </div>
               </div>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Bihar</label>
                    <select name="bihar" class="form-control">
                          <option value=""></option>
                       <option value="Yes" <?php if(set_value('bihar',$data->bihar)=='Yes'){ echo 'selected="selected"';}?>>Yes</option>
                       <option value="No" <?php if(set_value('bihar',$data->bihar)=='No'){ echo 'selected="selected"';}?>>No</option>
                    </select>
                  </div>
               </div>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Finance Embargo</label>
                     <select name="embargo" class="form-control readonly">
                        <option value="1" <?php if(set_value('embargo',$data->embargo)=='1'){ echo 'selected="selected"';}?>>Active</option>
                        <option value="0" <?php if(set_value('embargo',$data->embargo)=='0'){ echo 'selected="selected"';}?>>Inactive</option><
                     </select>
                 </div>
               </div>
                  <div class="clearfix"></div>
                 <hr>
                 <h3>Panel Status</h3>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Rivigo Status</label>
                    <select name="fedex" class="form-control">
                          <option value=""></option>
                        <option value="Yes" <?php if(set_value('fedex',$data->fedex)=='Yes'){ echo 'selected="selected"';}?>>Yes</option>
                       <option value="No" <?php if(set_value('fedex',$data->fedex)=='No'){ echo 'selected="selected"';}?>>No</option>
                    </select>
                  </div>
               </div>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Delhivery Status</label>
                    <select name="delhivery" class="form-control">
                          <option value=""></option>
                        <option value="Yes" <?php if(set_value('delhivery',$data->delhivery)=='Yes'){ echo 'selected="selected"';}?>>Yes</option>
                       <option value="No" <?php if(set_value('delhivery',$data->delhivery)=='No'){ echo 'selected="selected"';}?>>No</option>
                    </select>
                  </div>
               </div>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">DTDC Status</label>
                    <select name="dtdc" class="form-control">
                          <option value=""></option>
                        <option value="Yes" <?php if(set_value('dtdc',$data->dtdc)=='Yes'){ echo 'selected="selected"';}?>>Yes</option>
                       <option value="No" <?php if(set_value('dtdc',$data->dtdc)=='No'){ echo 'selected="selected"';}?>>No</option>
                    </select>
                  </div>
               </div>

                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">B2C Status</label>
                    <select name="xpress" class="form-control">
                          <option value=""></option>
                        <option value="Yes" <?php if(set_value('xpress',$data->xpress)=='Yes'){ echo 'selected="selected"';}?>>Yes</option>
                       <option value="No" <?php if(set_value('xpress',$data->xpress)=='No'){ echo 'selected="selected"';}?>>No</option>
                    </select>
                  </div>
               </div>
                  <div class="clearfix"></div>
                 <hr>
                 <h3>Attachment</h3>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">ID Proof   <small>(Max Size: 100KB)</small></label>
                     <input type="file" name="image"  class="form-control"/>
                    <?php  if(!empty($data->image)){if(explode('.',$data->image)[1]!='pdf'){ echo '<a href="'.base_url().'uploads/profile/'.$data->image.'" target="_blank"><img src="'.base_url().'uploads/profile/'.$data->image.'" width="150"></a>';}
                    else { echo '<a href="'.base_url().'uploads/profile/'.$data->image.'" target="_blank"><embed src="'.base_url().'uploads/profile/'.$data->image.'" style="width:100%; height:200px" /></a>';}}?>

                 </div>
               </div>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Agreement <small>(Max Size: 200KB)</small></label>
                     <input type="file" name="agreement"  class="form-control"/>
                    <?php  if(!empty($data->agreement) && explode('.',$data->agreement)[1]='pdf'){  echo '<a href="'.base_url().'uploads/profile/'.$data->agreement.'" target="_blank"><embed src="'.base_url().'uploads/profile/'.$data->agreement.'" style="width:100%; height:200px" /></a>';}?>

                 </div>
               </div>

               <div class="col-lg-12">
                 <div class="form-group">
                    <label class="control-label">Remark</label>
                     <?= form_input(['name'=>'remark', 'class'=>'form-control1', 'value'=>set_value('remark',$data->remark)]);?>
                 </div>
               </div>

               <div class="clearfix"></div>
               <hr>
               <!-- ================= Escalation Level ======================= -->
              <h3>Escalation</h3>
                <div id="contact_section">
                    <div id="contact_rows">
                        <?php $i = 1; if(!empty($escalation)) { foreach($escalation as $e) { ?>
                        <div class="row contact_row" id="row<?= $i ?>" style="margin-bottom:10px;">
                            <div class="col-md-2">
                                <input type="text" name="c_name[]" class="form-control" value="<?= $e->name ?>" placeholder="Name">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="c_phone[]" class="form-control" value="<?= $e->phone ?>" placeholder="Phone">
                            </div>
                            <div class="col-md-2">
                                <input type="email" name="c_email[]" class="form-control" value="<?= $e->email ?>" placeholder="Email ID">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="c_designation[]" class="form-control" value="<?= $e->designation ?>" placeholder="Designation">
                            </div>
                            <div class="col-md-2">
                                <select name="c_department[]" class="form-control">
                                    <option value="">Department</option>
                                    <option value="BILLING" <?= $e->department == "BILLING" ? "selected" : "" ?>>BILLING</option>
                                    <option value="CUSTOMER SERVICE" <?= $e->department == "CUSTOMER SERVICE" ? "selected" : "" ?>>CUSTOMER SERVICE</option>
                                    <option value="OPERATIONS" <?= $e->department == "OPERATIONS" ? "selected" : "" ?>>OPERATIONS</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select name="c_escalation[]" class="form-control">
                                    <option value="">Escalation</option>
                                    <option value="Level 1" <?= $e->escalation_level == "Level 1" ? "selected" : "" ?>>Level 1</option>
                                    <option value="Level 2" <?= $e->escalation_level == "Level 2" ? "selected" : "" ?>>Level 2</option>
                                    <option value="Level 3" <?= $e->escalation_level == "Level 3" ? "selected" : "" ?>>Level 3</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <?php if($i == 1) { ?>
                                    <button id="addRow" type="button" class="btn btn-primary w-100">+</button>
                                <?php } else { ?>
                                    <button type="button" id="<?= $i ?>" class="btn btn-danger w-100 btn_remove">X</button>
                                <?php } ?>
                            </div>
                        </div>
                        <?php $i++; } } else { ?>

                        <!-- FIRST TIME → BLANK FIELDS -->
                        <div class="row contact_row" id="row1" style="margin-bottom:10px;">
                            <div class="col-md-2">
                                <input type="text" name="c_name[]" class="form-control" placeholder="Name">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="c_phone[]" class="form-control" placeholder="Phone">
                            </div>
                            <div class="col-md-2">
                                <input type="email" name="c_email[]" class="form-control" placeholder="Email ID">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="c_designation[]" class="form-control" placeholder="Designation">
                            </div>
                            <div class="col-md-2">
                                <select name="c_department[]" class="form-control">
                                    <option value="">Department</option>
                                    <option value="BILLING">BILLING</option>
                                    <option value="CUSTOMER SERVICE">CUSTOMER SERVICE</option>
                                    <option value="OPERATIONS">OPERATIONS</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select name="c_escalation[]" class="form-control">
                                    <option value="">Escalation</option>
                                    <option value="Level 1">Level 1</option>
                                    <option value="Level 2">Level 2</option>
                                    <option value="Level 3">Level 3</option>
                                    <option value="Level 4">Level 4</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button id="addRow" type="button" class="btn btn-primary w-100">+</button>
                            </div>
                        </div>

                        <?php } ?>
                    </div>
</div>

               <div class="col-lg-4">
               <div class="form-group">
               <?php if(in_array("Add Customer", $GLOBALS['permission'])){ echo form_submit(['name'=>'submit','value'=>'Update','class'=>'btn btn-primary']);}?>
             </div>
                   </div>
          </fieldset>
         <?= form_close();?>
      </div>
    </div>

    <script>
      var i = $("#contact_rows .contact_row").length;

      $("#addRow").click(function () {
          i++;
          $("#contact_rows").append(`
              <div class="row contact_row" id="row${i}" style="margin-bottom:10px;">
                  <div class="col-md-2"><input type="text" name="c_name[]" class="form-control1" placeholder="Name"></div>
                  <div class="col-md-2"><input type="text" name="c_phone[]" class="form-control1" placeholder="Phone"></div>
                  <div class="col-md-2"><input type="email" name="c_email[]" class="form-control1" placeholder="Email ID"></div>
                  <div class="col-md-2"><input type="text" name="c_designation[]" class="form-control1" placeholder="Designation"></div>
                  <div class="col-md-2">
                      <select name="c_department[]" class="form-control1">
                          <option value="">Department</option>
                          <option value="BILLING">BILLING</option>
                          <option value="CUSTOMER SERVICE">CUSTOMER SERVICE</option>
                          <option value="OPERATIONS">OPERATIONS</option>
                      </select>
                  </div>
                  <div class="col-md-1">
                      <select name="c_escalation[]" class="form-control1">
                          <option value="">Escalation</option>
                          <option value="Level 1">Level 1</option>
                          <option value="Level 2">Level 2</option>
                          <option value="Level 3">Level 3</option>
                          <option value="Level 4">Level 4</option>
                      </select>
                  </div>
                  <div class="col-md-1">
                      <button type="button" id="${i}" class="btn btn-danger w-100 btn_remove">X</button>
                  </div>
              </div>
          `);
      });

      $(document).on("click", ".btn_remove", function () {
          var id = $(this).attr("id");
          $("#row" + id).remove();
      });

      $("form").submit(function (e) {
    let isValid = true;

    $(".contact_row").each(function () {
        let name = $(this).find("input[name='c_name[]']").val().trim();
        let phone = $(this).find("input[name='c_phone[]']").val().trim();
        let email = $(this).find("input[name='c_email[]']").val().trim();
        let designation = $(this).find("input[name='c_designation[]']").val().trim();
        let dept = $(this).find("select[name='c_department[]']").val();
        let escalation = $(this).find("select[name='c_escalation[]']").val();

        // If user filled anything → all required
        if (name || phone || email || designation || dept || escalation) {
            if (!(name && phone && email && designation && dept && escalation)) {
                isValid = false;
                $(this).addClass("error-border");
            } else {
                $(this).removeClass("error-border");
            }
        }
    });

    if (!isValid) {
        alert("Please fill all Escalation fields if you add escalation row.");
        e.preventDefault();
    }
});


</script>
