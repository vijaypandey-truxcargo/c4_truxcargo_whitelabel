<script>
   function validate(form) {
       return confirm('Do you really want to delete this record ?');
   }
</script>
<div id="page-wrapper">
   <div class="col-md-12 graphs">
      <div class="xs">
         <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0;">Customer Service Charge List</h3>
            <div style="display:flex; align-items:center; gap:10px;">
               <?php if (in_array("Add Customer Service", $GLOBALS['permission'])): ?>
                  <?= anchor('admin/CustomerServiceCharge/add','New Customer Service Charge',['class'=>'btn btn-danger']); ?>
               <?php endif; ?>
               <?php if (in_array("Import Customer Service", $GLOBALS['permission'])): ?>
               <button id="exportSampleBtn" class="btn btn-success">Download Sample</button>
               <?= form_open_multipart('admin/CustomerServiceCharge/import',['style'=>'display:flex; align-items:center; gap:10px; margin:0;']); ?>
               <input type="file" name="file" class="form-control" style="max-width:200px;">
               <input type="submit" name="importSubmit" class="btn btn-success" value="IMPORT">
                 <?php endif; ?>
                  <?php if (in_array("Export Customer Service", $GLOBALS['permission'])): ?>
               <button id="exportBtn" type="button" class="btn btn-success">Export All</button>
                <?php endif; ?>
               <?= form_close(); ?>
            </div>
         </div>
         <div class="col-lg-12">
            <?php 
               $error=session()->getFlashdata('error');
               $error_class=session()->getFlashdata('error_class');
               if($error):?>
            <div class="alert alert-dismissible <?= $error_class;?>">
               <strong><?= $error;?></strong>
            </div>
            <?php endif;?>
         </div>
         <?= form_open('admin/CustomerServiceCharge/index'); ?>
         <div class="row" style="margin-bottom:15px;">
            <div class="col-md-3">
               <label>Customer</label>
               <select name="customer" class="form-control">
                  <option value="All">All</option>
                  <?php foreach ($customers as $c): ?>
                  <option value="<?= $c->id ?>" <?= ($selected_customer == $c->id) ? 'selected' : '' ?>>
                     <?= !empty($c->code) ? $c->code.' - '.$c->first.' '.$c->last : $c->first.' '.$c->last ?>
                  </option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div class="col-md-3">
               <label>Service</label>
               <select name="service" class="form-control">
                  <option value="All">All</option>
                  <?php foreach ($services as $s): ?>
                  <option value="<?= $s->id ?>" <?= ($selected_service==$s->id)?'selected':'' ?>>
                     <?= $s->name ?>
                  </option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div class="col-md-3">
               <label>Vendor</label>
               <select name="vendor" class="form-control">
                  <option value="All">All</option>
                  <?php foreach ($vendors as $v): ?>
                  <option value="<?= $v->id ?>" <?= ($selected_vendor==$v->id)?'selected':'' ?>>
                     <?= $v->name ?>
                  </option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div class="col-md-2">
               <label>Status</label>
               <select name="status" class="form-control">
                  <option value="All">All</option>
                  <option value="1" <?= ($selected_status=="1")?'selected':'' ?>>Active</option>
                  <option value="0" <?= ($selected_status=="0")?'selected':'' ?>>Inactive</option>
               </select>
            </div>
            <div class="col-md-1" style="margin-top:23px;">
               <input type="submit" name="apply" value="Apply" class="btn btn-primary" style="width:100%;">
            </div>
         </div>
         <?= form_close(); ?>
         <div class="bs-example4">
            <table class="table table-responsive table-bordered" id="example">
               <thead>
                  <tr>
                     <th>Sr.No.</th>
                     <th>Customer</th>
                     <th>Service</th>
                     <th>Vendor</th>
                     <th>Min Charge</th>
                     <th>Divisor</th>
                     <th>Min Weight</th>
                     <th>Fuel Percentage</th>
                     <th>Status</th>
                     <th>Created At</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php if(empty($code)): ?>
                  <tr>
                     <td colspan="11" class="text-center text-danger"><b>No Data Found</b></td>
                  </tr>
                  <?php else: ?>
                  <?php if(empty($count)) $count=0; ?>
                  <?php foreach($code as $row): $count++; ?>
                  <tr>
                     <?php
                        $names = $row->customer_names ?? [['no' => 1, 'name' => '<b>Default Customer</b>']];
                        $total = count($names);
                        $showNames = array_slice(array_column($names, 'name'), 0, 4);
                     ?>
                     <td><?= $count ?></td>
                     <td>
                        <?= implode(', ',$showNames); ?>
                        <?php if($total>4): ?>
                        <button type="button"
                           class="btn btn-xs btn-info showCustomerBtn"
                           data-customers='<?= json_encode($names); ?>'>
                        Show More
                        </button>
                        <?php endif; ?>
                     </td>
                     <td><?= $row->service_name ?></td>
                     <td><?= $row->vendor_name ?></td>
                     <td><?= number_format($row->min_charge,2) ?></td>
                     <td><?= $row->divisor ?></td>
                     <td><?= $row->min_weight ?></td>
                     <td><?= $row->fuel_percent ?></td>
                     <td>
                        <span class="<?= $row->status==1?'text-success':'text-danger' ?>">
                        <?= $row->status==1?'Active':'Inactive' ?>
                        </span>
                     </td>
                     <td><?= $row->created_at ?></td>
                     <td>
                        <?php if (in_array("Edit Customer Service", $GLOBALS['permission'])): ?>
                           <?= anchor("admin/CustomerServiceCharge/edit/".$row->id,'Edit',['class'=>'btn btn-primary btn-sm']); ?>
                        <?php endif; ?>
                        <?php if (in_array("Delete Customer Service", $GLOBALS['permission'])): ?>
                        <?= form_open('admin/CustomerServiceCharge/delete',['onsubmit'=>'return validate(this);','style'=>'display:inline']); ?>
                        
                        <input type="hidden" name="id" value="<?= $row->id ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        <?= form_close(); ?>
                        <?php endif; ?>
                     </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php endif; ?>
               </tbody>
            </table>
         </div>
         <p class="pagination"><?= $links; ?></p>
      </div>
   </div>
</div>
<div id="customerModal" class="modal fade" tabindex="-1">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Customer List</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body" style="max-height:300px; overflow-y:auto;">
            <div id="customerModalContent"></div>
         </div>
      </div>
   </div>
</div>
<script>
   document.getElementById('exportSampleBtn').onclick=function(){
   window.location.href="<?= base_url('admin/CustomerServiceCharge/export_sample'); ?>";
   };
   
   document.getElementById('exportBtn').onclick=function(){
   window.location.href="<?= base_url('admin/CustomerServiceCharge/export_all'); ?>";
   };
   
   document.addEventListener("DOMContentLoaded",function(){
   
   document.querySelectorAll(".showCustomerBtn").forEach(function(button){
   
   button.addEventListener("click",function(){
   
   let customers=this.getAttribute("data-customers");
   let data=JSON.parse(customers);
   
   let html=`<table class="table table-bordered">
   <thead>
   <tr>
   <th>Sr.No.</th>
   <th>Customer Name</th>
   </tr>
   </thead>
   <tbody>`;
   
   data.forEach(function(item,index){
   
   html+=`
   <tr>
   <td>${index+1}</td>
   <td>${item.name}</td>
   </tr>`;
   
   });
   
   html+=`</tbody></table>`;
   
   document.getElementById("customerModalContent").innerHTML=html;
   
   $("#customerModal").modal("show");
   
   });
   
   });
   
   });
   
</script>
