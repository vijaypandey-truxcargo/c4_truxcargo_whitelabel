<style>.Active{background: green} .Expire{background: red}
.total {
    border-radius: 32px;
    background: linear-gradient(46deg, #FF8E00 50%, #f0d957 100%);
    box-shadow: 0px 4px 4px 0px rgba(255, 255, 255, 0.20);
    padding: 16px 30px;
    color:#fff;
    text-align:center;
}
.start{background: linear-gradient(48deg, #31708f -24.9%, #36CB72 97.72%);}
.sme{background: linear-gradient(47deg, #f63c38 1.69%, #EF7B5A 100%);}
.ent{background: linear-gradient(48deg, #0d0acc -24.9%, #5ccbf6 97.72%);}
</style>
<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
            <h3 class="pull-left"><?= $page_title;?></h3>
            <?= form_open('admin/customer/subscription_report',['target'=>'_blank'])?>
                <input type="hidden" name="condition" value="<?= $condition;?>">
                <button class="btn btn-primary pull-right ml-1" style="margin-bottom: 12px" type="submit"><i class="fa fa-download"></i> All Data</button>
            <?= form_close();?>
            

	  <div class="clearfix"></div>
	  <div class="row">
	    <div class="col-xs-12 col-sm-12">
	        <div class="card">
	            <div class="card-header"><strong>Overview</strong>  </div>
                <div class="card-body card-block">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="total">
                              <p>Total Subscription :<b><?= $startup + $sme + $enterprise;?> </b></p>
                              <h3 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($total,2);?></b></h3>
                               <hr>
                              <p>Active: <?= $startup_active+$sme_active+$enterprise_active;?> </p>
                              <h4 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($sa1+$sa2+$sa3,2);?></b></h4>
                              <hr>
                              <p>Expire: <?= $startup + $sme + $enterprise- $startup_active-$sme_active-$enterprise_active;?> </p>
                              <h4 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($total -$sa1-$sa2-$sa3,2);?></b></h4>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="total start">
                              <p>Startup: <b><?= $startup;?></b> </p>
                              <h3 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($s1,2);?></b></h3>
                              <hr>
                              <p>Active: <?= $startup_active;?> </p>
                              <h4 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($sa1,2);?></b></h4>
                              <hr>
                              <p>Expire: <?= $startup-$startup_active;?> </p>
                              <h4 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($s1-$sa1,2);?></b></h4>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="total sme">
                              <p>SME : <b><?= $sme;?></b> </p><h3 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($s2,2);?></b></h3>
                              <hr>
                              <p>Active: <?= $sme_active;?> </p>
                              <h4 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($sa2,2);?></b></h4>
                              <hr>
                              <p>Expire: <?= $sme-$sme_active;?> </p>
                              <h4 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($s2-$sa2,2);?></b></h4>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="total ent">
                                <p>Enterprise : <b><?= $enterprise;?></b></p><h3 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($s3,2);?></b></h3>
                                <hr>
                              <p>Active: <?= $enterprise_active;?> </p>
                              <h4 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($sa3,2);?></b></h4>
                              <hr>
                              <p>Expire: <?= $enterprise-$enterprise_active;?> </p>
                              <h4 class="mb-0 mt-1"><b> <i class="fa fa-inr"></i> <?= number_format($s3-$sa3,2);?></b></h4>
                            </div>    
                        </div>
                        <div class="clearfix"></div>
                    </div>
            </div>
          </div>
        </div>
          <div class="bs-example4" data-example-id="contextual-table">
          <?= form_open('admin/customer/subscription');?>
              <div class="col-lg-3">
                   <p class="input-group">
                      <select class="form-control" name="login_id">
                          <option value="All" <?php if(session()->get('login_id')=='All'){echo 'selected="selected"';}?>>All Members</option>
                         <?php foreach ($key as $key){ ?>
                          <option value="<?php echo $key->id;?>" <?php if(session()->get('login_id')==$key->id){echo 'selected="selected"';}?>><?php echo $key->first.' '.$key->last.' ('.$key->username.')';?> </option>
                         <?php }?>
                      </select>   
                  </p>
                </div>               
                           
               <div class="col-lg-3">
                   <p class="input-group">
                      <select class="form-control" name="plan">
                          <option value="All" <?php if(session()->get('plan')=='All'){echo 'selected="selected"';}?>>All Plans </option>
                          <option value="Startup" <?php if(session()->get('plan')=='Startup'){echo 'selected="selected"';}?>>Startup</option>
                          <option value="Small Business" <?php if(session()->get('plan')=='Small Business'){echo 'selected="selected"';}?>>Small Business</option>
                          <option value="Enterprise" <?php if(session()->get('plan')=='Enterprise'){echo 'selected="selected"';}?>>Enterprise</option>
                       </select>                     
                  </p>
                </div> 
               <div class="col-lg-3">
                  <p class="input-group">
                      <select class="form-control" name="status">
                          <option value="All" <?php if(session()->get('status')=='All'){echo 'selected="selected"';}?>>All Status</option>
                          <option value="Active" <?php if(session()->get('status')=='Active'){echo 'selected="selected"';}?>>Active</option>
                          <option value="Expire" <?php if(session()->get('status')=='Expire'){echo 'selected="selected"';}?>>Expire</option>
                      </select>                 
                  </p>
                </div>   
                <div class="col-lg-3">
                  <p class="input-group">  
                      <select class="form-control" name="date" id="date" onchange="dateSearch()">
                          <option value="All" <?php if(session()->get('date')=='All'){echo 'selected="selected"';}?>>All</option>
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
                          
                  <div class="col-lg-3" id="apply">
                  <p class="input-group">
                      <input type="submit" name="apply" value="Apply" class="btn btn-primary">        
                  </p>                  
                  </div> 
                <?= form_close();?>
             
                     
                <table class="table table-responsive" style="margin-top: 40px; width: 100%; max-width: 100%"  id="example">
                  <thead>
                      <tr>
                          <th> SNO</th>
                          <th>USERID</th>    
                          <th>Plan </th>
                          <th>Plan Amt </th>
                          <th>Purchase Date</th>                          
                          <th>Expire Date</th>
                          <th>Coupon</th>
                          <th>POC</th>
                          <th>Status</th>  
                      </tr>
                  </thead>
                  <tbody>
               <?php  $i=$count;
              foreach ($code as $key){$i++; 
               $ts1 = strtotime($key->date);
            $ts2 = strtotime($key->edate); 
            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);
            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

             $months = (($year2 - $year1) * 12) + ($month2 - $month1);
              $m= $months;
              if($m==1){$duration= 'Monthly'; $day='monthly';}
              else if($m>=2 && $m<=4 ){$duration= 'Quarterly'; $day='quarterly';}
              else if($m==6){$duration= 'Semi-Yearly'; $day='half';} 
              else {$duration= 'Yearly'; $day='yearly';}
              $plan_amt = $price($key->plan,$day,$key->coupon,$key->login_id);
              ?>
                <tr>
                     <td><?= $i;?></td> 
                     <td><?= $user($key->login_id);?></td>
                     <td><?= $key->plan.'<br>'.$duration;?></td> 
                     <td><i class="fa fa-inr"></i> <?= $plan_amt;?></td> 
                     <td><?= date('d M, Y',strtotime($key->date));?></td>
                     <td><?= date('d M, Y',strtotime($key->edate));?></td>
                     <td><?= $key->coupon;?></td> 
                     <td><?= $poc($key->login_id);?></td>
                    <td><span class="status <?= $key->status;?>"><?= $key->status;?></span></td>
                </tr>  
               <?php }?>
                  </tbody>
              </table>
               <p class="pagination"><?php echo $links; ?></p>
          </div>
        </div>
         
  <script>
 function dateSearch(){
     $('#apply').css('display', 'block');
     var opt = $("#date option:selected").val();
      if(opt==='Custom Range'){$('#show').css('display', 'block'); $('#show1').css('display', 'block');}
      else {$('#show').css('display', 'none'); $('#show1').css('display', 'none');}
 }
 </script>
  
