<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
            <h3 class="pull-left">All Customer Wallet</h3>
	    <div class="clearfix"></div>
            <div class="bs-example4" data-example-id="contextual-table">
                <?= form_open('admin/customer/wallet'); ?>
                <div class="col-lg-3">
                   <p class="input-group">
                      <select class="form-control" name="login_id">
                          <option value="All" <?php if(session()->get('login_id')=='All'){echo 'selected="selected"';}?>>All Members</option>
                         <?php foreach ($key as $key){ ?>
                          <option value="<?php echo $key->id;?>" <?php if(set_value('login_id')==$key->id){echo 'selected="selected"';}?>><?php echo $key->first.' '.$key->last.' ('.$key->username.')';?> </option>
                         <?php }?>
                      </select>                      
                   </p>
                </div> 
                 
                <div class="col-lg-3" id="apply">
                  <p class="input-group">
                      <input type="submit" name="apply" value="Apply" class="btn btn-primary">        
                  </p>                  
                </div> 
                <?= form_close();?>
                <button class="btn btn-default pull-right" style="margin-bottom: 12px" onclick="customer('registration')"><i class="fa fa-download"></i> All Data</button>
             
              <table class="table table-responsive" style="margin-top: 40px" id="example">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th> Username</th> 
                          <th> Name</th>
                          <th> Company Name</th>
                          <th>Recharge</th>
                          <th>Deduction</th>  
                          <th>New Negative</th>  
                          <th> Refund</th>
                          <th>Balance</th>
                          <th>Action</th>
                       </tr>
                  </thead>
                  <tbody>
          <?php 
               if(empty($count)){ $count =0;} else {$count=$count;}   foreach ($code as $key):   $count++;?>
                      <tr>
                          <td><?= $count; ?></td>
                          <td><b><?= $key->username; ?></b> <br>(<?= $key->panel; ?>)</td>
                          <td><?= $key->first; ?> <?= $key->last; ?></td>
                          <td><?= $key->company; ?>   </td> 
                          <td><?= number_format($recharge($key->id),2);?></td> 
                          <td><?= number_format($deduction($key->id),2);?></td> 
                          <td><?= number_format($negative($key->id),2);?></td> 
                          <td><?= number_format($refund($key->id),2);?></td> 
                          <td><?= number_format($total($key->id),2);?></td> 
                          <td width="15%">
                              <?php  echo form_open('admin/wallet/'),
                                 form_hidden('login_id',$key->id),
                                 form_submit(['name'=>'apply','value'=>'More Info','class'=>'btn btn-danger']),
                                 form_close(); ?>
                          </td>
                      </tr>
                          <?php endforeach;?>
                  </tbody>
              </table>
              <p class="pagination"><?php echo $links; ?></p>
          </div>
        </div>
        <div id="report_order" style="display: none"></div>
  <script>
 function dateSearch(){
     $('#apply').css('display', 'block');
     var opt = $("#date option:selected").val();
      if(opt==='Custom Range'){$('#show').css('display', 'block'); $('#show1').css('display', 'block');}
      else {$('#show').css('display', 'none'); $('#show1').css('display', 'none');}
 }
 </script>
 <script>
    function customer(table){
      $.ajax({  
        url:"<?= base_url('admin/customer/report_wallet') ?>",
        method:"POST",  
        data:{[csrfName]: csrfHash,table:table},      
        success:function(data){  
            $('#report_order').html(data);                          
        }  
      });   
   }
</script> 
