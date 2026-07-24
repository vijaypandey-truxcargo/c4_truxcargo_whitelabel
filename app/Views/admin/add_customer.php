<style>
   .disabled{ background:#f1f1f1; }
    .contact_row .form-control,
.contact_row select {
    height: 38px;
}
.btn-danger {
    padding: 6px 0;
}
.error-border {
    border: 2px solid red;
    padding: 8px;
    border-radius: 4px;
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
         <h3 class="pull-left">Add Customer</h3>
         <?= anchor('admin/customer/all','View All Customer',['class'=>'btn btn-success pull-right']);?>
         <div class="clearfix"></div>
         <div class="col-lg-12">
            <?php
               $error = session()->getFlashdata('error');
               $error_class = session()->getFlashdata('error_class');
               if($error): ?>
            <div class="alert alert-dismissible <?= $error_class;?>">
               <strong><?= $error;?></strong>
            </div>
            <?php endif;?>
         </div>
         <div class="clearfix"></div>
         <div class="well1 white">
            <?= form_open_multipart("admin/customer/insert",['class'=>'form-floating']) ?>
            <fieldset>
               <!-- ================= PRIMARY INFO ======================= -->
               <h3>Primary Info</h3>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label class="required">Username</label>
                     <?= form_input(['name'=>'username','class'=>'form-control1','required'=>'required']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label class="required">First Name</label>
                     <?= form_input(['name'=>'first','class'=>'form-control1','required'=>'required']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label class="required">Last Name</label>
                     <?= form_input(['name'=>'last','class'=>'form-control1','required'=>'required']);?>
                  </div>
               </div>
               <!--<div class="col-lg-4">-->
               <!--   <div class="form-group">-->
               <!--      <label class="required">Customer Code</label>-->
               <!--      <?= form_input(['name'=>'code','class'=>'form-control1','required'=>'required']);?>-->
               <!--   </div>-->
               <!--</div>   -->
               <div class="col-lg-4">
                  <div class="form-group">
                     <label class="required">Phone</label>
                     <?= form_input(['name'=>'phone','class'=>'form-control1','required'=>'required']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label class="required">Email Id</label>
                     <?= form_input(['name'=>'email','class'=>'form-control1','required'=>'required']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label class="required">Company Name</label>
                      <select class="form-control1" name="company" id="company_id" required>
                        <option value="">SELECT Anyone</option>
                        <?php
                           foreach($company as $company){ ?>
                              <option value="<?php echo $company->id ?>"><?php echo $company->name ?></option>
                           <?php }
                        ?>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label class="required">Password</label>
                     <?= form_input(['name'=>'password','class'=>'form-control1','required'=>'required']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Aadhar No</label>
                     <?= form_input(['name'=>'aadhar','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>PAN Card</label>
                     <?= form_input(['name'=>'pan','class'=>'form-control1','oninput'=>"this.value=this.value.toUpperCase()"]);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>KYC</label>
                     <select name="kyc" class="form-control">
                        <option value="1">Verified KYC</option>
                        <option value="0">Incomplete KYC</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>GST Percentage</label>
                     <?= form_input(['name'=>'gstpercentage','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>GST Apply</label>
                     <select name="gstpre" class="form-control">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>User Type</label>
                     <select name="type" class="form-control">
                        <option value="">None</option>
                        <option value="Customer">Customer</option>
                        <option value="Transporter">Transporter</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label class="required">Hub</label>
                     <select name="hub" class="form-control" required>
                        <option value="">Select Anyone</option>
                        <?php foreach ($hub as $hub_list): ?>
                        <option value="<?= $hub_list->id ?>"><?= $hub_list->name ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
                <div class="col-lg-4">
                  <div class="form-group">
                     <label>Divisor</label>
                      <?= form_input(['name'=>'divisor','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="clearfix"></div>
               <hr>
               <!-- ================= BILLING INFO ======================= -->
               <h3>Billing Info</h3>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>GST NO</label>
                     <?= form_input(['name'=>'gst','class'=>'form-control1','oninput'=>"this.value=this.value.toUpperCase()"]);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Address</label>
                     <?= form_input(['name'=>'address','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>City</label>
                     <?= form_input(['name'=>'city','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>State</label>
                     <select name="state" class="form-control">
                        <option value="">Select Any State</option>
                        <?php foreach ($state as $s): ?>
                        <option value="<?= $s->state ?>"><?= $s->state ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Pincode</label>
                     <?= form_input(['name'=>'pincode','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Bank Account Number</label>
                     <?= form_input(['name'=>'bank_account_number','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Bank IFSC Code</label>
                     <?= form_input(['name'=>'bank_ifsc_code','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>TDS Percentage</label>
                     <?= form_input(['name'=>'tds_percentage','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Sales Man</label>
                     <select name="sales_man" class="form-control">
                        <option value="">Select All</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= $u->id ?>"><?= $u->userName ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>CS Person</label>
                        <select name="cs_person" class="form-control">
                        <option value="">Select All</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= $u->id ?>"><?= $u->userName ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Billing Person</label>
                      <select name="billing_person" class="form-control">
                        <option value="">Select All</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= $u->id ?>"><?= $u->userName ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Child code</label>
                     <?= form_input(['name'=>'child_code','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Contrext ID</label>
                     <?= form_input(['name'=>'context_id','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="clearfix"></div>
               <hr>
               <!-- ================= PREFERENCE ======================= -->
               <h3>Preference</h3>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>B2B Wallet</label>
                     <select name="wallet" class="form-control">
                        <option value="Prepaid">Prepaid</option>
                        <option value="Postpaid">Postpaid</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>B2C Wallet</label>
                     <select name="b2c_wallet" class="form-control">
                        <option value="Prepaid">Prepaid</option>
                        <option value="Postpaid">Postpaid</option>
                     </select>
                  </div>
               </div>
               
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Credit limit</label>
                     <?= form_input(['name'=>'capping','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Aging Limit</label>
                     <?= form_input(['name'=>'aging_limit','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Payment type</label>
                     <select name="payment_type" class="form-control">
                        <option value="">Select All</option>
                        <option value="Cash">Cash</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Online">Online</option>
                        <option value="Credit">Credit</option>
                        <option value="FOC">FOC</option>
                        <option value="COD">COD</option>
                        <option value="To Pay">To Pay</option>
                        <option value="Card">Card</option>
                        <option value="Prepaid">Prepaid</option>
                        <option value="CASH ON DELIVERY">CASH ON DELIVERY</option>
                        <option value="CHEQUE ON DELIVERY">CHEQUE ON DELIVERY</option>
                        <option value="To Pay + COD">To Pay + COD</option>
                        <option value="Total Prepaid">Total Prepaid</option>
                        <option value="PENDING">PENDING</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Invoice Cycle</label>
                     <select name="invoice_cycle" class="form-control">
                        <option value="">Select All</option>
                        <option value="WEEKLY">WEEKLY</option>
                        <option value="FORTNIGHTLY">FORTNIGHTLY</option>
                        <option value="MONTHLY">MONTHLY</option>
                        <option value="Daily">DAILY</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Invoice Head</label>
                     <select name="invoice_head" class="form-control">
                        <option value="">Select All</option>
                        <option value="TAX INVOICE">TAX INVOICE</option>
                        <option value="TAX INVOICE - FWD NO">TAX INVOICE - FWD NO</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Capping Activation Date</label>
                     <input name="capping_date" type="text" class="form-control">
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Capping Expire Date</label>
                     <input name="capping_expire" type="text" class="form-control">
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Commission Percentage</label>
                     <?= form_input(['name'=>'commission','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Remittance</label>
                     <select name="preference" class="form-control">
                        <option value="Wallet">Wallet</option>
                        <option value="Bank">Bank</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Status</label>
                     <select name="status" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>B2B Panel</label>
                     <select name="panel" class="form-control">
                        <?php foreach ($key as $k): ?>
                        <option value="<?= $k->title ?>"><?= $k->title ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>B2B Panel Count</label>
                     <select name="panel_count" class="form-control">
                        <option value="1">Single Panel</option>
                        <option value="2">B2B & Cargo</option>
                        <option value="4">B2B & Dense</option>
                        <option value="5">Dense & Cargo</option>
                        <option value="3">All Panels</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>B2B Matrix</label>
                     <select name="matrix" class="form-control">
                        <option value="5">5 Matrix</option>
                        <option value="16">16 Matrix</option>
                        <option value="24">24 Matrix</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Plan Type</label>
                     <select name="plan" class="form-control">
                        <option value="">None</option>
                        <option value="Startup">Startup</option>
                        <option value="Small Business">Small Business</option>
                        <option value="Enterprise">Enterprise</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Plan Expire Date</label>
                     <input name="expire" type="text" class="form-control">
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Bihar</label>
                     <select name="bihar" class="form-control">
                        <option value=""></option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Finance Embargo</label>
                     <select name="embargo" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                     </select>
                  </div>
               </div>
               <div class="clearfix"></div>
               <hr>
               <!-- ================= PANEL STATUS ======================= -->
               <h3>Panel Status</h3>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Rivigo Status</label>
                     <select name="fedex" class="form-control">
                        <option value=""></option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Delhivery Status</label>
                     <select name="delhivery" class="form-control">
                        <option value=""></option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>DTDC Status</label>
                     <select name="dtdc" class="form-control">
                        <option value=""></option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>B2C Status</label>
                     <select name="xpress" class="form-control">
                        <option value=""></option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                     </select>
                  </div>
               </div>
               <div class="clearfix"></div>
               <hr>
               <!-- ================= ATTACHMENT ======================= -->
               <h3>Attachment</h3>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>ID Proof <small>(Max Size:100KB)</small></label>
                     <input type="file" name="image" class="form-control">
                  </div>
               </div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <label>Agreement <small>(Max Size:200KB)</small></label>
                     <input type="file" name="agreement" class="form-control">
                  </div>
               </div>
               <div class="col-lg-12">
                  <div class="form-group">
                     <label>Remark</label>
                     <?= form_input(['name'=>'remark','class'=>'form-control1']);?>
                  </div>
               </div>
               <div class="clearfix"></div>
               <hr>
               <!-- ================= Escalation Level ======================= -->
              <h3>Escalation</h3>
               <div id="contact_section">
                  <div id="contact_rows">

                     <!-- First Row -->
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
                                 <option value="">Department Name</option>
                                 <option value="BILLING">BILLING</option>
                                 <option value="CUSTOMER SERVICE">CUSTOMER SERVICE</option>
                                 <option value="OPERATIONS">OPERATIONS</option>
                              </select>
                           </div>
                           <div class="col-md-1">
                              <select name="c_escalation[]" class="form-control">
                                 <option value="">Esclation</option>
                                 <option value="Level 1">Level 1</option>
                                 <option value="Level 2">Level 2</option>
                                 <option value="Level 3">Level 3</option>
                                 <option value="Level 4">Level 4</option>
                              </select>
                           </div>

                           <!-- Add Button in Row -->
                           <div class="col-md-1">
                              <button id="addRow" type="button" class="btn btn-primary w-100">+</button>
                           </div>
                     </div>

                  </div>
               </div>
               <div class="clearfix"></div>
               <div class="col-lg-4">
                  <div class="form-group">
                     <?php if(in_array("Add Customer", $GLOBALS['permission'])): ?>
                     <input type="submit" name="submit" value="Add Customer" class="btn btn-primary">
                     <?php endif; ?>
                  </div>
               </div>
            </fieldset>
            <?= form_close(); ?>
         </div>
      </div>
   </div>
</div>
<script>
$(document).ready(function () {
    var i = 1;

    $("#addRow").click(function () {
        i++;

        $("#contact_rows").append(`
            <div class="row contact_row" id="row${i}" style="margin-bottom:10px;">
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
                        <option value="">Department Name</option>
                        <option value="BILLING">BILLING</option>
                        <option value="CUSTOMER SERVICE">CUSTOMER SERVICE</option>
                        <option value="OPERATIONS">OPERATIONS</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="c_escalation[]" class="form-control">
                        <option value="">Esclation</option>
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

        // If any field has value but others are empty → block submit
        if (name || phone || email || designation || dept || escalation) {
            if (!(name && phone && email && designation && dept && escalation)) {
                isValid = false;
                $(this).addClass("error-border"); // highlight row
            } else {
                $(this).removeClass("error-border");
            }
        }
    });

    if (!isValid) {
        alert("Please fill all Escalation fields if adding escalation details.");
        e.preventDefault();
    }
});

});
</script>
