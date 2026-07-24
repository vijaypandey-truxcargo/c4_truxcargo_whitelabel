<script>
function validate(form) {
        return confirm('Do you really want to delete this record ?');
}
</script>
<style>
.ml-1 { margin-left: 5px; }
.customer-table-shell {
    clear: both;
    max-width: 100%;
    overflow: hidden;
    position: relative;
    z-index: 1;
}
.customer-table-scroll {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 5px;
}
.customer-table-scroll table {
    margin-bottom: 0;
    min-width: max-content;
    width: 100%;
}
.customer-table-scroll th,
.customer-table-scroll td {
    padding: 9px 12px !important;
    vertical-align: top !important;
    white-space: nowrap;
}
.customer-table-scroll table.dataTable thead > tr > th.sorting,
.customer-table-scroll table.dataTable thead > tr > th.sorting_asc,
.customer-table-scroll table.dataTable thead > tr > th.sorting_desc {
    padding-right: 34px !important;
}
.customer-table-scroll table.dataTable thead > tr > th.sorting::before,
.customer-table-scroll table.dataTable thead > tr > th.sorting_asc::before,
.customer-table-scroll table.dataTable thead > tr > th.sorting_desc::before,
.customer-table-scroll table.dataTable thead > tr > th.sorting::after,
.customer-table-scroll table.dataTable thead > tr > th.sorting_asc::after,
.customer-table-scroll table.dataTable thead > tr > th.sorting_desc::after {
    right: 12px !important;
}
.customer-dt-toolbar {
    align-items: center;
    clear: both;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: space-between;
    margin: 20px 0 10px;
}
</style>
<div id="page-wrapper">
    <div class="col-md-12 graphs">
      <div class="xs">
        <h3 class="pull-left">All Customer</h3>
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
         <div class="bs-example4" data-example-id="contextual-table">
            <?= form_open('admin/customer/all'); ?>
              <div class="col-lg-4">
                  <p class="input-group">
                      <input type="text" class="form-control" name="search" value="<?= set_value('search');?>" placeholder='Search By Name/Phone/Username/Customer Code'>
                   </p>
                </div>
                <div class="col-lg-2">
                 <p class="input-group">
                      <select class="form-control" name="type" >
                          <option value="All" <?php if(session()->get('type')=='All'){echo 'selected="selected"';}?>>All User Type</option>
                          <option value="Customer" <?php if(session()->get('type')=='Customer'){echo 'selected="selected"';}?>>Customer</option>
                          <option value="Transporter" <?php if(session()->get('type')=='Transporter'){echo 'selected="selected"';}?>>Transporter</option>
                        </select>
                  </p>
                </div>
                <div class="col-lg-2">
                 <p class="input-group">
                      <select class="form-control" name="wallet" >
                          <option value="All" <?php if(session()->get('wallet')=='All'){echo 'selected="selected"';}?>>All  Wallet Type</option>
                          <option value="Postpaid" <?php if(session()->get('wallet')=='Postpaid'){echo 'selected="selected"';}?>>Postpaid</option>
                          <option value="Prepaid" <?php if(session()->get('wallet')=='Prepaid'){echo 'selected="selected"';}?>>Prepaid</option>
                          <option value="Capping" <?php if(session()->get('wallet')=='Capping'){echo 'selected="selected"';}?>>Capping</option>
                       </select>
                  </p>
                </div>

             <div class="col-lg-4">
                 <p class="input-group">
                      <select class="form-control" name="gstpre" >
                          <option value="All" <?php if(session()->get('gstpre')=='All'){echo 'selected="selected"';}?>>All  GST Preference</option>
                          <option value="Yes" <?php if(session()->get('gstpre')=='Yes'){echo 'selected="selected"';}?>>Yes</option>
                          <option value="No" <?php if(session()->get('gstpre')=='No'){echo 'selected="selected"';}?>>No</option>
                        </select>
                  </p>
             </div>
             <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control" name="cod_plan" >
                          <option value="All" <?php if(session()->get('cod_plan')=='All'){echo 'selected="selected"';}?>>All COD Plan</option>
                          <option value="Weekly" <?php if(session()->get('cod_plan')=='Weekly'){echo 'selected="selected"';}?>>Weekly</option>
                          <?php foreach ($plan as $plan){?>
                          <option value="<?= $plan->plan?>" <?php if(session()->get('cod_plan')==$plan->plan){echo 'selected="selected"';}?>><?= $plan->plan;?></option>
                          <?php }?>
                    </select>
                </div>
              </div>
              <div class="col-lg-3">
                 <div class="form-group">
                     <select name="panel_count" class="form-control">
                          <option value="All" <?php if(session()->get('panel_count')=='All'){echo 'selected="selected"';}?>>All Panel Count</option>
                         <option value="1" <?php if(session()->get('panel_count')==1){ echo 'selected="selected"';}?>>Single Panel</option>
                         <option value="2" <?php if(session()->get('panel_count')==2){ echo 'selected="selected"';}?>>B2B & Cargo</option>
                         <option value="4" <?php if(session()->get('panel_count')==4){ echo 'selected="selected"';}?>>B2B & Dense</option>
                         <option value="5" <?php if(session()->get('panel_count')==5){ echo 'selected="selected"';}?>>Dense & Cargo</option>
                         <option value="3" <?php if(session()->get('panel_count')==3){ echo 'selected="selected"';}?>>3 Panels</option>
                      </select>
                 </div>
               </div>
               <?php if (! empty($hasBtype)): ?>
               <div class="col-lg-3">
                 <div class="form-group">
                     <select name="btype" class="form-control">
                          <option value="All" <?php if(session()->get('btype')=='All'){echo 'selected="selected"';}?>>All Business Type</option>
                         <option value="Retailer" <?php if(session()->get('btype')=='Retailer'){ echo 'selected="selected"';}?>>Retailer</option>
                         <option value="Ecommerce" <?php if(session()->get('btype')=='Ecommerce'){ echo 'selected="selected"';}?>>Ecommerce</option>
                         <option value="Franchise" <?php if(session()->get('btype')=='Franchise'){ echo 'selected="selected"';}?>>Franchise</option>
                         <option value="Co-loader" <?php if(session()->get('btype')=='Co-loader'){ echo 'selected="selected"';}?>>Co-loader </option>
                         <option value="Brand" <?php if(session()->get('btype')=='Brand'){ echo 'selected="selected"';}?>>Brand</option>
                         <option value="Enterprise" <?php if(session()->get('btype')=='Enterprise'){ echo 'selected="selected"';}?>>Enterprise</option>
                      </select>
                 </div>
               </div>
               <?php endif; ?>
               <?php if (! empty($hasMorder)): ?>
               <div class="col-lg-3">
                 <div class="form-group">
                     <select name="morder" class="form-control">
                          <option value="All" <?php if(session()->get('morder')=='All'){echo 'selected="selected"';}?>>All Monthly Orders</option>
                         <option value="0–50" <?php if(session()->get('morder')=='0–50'){ echo 'selected="selected"';}?>>0–50</option>
                         <option value="50-100" <?php if(session()->get('morder')=='50-100'){ echo 'selected="selected"';}?>>50-100</option>
                         <option value="100-500" <?php if(session()->get('morder')=='100-500'){ echo 'selected="selected"';}?>>100-500</option>
                         <option value="Above 500" <?php if(session()->get('morder')=='Above 500'){ echo 'selected="selected"';}?>>Above 500</option>
                      </select>
                 </div>
               </div>
               <?php endif; ?>
             <div class="col-lg-3">
              <p class="input-group ">
                       <select class="form-control" name="date" id="date" onchange="dateSearch()">
                          <option value="All" <?php if(session()->get('date')=='All'){echo 'selected="selected"';}?>>All Joining Date</option>
                          <option value="Today" <?php if(session()->get('date')=='Today'){echo 'selected="selected"';}?>>Today</option>
                          <option value="Yesterday" <?php if(session()->get('date')=='Yesterday'){echo 'selected="selected"';}?>>Yesterday</option>
                          <option value="7 Days" <?php if(session()->get('date')=='7 Days'){echo 'selected="selected"';}?>>Last 7 Days</option>
                          <option value="30 Days" <?php if(session()->get('date')=='30 Days'){echo 'selected="selected"';}?>>Last 30 Days</option>
                          <option value="This Month" <?php if(session()->get('date')=='This Month'){echo 'selected="selected"';}?>>This Month</option>
                          <option value="Last Month" <?php if(session()->get('date')=='Last Month'){echo 'selected="selected"';}?>>Last Month</option>
                          <option value="Custom Range" <?php if(session()->get('date')=='Custom Range'){echo 'selected="selected"';}?>>Custom Range</option>
                      </select>
                  </p>
             </div>
            <div class="col-lg-4">
                <p class="input-group">
                    <select class="form-control" name="hub">
                        <option value="All">All Hubs</option>
                        <?php foreach($hubList as $h){ ?>
                         <?php
                            $selected = "";
                            if (session()->get('hub') == $h->id) {
                                $selected = "selected";
                            }
                        ?>
                          <option value="<?= $h->id ?>" <?= $selected ?>>
                              <?= $h->name ?>
                          </option>

                      <?php } ?>
                    </select>
                </p>
             </div>

            <div class="col-lg-4">
                <p class="input-group">
                    <select class="form-control" name="status">
                        <option value="All">Status</option>
                        <option value="1" <?= (session()->get('status') == '1') ? 'selected' : '' ?> >Active</option>
                        <option value="0" <?= (session()->get('status') == '0') ? 'selected' : '' ?> >Inactive</option>
                    </select>
                </p>
            </div>

             <div class="col-lg-3" id="show" style="display:<?php if(session()->get('date1')){echo 'block';} else {echo 'none';}?>">
                  <p class="input-group">
                      <input type="date" name="date1" class="form-control" value="<?= session()->get('date1');?>">
                  </p>
              </div>
             <div class="col-lg-3" id="show1" style="display:<?php if(session()->get('date2')){echo 'block';} else {echo 'none';}?>">
                  <p class="input-group">
                      <input type="date" name="date2" class="form-control" value="<?= session()->get('date2');?>">
                  </p>
             </div>
             <div class="col-lg-3">
                <p class="input-group">
                    <input type="submit" name="apply" value="Apply" class="btn btn-primary">
                </p>
             </div>
               <?= form_close();?>

                <?=form_open('admin/customer/report',['target'=>'_blank']); ?>
                <input type="hidden" name="con" value="<?= $condition?>">
                <button type="submit" class="btn btn-default pull-right" style="margin-bottom: 12px" ><i class="fa fa-download"></i> All Data</button>
                <?= form_close();?>
              <div class="clearfix"></div>

              <div class="customer-dt-toolbar">
                <div></div>
                <div id="customer_export_buttons"></div>
              </div>

              <div class="customer-table-shell">
                <div class="customer-table-scroll">
              <table class="table" id="customer_table">
                  <thead>
                      <tr>
                          <th> ID</th>
                          <th> Date</th>
                           <th> Username</th>
                           <th> Customer Code</th>
                          <th> Name</th>
                          <th> Type</th>
                          <th> Contact Info</th>
                           <th> Company Name</th>
                           <th>GST & Preference</th>
                           <th>COD Plan</th>
                           <th>POC</th>
                          <th> Status</th>
                          <th> Action</th>
                       </tr>
                  </thead>
                  <tbody>
          <?php
             $companyMap = [];
             foreach ($company as $c) {
                 $companyMap[$c->id] = $c->name;
             }
             foreach ($code as $key): $count++; ?>
                      <tr class="active">
                          <td><?= $count;?></td>
                          <td><?= date('d M Y', strtotime($key->date)); ?></td>
                          <td><b><?= $key->username; ?></b> <br>(<?= $key->panel; ?>)</td>
                          <td><?= $key->code; ?></td>
                          <td><?= $key->first; ?> <?= $key->last; ?></td>
                          <td><?= $key->wallet; ?> <br>(<?= $key->type; ?>)</td>
                          <td> <?= $key->phone?> </td>
                          <td><?= $companyMap[$key->company] ?? ''; ?></td>
                           <td><?php if(!empty($key->gst)){echo $key->gst;} else {echo 'NA';} ?><br><b>(<?= $key->gstpre; ?>)</b> </td>
                          <td><?= $key->cod_plan; ?>  </td>
                          <td><?= $poc($key->id); ?>  </td>
                          <td><?php if($key->status==1){echo '<span class="gactive">Active</span>';} else {echo '<span class="inactive">Inactive</span>';}?></td>

                          <td width="15%">
                              <?php echo anchor("admin/customer/edit_customer/{$key->id}",'<i class="fa fa-pencil"></i>',['class'=>'btn btn-primary pull-left','title'=>'Edit Profile']);
                                    echo anchor("admin/billing/ledger/{$key->id}",'<i class="fa fa-bookmark"></i>',['class'=>'btn btn-success pull-left  ml-1','title'=>'Ledger','target'=>'_blank']);
                              if(in_array("Add Customer", $GLOBALS['permission'])){
                          /* echo form_open('admin/customer/delete',['onsubmit'=>'return validate(this);']),
                                 form_hidden('id',$key->id),
                                 form_submit(['name'=>'submit','value'=>'X','class'=>'btn btn-danger pull-left ml-1','title'=>'Delete']),
                          form_close();*/
                                  }?>
                           <?php if(in_array("Add Order", $GLOBALS['permission'])){
                        //   echo form_open('admin/order/add_order'),
                        //         form_hidden('id',$key->id),
                        //         form_submit(['name'=>'submit','value'=>'+','class'=>'btn btn-warning pull-left ml-1','title'=>'Add Order']),
                        //         form_close();
                                }?>
                             </td>
                      </tr>
                          <?php endforeach;?>
                  </tbody>
              </table>
                </div>
              </div>
              <p class="pagination"><?php echo $links; ?></p>
          </div>
        </div>
          <div id="report_order" style="display: none"></div>
  <script>
 $(document).ready(function() {
     if ($.fn.DataTable && ! $.fn.DataTable.isDataTable('#customer_table')) {
         var customerTable = $('#customer_table').DataTable({
             paging: false,
             info: false,
             ordering: true,
             order: [],
             pageLength: 10,
             dom: 't',
             language: {
                 emptyTable: 'No records found.'
             },
             buttons: [
                 { extend: 'excelHtml5', footer: true },
                 { extend: 'csvHtml5', footer: true },
                 { extend: 'pdfHtml5', footer: true }
             ]
         });

         customerTable.buttons().container().appendTo('#customer_export_buttons');
     }
 });

 function dateSearch(){
     var opt = $("#date option:selected").val();
      if(opt==='Custom Range'){$('#show').css('display', 'block'); $('#show1').css('display', 'block');}
      else {$('#show').css('display', 'none'); $('#show1').css('display', 'none');}
 }
 </script>
