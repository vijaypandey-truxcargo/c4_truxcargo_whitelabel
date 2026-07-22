<style>
    .agileinfo-grap {margin-bottom: 0em;}
    .circleProgress { width: 100%; margin: auto;} .mb-2{margin-bottom: 20px}
    .circleProgress span{position: absolute; left: 50%; top: 52%;  transform: translate(-50%,-50%);  color: #fff;  font-size: 16px;font-weight: 500;}
    .four-grid{margin-bottom: 28px;} #b2ccontainer svg, #member svg,#b2bcontainer svg{height:300px} .anychart-credits{display: none} .ph4{color: #7e7d7d; font-size: 18px; font-weight: 400;}
</style>
<script src="https://cdn.anychart.com/js/8.0.1/anychart-core.min.js"></script>
<script src="https://cdn.anychart.com/js/8.0.1/anychart-pie.min.js"></script>
<?php
$permission = $permission ?? [];
if (! is_array($permission)) {
    if (is_string($permission)) {
        $permission = array_values(array_filter(array_map('trim', explode(',', $permission)), fn ($value) => $value !== ''));
    } else {
        $permission = [];
    }
}

$b2b_con = 'status="Complete" and awb_status!="Not Picked"';
       $b2c_con = "status NOT IN ('Cancel','Not Picked')";
 
       $weight = $sum('order_waybills','cweight',$b2b_con.$condition);
       $last_weight = $sum('order_waybills','cweight',$b2b_con.$con);
       
       $lr_count = $count('order_waybills',$b2b_con.$condition);
       $lr_count_last = $count('order_waybills',$b2b_con.$con);
       
       $b2c_weight = $sum('b2c_waybills','charged_weight',$b2c_con.$condition);
       $b2c_last_weight = $sum('b2c_waybills','charged_weight',$b2c_con.$con);
       
       $awb_count = $count('b2c_waybills',$b2c_con.$condition);
       $awb_count_last = $count('b2c_waybills',$b2c_con.$con);
       
       $pweight = $sum('order_waybills','cweight','status="Complete" and awb_status NOT IN ("Not Picked","Manifested")'.$condition);
       $plr = $count('order_waybills','status="Complete" and awb_status NOT IN ("Not Picked","Manifested")'.$condition);
       
       $pb2c_weight = $sum('b2c_waybills','charged_weight','status NOT IN ("Not Picked","Manifested")'.$condition);
       $pawb = $count('b2c_waybills','status NOT IN ("Not Picked","Manifested")'.$condition);
       
       $customer = $count('registration','1=1 '.$condition);
       $kyc = $count('registration','status=1 and kyc=1');
       ?>
<div id="page-wrapper">
  <div class="graphs">
    <div style=" background: #fff; padding: 16px 0px 0; display: inline-block;  width: 100%;">
      <div class="col-lg-8"> 
          <?= form_open('admin/dashboard/search');?>
           <div class="col-lg-8"> 
                <p class="input-group"><input type="text" class="form-control"   name="lrn" placeholder="Search by AWB/LR No."></p>
           </div> 
          <div class="col-lg-4">
                  <p class="input-group"><input type="submit" name="apply" value="Search" class="btn btn-warning">  </p> 
          </div> 
         <?= form_close();?>
      </div> 
      <div class="col-lg-4"> 
          <?= form_open('admin/dashboard/');?> 
          <div class="col-lg-8">
                <p class="input-group">  
                      <select class="form-control" name="date" id="date" onchange="dateSearch()">
                          <option value="All" <?php if(session()->getFlashdata('date')=='All'){echo 'selected="selected"';}?>>All Dates</option>
                          <option value="Today" <?php if(session()->getFlashdata('date')=='Today'){echo 'selected="selected"';}?>>Today</option>
                          <option value="Yesterday" <?php if(session()->getFlashdata('date')=='Yesterday'){echo 'selected="selected"';}?>>Yesterday</option>
                          <option value="7 Days" <?php if(session()->getFlashdata('date')=='7 Days'){echo 'selected="selected"';}?>>Last 7 Days</option>
                          <option value="30 Days" <?php if(session()->getFlashdata('date')=='30 Days'){echo 'selected="selected"';}?>>Last 30 Days</option>
                          <option value="This Month" <?php if(session()->getFlashdata('date')=='This Month'){echo 'selected="selected"';}?>>This Month</option>
                          <option value="Last Month" <?php if(session()->getFlashdata('date')=='Last Month'){echo 'selected="selected"';}?>>Last Month</option>
                          <option value="Custom Range" <?php if(session()->getFlashdata('date')=='Custom Range'){echo 'selected="selected"';}?>>Custom Range</option>
                      </select>                     
                </p>
          </div> 
          <div class="col-lg-8" id="show" style="display:<?php if(session()->getFlashdata('date1')){echo 'block';} else {echo 'none';}?>">
                <p class="input-group"><input type="date" name="date1" class="form-control" value="<?= session()->getFlashdata('date1');?>"> </p>
          </div>
          <div class="col-lg-8" id="show1" style="display:<?php if(session()->getFlashdata('date2')){echo 'block';} else {echo 'none';}?>">
                <p class="input-group"><input type="date" name="date2" class="form-control" value="<?= session()->getFlashdata('date2');?>"> </p>
          </div>
          <div class="col-lg-3" id="apply">
                <p class="input-group"><input type="submit" name="submit" value="Apply" class="btn btn-warning"></p> 
          </div> 
          <?= form_close();?>
      </div> 
    </div>   
    <div class="four-grids mb-0">
        <div class="row"> 
            <div class="col-lg-8 col-sm-6">
                <div class="row">
                   <?php  if(in_array("B2B MIS", $permission)) { ?>    
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                       <a href="<?= base_url('admin/load/b2b');?>" class="ds">   
                        <div class="card bg-dash bg-secondary-gradient overflow-hidden"> 
                            <div class="card-body"> 
                                <div class="d-flex clearfix"> 
                                    <div class="text-left">
                                        <p class="mb-0 text-white fs-24">B2B Tonnage</p>
                                        <h1 class="mb-0 text-white fs-30"><?= number_format($weight/1000,2);?> </h1> 
                                        <p class="mb-0 text-white icon-service-1">
                                            <span class="text-white">
                                              <?php if($weight>0 && $last_weight>0){  if($weight>$last_weight){?>
                                                <i class="fa fa-chevron-up text-white"></i> +<?php echo round(($weight-$last_weight)*100/$last_weight,2);?>
                                              <?php } else {?>
                                                <i class="fa fa-chevron-down text-white"></i> <?php echo round(($weight-$last_weight)*100/$last_weight,2);?>
                                              <?php } }?>%</span>
                                            <br><span class="text-white">Last Month : <?= number_format($last_weight/1000,2);?></span>
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="bg-secondary icon-service1 text-white "> 
                                            <div class="p-2 circleProgress">
                                                <svg viewBox="0 0 100 100" style="display: block; width: 100%;">
                                                <path d="M 50,50 m 0,-45 a 45,45 0 1 1 0,90 a 45,45 0 1 1 0,-90" stroke="#000" stroke-width="9" fill-opacity="0"></path>
                                                <path d="M 50,50 m 0,-45 a 45,45 0 1 1 0,90 a 45,45 0 1 1 0,-90" stroke="#fff" stroke-width="9" fill-opacity="0" style="stroke-dasharray: 282.783, 282.783; stroke-dashoffset: <?php if($weight>0){ echo 283-round($pweight*283/$weight); }?>"></path>
                                                </svg>
                                                <span><?= number_format($pweight/1000,2);?></span> 
                                            </div>
                                        </span>
                                    </div> 
                                </div> 
                            </div> 
                            <img src="<?= base_url('backend/images/img-1.png')?>" alt="img" class="img-card-circle"> 
                        </div>
                       </a>     
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                       <a href="<?= base_url('admin/order/mis/all');?>" class="ds">   
                        <div class="card bg-dash bg-purple-gradient overflow-hidden"> 
                            <div class="card-body"> 
                                <div class="d-flex clearfix"> 
                                    <div class="text-left">
                                        <p class="mb-0 text-white fs-24">LRs Count</p>
                                        <h1 class="mb-0 text-white fs-30"><?= number_format($lr_count);?> </h1> 
                                        <p class="mb-0 text-white icon-service-1">
                                            <span class="text-white">
                                            <?php if($lr_count>0 && $lr_count_last>0){  if($lr_count>$lr_count_last){?>
                                                <i class="fa fa-chevron-up text-white"></i> +<?php echo  round(($lr_count-$lr_count_last)*100/$lr_count_last,2);?>
                                              <?php } else {?>
                                                <i class="fa fa-chevron-down text-white"></i> <?php echo round(($lr_count-$lr_count_last)*100/$lr_count_last,2);?>
                                              <?php }}?>%</span>
                                           
                                            <br><span class="text-white">Last Month : <?= $lr_count_last;?></span>
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="bg-purple icon-service1 text-white "> 
                                            <div class="p-2 circleProgress"> 
                                                <svg viewBox="0 0 100 100" style="display: block; width: 100%;">
                                                <path d="M 50,50 m 0,-45 a 45,45 0 1 1 0,90 a 45,45 0 1 1 0,-90" stroke="#000" stroke-width="9" fill-opacity="0"></path>
                                                <path d="M 50,50 m 0,-45 a 45,45 0 1 1 0,90 a 45,45 0 1 1 0,-90" stroke="#fff" stroke-width="9" fill-opacity="0" style="stroke-dasharray: 282.783, 282.783; stroke-dashoffset: <?php if($lr_count>0){ echo 283-round($plr*283/$lr_count); } ?>"></path>
                                                </svg>
                                                <span><?= $plr;?></span>
                                            </div>
                                        </span>
                                    </div> 
                                </div> 
                            </div> 
                            <img src="<?= base_url('backend/images/img-2.png')?>" alt="img" class="img-card-circle"> 
                        </div>
                       </a>     
                    </div>
                   <?php }
                   if(in_array("B2C MIS", $permission)) { ?>    
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                       <a href="<?= base_url('admin/load/b2c');?>" class="ds">   
                        <div class="card bg-dash bg-primary-gradient overflow-hidden"> 
                            <div class="card-body"> 
                                <div class="d-flex clearfix"> 
                                    <div class="text-left">
                                        <p class="mb-0 text-white fs-24">B2C Tonnage</p>
                                        <h1 class="mb-0 text-white fs-30"><?= number_format($b2c_weight/1000000,2);?> </h1> 
                                        <p class="mb-0 text-white icon-service-1">
                                            <span class="text-white">
                                            <?php if($b2c_weight>0 && $b2c_last_weight>0){  if($b2c_weight>$b2c_last_weight){?>
                                                <i class="fa fa-chevron-up text-white"></i> +<?php echo  round(($b2c_weight-$b2c_last_weight)*100/$b2c_last_weight,2);?>
                                              <?php } else {?>
                                                <i class="fa fa-chevron-down text-white"></i> <?php echo round(($b2c_weight-$b2c_last_weight)*100/$b2c_last_weight,2);?>
                                              <?php } }?>
                                            %</span>
                                            <br><span class="text-white">Last Month : <?= number_format($b2c_last_weight/1000000,2);?></span>
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="bg-primary icon-service1 text-white ">
                                            <div class="p-2 circleProgress">
                                                <svg viewBox="0 0 100 100" style="display: block; width: 100%;">
                                                <path d="M 50,50 m 0,-45 a 45,45 0 1 1 0,90 a 45,45 0 1 1 0,-90" stroke="#000" stroke-width="9" fill-opacity="0"></path>
                                                <path d="M 50,50 m 0,-45 a 45,45 0 1 1 0,90 a 45,45 0 1 1 0,-90" stroke="#fff" stroke-width="9" fill-opacity="0" style="stroke-dasharray: 282.783, 282.783; stroke-dashoffset: <?php if($b2c_weight>0){ echo 283-round($pb2c_weight*283/$b2c_weight); } ?>"></path>
                                                </svg>
                                                <span><?= number_format($pb2c_weight/1000000,2);?></span> 
                                            </div>
                                        </span>
                                    </div> 
                                </div> 
                            </div> 
                            <img src="<?= base_url('backend/images/img-2.png')?>" alt="img" class="img-card-circle"> 
                        </div>
                       </a>     
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                       <a href="<?= base_url('admin/orderb2c/mis/all');?>" class="ds">   
                        <div class="card bg-dash bg-success-gradient overflow-hidden"> 
                            <div class="card-body"> 
                                <div class="d-flex clearfix"> 
                                    <div class="text-left">
                                        <p class="mb-0 text-white fs-24">AWBs Count</p>
                                        <h1 class="mb-0 text-white fs-30"><?= number_format($awb_count);?> </h1> 
                                        <p class="mb-0 text-white icon-service-1">
                                            <span class="text-white">
                                                <?php  if($awb_count>0 && $awb_count_last>0){ if($awb_count>$awb_count_last){?>
                                                <i class="fa fa-chevron-up text-white"></i> +<?php echo round(($awb_count-$awb_count_last)*100/$awb_count_last,2);?>
                                              <?php } else {?>
                                                <i class="fa fa-chevron-down text-white"></i> <?php echo round(($awb_count-$awb_count_last)*100/$awb_count_last,2);?>
                                              <?php } }?>
                                                %</span>
                                            <br><span class="text-white">Last Month : <?= $awb_count_last;?></span>
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="bg-success icon-service1 text-white ">
                                            <div class="p-2 circleProgress">
                                                <svg viewBox="0 0 100 100" style="display: block; width: 100%;">
                                                <path d="M 50,50 m 0,-45 a 45,45 0 1 1 0,90 a 45,45 0 1 1 0,-90" stroke="#000" stroke-width="10" fill-opacity="0"></path>
                                                <path d="M 50,50 m 0,-45 a 45,45 0 1 1 0,90 a 45,45 0 1 1 0,-90" stroke="#fff" stroke-width="10" fill-opacity="0" style="stroke-dasharray: 282.783, 282.783; stroke-dashoffset: <?php if($awb_count>0){ echo 283-round($pawb*283/$awb_count);} ?>"></path>
                                                </svg>
                                                <span><?= $pawb;?></span>   
                                            </div> 
                                        </span>
                                    </div> 
                                </div> 
                            </div> 
                            <img src="<?= base_url('backend/images/img-1.png')?>" alt="img" class="img-card-circle"> 
                        </div>
                       </a>     
                    </div>
                   <?php }?> 
                    
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">               
                <div class="card bg-dash">
                <div class="card-body">
                    <h3 class="mb-3">Customers</h3>
                     <div class="row">
                         <div class="col-lg-6 col-sm-12">
                             <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
                    <canvas id="myChart" style="width:100%;max-width:600px;height: 190px "></canvas>
                    <script>
                        var xValues = ["Active","KYC Verified"];
                        var yValues = [<?= $active;?>,<?= $kyc;?>];
                        var barColors = ["#08a00b","#f85c34"];
                        new Chart("myChart", {
                            type: "doughnut",
                            data: { labels: xValues, datasets: [{ backgroundColor: barColors,  data: yValues }]  },
                            options: {
                                title: {  display: false, text: "" }
                            }});
                    </script>
                         </div>
                         <div class="col-lg-6 col-sm-12">
                            <table class="table table-striped">
                      <tbody>
                          <tr><td><b><?= session()->getFlashdata('date')?> Signup: </b></td><td><?= $customer;?></td></tr>
                        <tr><td><b>KYC Verified: </b></td><td><?= $kyc;?></td></tr>
                        <tr><td><b>Active : </b></td><td><?= $active;?></td></tr>
                       </tbody>
                    </table>  
                         </div>
                     </div>
                </div>
            </div>
            </div>
            </div>
        </div>    
       
        <div class="row">
            <?php if (in_array("View Wallet", $permission)) { ?>
            <div class="col-lg-3 col-sm-6"> 
               <a href="<?= base_url('admin/wallet/index/Recharge');?>" class="ds">  
                <div class="card  bg-dash"> 
                    <div class="card-body p-4">
                         <h6 class="mb-1 text-primary fs-18 font-weight-semibold">Wallet Recharge</h6> 
                        <div class="d-flex no-block align-items-center">
                            <div class="text-left"> 
                                <p class="mb-1 text-muted fs-16 font-weight-semibold"> <?php if(!empty($wallet)){ echo number_format($wallet/100000,2).' Lakh';} else { echo 0;}?></p>
                                 <span class="text-success" style="width: 100%;  font-size: 13px;  position: relative; display: block;">Last Month : <?= number_format($wallet_last/100000,2).' L';?></span>
                                 <span style="font-size: 13px;">
                                                <?php  if($wallet>0 && $wallet_last>0){ if($wallet>$wallet_last){?>
                                                <i class="fa fa-chevron-up"></i> +<?php echo round(($wallet-$wallet_last)*100/$wallet_last,2);?>
                                              <?php } else {?>
                                                <i class="fa fa-chevron-down"></i> <?php echo round(($wallet-$wallet_last)*100/$wallet_last,2);?>
                                              <?php } }?>
                                                %</span>
                            </div> 
                             <div class="ml-auto"> 
                                <span class="bg-primary icon-service-2 text-white "> <i class="fa fa-inr"></i> </span>
                            </div>
                            
                        </div>
                    </div>
                </div>
               </a>     
            </div>
            <div class="col-lg-3 col-sm-6"> 
               <a href="<?= base_url('admin/wallet/index/Deduction');?>" class="ds">  
                <div class="card  bg-dash"> 
                    <div class="card-body p-4">
                        <div class="d-flex no-block align-items-center">
                            <div class="text-left"> 
                                <h6 class="mb-1 text-secondary fs-18 font-weight-semibold">Wallet<br> Deduction</h6> 
                                <p class="mb-1 text-muted fs-16 font-weight-semibold"> <?php if(!empty($deduction)){ echo number_format($deduction/100000,2).' Lakh';} else { echo 0;}?></p>
                            </div> 
                            <div class="ml-auto"> 
                                <span class="bg-secondary icon-service-2 text-white "> <i class="fa fa-arrow-down"></i> </span>
                            </div> 
                        </div>
                    </div>
                </div>
               </a>     
            </div>
            <div class="col-lg-3 col-sm-6"> 
               <a href="<?= base_url('admin/wallet/index/Refund');?>" class="ds">  
                <div class="card  bg-dash"> 
                    <div class="card-body p-4">
                        <div class="d-flex no-block align-items-center">
                            <div class="text-left"> 
                                <h6 class="mb-1 text-success fs-18 font-weight-semibold">Wallet<br> Refund</h6> 
                                <p class="mb-1 text-muted fs-16 font-weight-semibold"> <?php if(!empty($refund)){ echo number_format($refund/100000,2).' Lakh';} else { echo 0;}?></p>
                            </div> 
                            <div class="ml-auto"> 
                                <span class="bg-success icon-service-2 text-white "> <i class="fa fa-plus"></i> </span>
                            </div> 
                        </div>
                    </div>
                </div>
               </a>     
            </div>
            
             <?php } if (in_array("B2B Pickup", $permission) || in_array("B2C Pickup", $permission)) { ?>
               <div class="col-lg-3 col-sm-6"> 
               <a href="<?= base_url('admin/order/pickup/all');?>" class="ds">  
                <div class="card  bg-dash"> 
                    <div class="card-body p-4">
                        <div class="d-flex no-block align-items-center">
                            <div class="text-left"> 
                                <h6 class="mb-1 text-purple fs-18 font-weight-semibold">B2B Pickup Request</h6> 
                                <p class="mb-1 text-muted fs-16 font-weight-semibold"> <?= number_format($count('pickup_request',$condition2));?></p>
                            </div> 
                            <div class="ml-auto"> 
                                <span class="bg-purple icon-service-2 text-white "> <i class="fa fa-truck"></i> </span>
                            </div> 
                        </div>
                    </div>
                </div>
               </a>     
              </div>
             <?php } ?>
        </div>
   </div>
    <div class="clearfix"></div>
    <!-------------- Payment mode bar chart ------------------> 
         
       <div class="agileinfo-grap">
           <div class="row">
            <?php  if (in_array("B2B Order", $permission)) { ?>
           <div class="col-lg-6 col-xs-12">
                <div class="card bg-dash"> 
                   <div class="card-body"><div id="container"></div></div>
                </div>  
                          <script src="<?= base_url('assets/js/highcharts.js');?>"></script>
                          <script src="https://code.highcharts.com/highcharts-3d.js"></script>
                          <script src="https://code.highcharts.com/modules/exporting.js"></script>
                          <script src="https://code.highcharts.com/modules/export-data.js"></script>
                          <script src="https://code.highcharts.com/modules/accessibility.js"></script>
                          <script>
                             const chart = new Highcharts.Chart({
                                 chart: {
                                     renderTo: 'container',
                                     type: 'column',
                                     options3d: {
                                         enabled: true,
                                         alpha: 0,
                                         beta: -10,
                                         depth: 50,
                                         viewDistance: 25
                                     }
                                 },
                                 xAxis: {
                                     categories: ['Manifested', 'In Transit', 'Pending', 'Dispatched', 'Delivered', 'RTO','Not Picked']
                                 },
                                 yAxis: {
                                     title: { enabled: false }
                                 },
                                 tooltip: {
                                     headerFormat: '<b>{point.key}</b><br>',
                                     pointFormat: 'Orders: {point.y}'
                                 },
                                 title: {
                                     text: 'B2B Orders Status',
                                     align: 'left'
                                 },
                                 legend: {enabled: false},
                                 plotOptions: {
                                     column: { depth: 35}
                                 },
                                 series: [{
                                         data: [<?= $count('order_waybills','awb_status="Manifested"'.$condition);?>,
                                                <?= $count('order_waybills','awb_status="In Transit"'.$condition);?>,
                                                <?= $count('order_waybills','awb_status="Pending"'.$condition);?>,
                                                <?= $count('order_waybills','awb_status="Dispatched"'.$condition);?>,
                                                <?= $count('order_waybills','awb_status="Delivered"'.$condition);?>,
                                                <?= $count('order_waybills','awb_status="RTO"'.$condition);?>,
                                                <?= $count('order_waybills','awb_status="Not Picked"'.$condition);?>
                                                ],
                                         colorByPoint: true,
                                         dataLabels: {
                                              enabled: true,
                                              rotation: 0,
                                              color: '#FFFFFF',
                                              align: 'right',
                                              y: 20, 
                                              style: {
                                                  fontSize: '12px',
                                                  textShadow: false,
                                                  fontWeight: 'normal'
                                              }
                                          }
                                     }]
                             });
                             function showValues() {
                             document.getElementById('alpha-value').innerHTML = chart.options.chart.options3d.alpha;
                             document.getElementById('beta-value').innerHTML = chart.options.chart.options3d.beta;
                             document.getElementById('depth-value').innerHTML = chart.options.chart.options3d.depth;
                         }
                         document.querySelectorAll('#sliders input').forEach(input => input.addEventListener('input', e => {
                          chart.options.chart.options3d[e.target.id] = parseFloat(e.target.value);
                          showValues();
                          chart.redraw(false);
                      }));
                      showValues();
                           </script>  
            </div>
            <?php } if (in_array("B2C Order", $permission)) { ?>
            <?php } if (in_array("B2B Order", $permission)) { ?>
           <div class="col-lg-6 col-xs-12">
            <div class="card bg-dash"> 
                <div class="card-body">
                    <?php
                    $b2b_cod = $count(
                        'order_waybills',
                        $b2b_con.'and d_mode="CoD" and topay!="Yes" and ftopay!="Yes"'.$condition
                    );
        
                    $b2b_prepaid = $count(
                        'order_waybills',
                        $b2b_con.'and d_mode="Prepaid"'.$condition
                    );
        
                    if($b2b_cod==0 && $b2b_prepaid==0){ 
                    ?>   
                        <h3 class="text-center mt-1 ph4">Payment Mode Of B2B Shipments</h3>
                        <img src="<?= base_url()?>assets/images/piechart.jpg" style="width:48%">
                    <?php } else { ?>
                        
                        <div id="b2bcontainer"></div>
        
                        <script>
                            anychart.onDocumentReady(function() {
                                var data = [
                                    {x: "COD", value: <?= $b2b_cod;?>},
                                    {x: "Prepaid", value: <?= $b2b_prepaid;?>}
                                ];
        
                                var chart = anychart.pie();
                                chart.title("Payment Mode Of B2B Shipments");
                                chart.data(data);
                                chart.container('b2bcontainer');
                                chart.draw();
                            });
                        </script>
        
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Payment Mode</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>COD</td>
                                    <td><?= $b2b_cod;?></td>
                                </tr>
                                <tr>
                                    <td>Prepaid</td>
                                    <td><?= $b2b_prepaid;?></td>
                                </tr>
                            </tbody>
                            <tfoot style="border-bottom: 1px solid #dedada;border-top: 1px solid #dedada;">
                                <tr>
                                    <td><b>Total</b></td>
                                    <td><b><?= $b2b_cod + $b2b_prepaid; ?></b></td>
                                </tr>
                            </tfoot>
                        </table>
        
                    <?php } ?>
                </div>
            </div>
        </div>
             <?php  } ?>
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
