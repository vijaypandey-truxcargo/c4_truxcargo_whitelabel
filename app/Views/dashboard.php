<style>
    #chartContainer canvas{ border-radius: 8px}
    .canvasjs-chart-credit{ color: #fff !important;} 
   
.skip{background: #FF5F63; color: #fff; float: right}
.profile-step:before{
     content: "";
    width: 100px;
    height: 133px;
    left: 0%;
    top: -3%;
    background: url(<?= base_url('assets/images/Boxes.png');?>);
    background-position: center;
    background-repeat: no-repeat;
    background-size: contain;
    position: absolute;
    z-index: 1;
}
.profile-step:after{
     content: "";
        width: 527px;
    height: 256px;
    right: -20%;
    bottom: 0%;
    background: url(<?= base_url('assets/images/step-profile.png');?>);
    background-position: center;
    background-repeat: no-repeat;
    background-size: contain;
    position: absolute;
    z-index: 1;
}
.pd-7{padding: 17% 0 12%}
.p-white{color:#fff !important}
   
 .fade {
   -webkit-animation-name: fadeIn;
	animation-name: fadeIn;
  -webkit-animation-duration: 3s;
  animation-duration: 3s;
  -webkit-animation-fill-mode: both;
  animation-fill-mode: both;
}
@-webkit-keyframes fadeIn {
  from {opacity: .4} 
  to {opacity: 1}
}
@keyframes fadeIn {
  from {opacity: .4} 
  to {opacity: 1}
} 
.main_points_img {
    width: 36px;
    height: 36px;
    margin: 4px 0;
}
.plan-Comparison-image{
    width: 49px;
    }
</style> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script src="<?= base_url('assets/js/jquery-3.3.1.min.js') ?>"></script>
<div class="content-inner hlo container-fluid" id="page_layout">
    <div class="row">
        <div class="col-lg-12">
            <?php                     
                $error=session()->getFlashdata('error');
                $error_class=session()->getFlashdata('error_class');
                if($error):?>
                     <div class="alert alert-dismissible <?= $error_class;?>"><strong><?= $error;?></strong> </div>
            <?php endif;?>    
        </div>
        <div class="col-lg-7 col-xl-7">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-5 gap-3">
                <div class="d-flex flex-column">
                    <h3>Welcome!</h3>
                    <p class="mb-0"><?= $user->first.' '.$user->last; ?></p>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-primary rounded-pill mt-2">Today's Orders |
                        <b><?= $b2b("date like '%".date('Y-m-d')."%'") + $b2c("date like '%".date('Y-m-d')."%'");?></b>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-7 col-xl-7">
            <div class="row">
                <div class="all-categories gap-2 mb-2">
                    <div class="categories_box odd">
                        <a href="<?= base_url('ratecalculator');?>">
                            <div class="wrap_all">
                                <div>
                                     <img src="<?= base_url('assets/images/index/calculator.svg');?>" alt="" srcset="" class="main_points_img">
                                </div>
                                <h6>Rate Calculator </h6>
                            </div>
                        </a>
                    </div>

                    <div class="categories_box even">
                        <a href="<?= base_url('wallet/');?>">
                            <div class="wrap_all">
                                <div>
                                     <img src="<?= base_url('assets/images/index/wallet.svg');?>" alt="" srcset="" class="main_points_img">
                                </div>
                                <h6>Wallet recharge </h6>
                            </div>
                        </a>
                    </div>
                    <div class="categories_box odd">
                        <a href="<?= base_url('reconciliation/index/all')?>">
                          <div class="wrap_all">
                            <div>
                                <img src="<?= base_url('assets/images/index/weight_reconcilation.svg');?>" alt="" srcset="" class="main_points_img">
                            </div>
                            <h6>Weight Dispute </h6>
                        </div>
                        </a>     
                    </div>
                    <div class="categories_box even">
                        <a href="<?= base_url('shipment');?>">
                            <div class="wrap_all">
                                <div>
                                    <img src="<?= base_url('assets/images/index/order.png');?>" alt="" srcset="" class="main_points_img">
                                </div>
                                <h6>Order Creation </h6>
                            </div>
                        </a> 
                    </div>
                    <div class="categories_box odd">
                        <a href="<?= base_url('warehouse');?>">
                          <div class="wrap_all">
                            <div>
                                <img src="<?= base_url('assets/images/index/track.png');?>" alt="" srcset="" class="main_points_img">
                            </div>
                            <h6>Pickup Points</h6>
                        </div>
                        </a>    
                    </div>
                    <div class="categories_box even">
                        <a href="<?= base_url('order/pickup_request');?>">
                        <div class="wrap_all">
                            <div>
                                <img src="<?= base_url('assets/images/index/early.svg');?>" alt="" srcset="" class="main_points_img">
                            </div>
                            <h6>Pickup Request </h6>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="pickup_fixed">
                         <?php  if($pickup){?>
                        <h4 class=""> Today Pickup's</h4>
                        <div class="insiders row">
                             <?php foreach ($pickup as $pickup){?> 
                            <div class="insiders_details col-xl-6 col-md-6 col-sm-12">
                                <h6><?= $pickup->pickup_id;?> </h6>
                                <h5>#Box : <?= $pickup->package_count;?></h5>
                                <small><b><?= $pickup->pickup_location;?>  </b> <?= $point($pickup->pickup_point,'B2B');?> </small>
                            </div>
                             <?php }?>
                        </div>
                         <?php }?>
                        <div class="text-center mt-2">
                            <a href="<?= base_url('order/pickup_request');?>" type="button" class="btn btn-success btn-green-gradient rounded-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17"
                                    fill="none" class="m-1">
                                    <g clip-path="url(#clip0_503_3724)">
                                        <path
                                            d="M0.75 12.1005V10.1914H10.1508V12.1004H6.8598C6.51385 11.2273 5.65939 10.5965 4.66362 10.5965C3.66784 10.5965 2.81337 11.2273 2.46741 12.1005H0.75Z"
                                            fill="white" stroke="white" stroke-width="0.5" />
                                        <path d="M11.2812 7.61371V5.30078H13.5818L14.4286 7.61371H11.2812Z"
                                            stroke="white" stroke-width="0.5" />
                                        <path
                                            d="M15.2269 8.73903L15.2269 8.73903L15.2282 8.74034L16.2435 9.74168C16.2436 9.74182 16.2437 9.74195 16.2439 9.74208C16.2487 9.74702 16.2501 9.75004 16.2508 9.7518C16.2517 9.75389 16.2538 9.76022 16.2538 9.77381V12.0999H15.9707C15.6248 11.2268 14.7703 10.596 13.7745 10.596C12.7648 10.596 11.9224 11.2276 11.5782 12.0999H11.2812V8.71484H15.1945C15.195 8.71498 15.1961 8.71538 15.198 8.7163C15.2033 8.71898 15.2133 8.72543 15.2269 8.73903ZM12.6605 8.81365H11.9921C11.687 8.81365 11.4497 9.05088 11.4497 9.3561C11.4497 9.66132 11.6869 9.89855 11.9921 9.89855H12.6605C12.944 9.89855 13.203 9.66896 13.203 9.3561C13.203 9.04323 12.944 8.81365 12.6605 8.81365Z"
                                            fill="white" stroke="white" stroke-width="0.5" />
                                        <path
                                            d="M4.11774 12.9492V12.9493C4.11774 13.239 4.34008 13.5056 4.66019 13.5056C4.98034 13.5056 5.20264 13.239 5.20264 12.9493C5.20264 12.644 4.96534 12.4068 4.66019 12.4068C4.35497 12.4068 4.1178 12.6441 4.11774 12.9492ZM4.66019 11.6953C5.35615 11.6953 5.91413 12.2642 5.91413 12.9493C5.91413 13.6467 5.35765 14.2032 4.66019 14.2032C3.96272 14.2032 3.40625 13.6467 3.40625 12.9493C3.40625 12.2642 3.96426 11.6953 4.66019 11.6953Z"
                                            fill="white" stroke="white" stroke-width="0.5" />
                                        <path
                                            d="M13.7696 11.6953C14.4531 11.6953 15.0235 12.2657 15.0235 12.9493C15.0235 13.6452 14.4546 14.2032 13.7696 14.2032C13.0721 14.2032 12.5156 13.6467 12.5156 12.9493C12.5156 12.2642 13.0736 11.6953 13.7696 11.6953ZM14.312 12.9493C14.312 12.644 14.0747 12.4068 13.7696 12.4068C13.4798 12.4068 13.2132 12.6291 13.2132 12.9493C13.2132 13.2544 13.4644 13.5056 13.7696 13.5056C14.0897 13.5056 14.312 13.239 14.312 12.9493Z"
                                            fill="white" stroke="white" stroke-width="0.5" />
                                        <path
                                            d="M6.01424 7.6561V7.07305C6.14189 7.12995 6.27588 7.13745 6.39959 7.10083C6.54186 7.0587 6.65477 6.96324 6.72664 6.85112C6.79844 6.73912 6.83849 6.59596 6.81482 6.4482C6.79055 6.29671 6.70239 6.16169 6.55819 6.06992C6.55119 6.06487 6.54179 6.05802 6.53028 6.04963C6.45422 5.99421 6.28601 5.87165 6.1097 5.7566C6.00397 5.68761 5.88959 5.61747 5.78493 5.56368C5.73268 5.53682 5.67864 5.51185 5.62647 5.49307C5.57847 5.47579 5.51306 5.45661 5.44395 5.45661C5.37543 5.45661 5.31176 5.47698 5.26862 5.49327C5.22041 5.51147 5.17029 5.53552 5.1219 5.56111C5.02492 5.61239 4.91686 5.67953 4.81478 5.7461C4.71987 5.80799 4.62556 5.87241 4.54524 5.92726C4.53858 5.93181 4.53201 5.9363 4.52555 5.94071C4.43677 6.00132 4.37536 6.04278 4.34324 6.06151L4.34313 6.06132L4.33438 6.06692C4.18723 6.16111 4.10121 6.3005 4.08009 6.45348C4.05992 6.59951 4.10081 6.74053 4.17166 6.85106C4.24251 6.96159 4.35357 7.0576 4.49462 7.10032C4.61652 7.13724 4.74854 7.13024 4.87365 7.07375V7.6561C4.87365 7.83033 4.94313 7.97838 5.0597 8.07975C5.17127 8.17677 5.31207 8.21943 5.44395 8.21943C5.57582 8.21943 5.71662 8.17677 5.82819 8.07975C5.94477 7.97838 6.01424 7.83033 6.01424 7.6561ZM5.05341 3.29688H5.84844V3.47922H5.0534L5.05341 3.33235L5.05341 3.29688ZM2.54688 9.09105V3.29688H3.95461V3.61781C3.95461 4.14576 4.3868 4.57799 4.9148 4.57799H5.98706C6.51502 4.57799 6.94725 4.14581 6.94725 3.61781V3.29688H8.35499V9.09105H2.54688Z"
                                            fill="white" stroke="white" stroke-width="0.5" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_503_3724">
                                            <rect width="16" height="16" fill="white" transform="translate(0.5 0.75)" />
                                        </clipPath>
                                    </defs>
                                </svg>Add Pickup Request
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-body latesto">
                    <h4 class="mb-4 mt-1"> Latest Orders</h4>
                    <div class="fancy-table table-responsive border rounded">
                        <table class="table table-striped mb-0 ">
                            <thead>
                                <tr>
                                    <th scope="col">LR/AWB NO</th>
                                    <th scope="col">CONSIGNEE </th>
                                    <th scope="col">MANIFESTED ON </th>
                                    <th scope="col">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php foreach ($orders as $orders){?>
                                <tr>
                                    <td class="text-dark"><a href="<?= base_url("order/track/{$orders->id}");?>"><?= $orders->lrnum;?></a></td>
                                    <td class="text-dark"><?= $customer($orders->customer_id);?></td>
                                    <td class="text-dark"><?= date('d M,Y', strtotime($orders->date));?></td>
                                    <td>
                                        <span class="status <?= $orders->awb_status;?>"><?= $orders->awb_status;?></span>
                                    </td>
                                </tr>
                                 <?php }?>
                                 <?php foreach ($b2corders as $b2corders){?>
                                <tr>
                                    <td><a href="<?= base_url("orderb2c/track/{$b2corders->id}");?>"><?= $b2corders->waybill;?></a></td>
                                    <td><?= $b2corders->name;?><br> <?= $b2corders->city;?>,<?= $b2corders->state;?></td>                     
                                    <td><?= date('d M,Y', strtotime($b2corders->date));?></td>
                                    <td><span class="status <?= $b2corders->status;?>"><?= $b2corders->status;?></span></td>
                                </tr>
                              <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            
            <?php if(isset($user->wallet) && $user->wallet == 'Prepaid') { ?>
            <div class="index_plans">
                <div class="row">     
                    <div class="col-lg-12">
                        <p>Our Plans</p>
                    </div>
                     <div class="col-lg-12">
                    <div class="row">
                    <div class="col-lg-6 ppr-0">
                         <a href="<?= base_url('plans')?>">
                            <div class="inside_index_plan" style="background:#546CFF">
                                  <img src="<?= base_url('assets/images/1.png');?>" alt="" srcset="" class="plan-Comparison-image">
                                <div>
                                    <h5>Basic Plan</h5>
                                    <h4>Free</h4>
                                </div>

                            </div>
                        </a>
                         <a href="<?= base_url('plans')?>">
                            <div class="inside_index_plan" style="background:#4656A3">
                                <img src="<?= base_url('assets/images/trophy.png');?>" alt="" srcset="" class="plan-Comparison-image">
                                <div>
                                    <h5>SME</h5>
                                    <h4>₹<span><?= $pplan('Small Business');?></span></h4>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="col-lg-6">
                        <a href="<?= base_url('plans')?>">
                            <div class="inside_index_plan" style="background:#632583">
                                <img src="<?= base_url('assets/images/medal.png');?>" alt="" srcset="" class="plan-Comparison-image">
                                <div>
                                    <h5>Startup</h5>
                                    <h4>₹<span><?= $pplan('Startup');?></span> </h4>
                                </div>

                            </div>
                        </a>
                        <a href="<?= base_url('plans')?>">
                            <div class="inside_index_plan" style="background:#BC3C43">
                                <img src="<?= base_url('assets/images/crown.png');?>" alt="" srcset="" class="plan-Comparison-image">
                                <div>
                                    <h5>Enterprise</h5>
                                    <h4>₹<span><?= $pplan('Enterprise');?></span></h4>
                                </div>
                            </div>
                        </a>
                    </div>
                    </div>
                     </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-7">
                         <a href="<?= base_url('plans')?>" class="btn btn-success btn-green-gradient rounded-pill mb-2"><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <g clip-path="url(#clip0_2624_20933)">
                                    <path
                                        d="M24 5.68791C24.0045 5.39471 23.89 5.11207 23.685 4.90253C23.4807 4.69375 23.1981 4.57617 22.9049 4.57617H18.0842C17.9478 4.57617 17.825 4.65833 17.7729 4.7842C17.7225 4.91007 17.7518 5.05479 17.8476 5.15126L18.7769 6.07985C18.9238 6.22607 19.0075 6.4243 19.0075 6.63157C19.0075 6.83809 18.9261 7.03707 18.7776 7.18329L14.0595 11.8993C13.6555 12.3041 13.0005 12.3041 12.5981 11.8993L9.55387 8.85505C9.37374 8.67491 9.1303 8.57391 8.87555 8.57391C8.6208 8.57391 8.3766 8.67491 8.19646 8.85505L0.280376 16.7669C-0.0934585 17.1422 -0.0934585 17.7505 0.280376 18.1258L1.30088 19.1449C1.48102 19.325 1.72446 19.4252 1.97921 19.4252C2.23396 19.4252 2.4774 19.325 2.65754 19.1464L8.1422 13.6623C8.54618 13.259 9.20114 13.259 9.60437 13.6623L12.595 16.6538C12.7895 16.8468 13.051 16.9545 13.3269 16.9545C13.6027 16.9545 13.8658 16.8445 14.0602 16.6538L21.1571 9.55601C21.3025 9.40979 21.5015 9.32763 21.7073 9.32763C21.9138 9.32763 22.1135 9.40979 22.2582 9.55676L23.3533 10.6549C23.449 10.7507 23.5915 10.7801 23.7181 10.7296C23.8432 10.6791 23.9239 10.5577 23.9284 10.4228L24 5.68791Z"
                                        fill="white" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_2624_20933">
                                        <rect width="24" height="24" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg> See Shipping Plans</a>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <ol class="carousel-indicators" style="display:none">
                    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
                    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
                    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <!-- <div class="carousel-item active">-->
                    <!--    <div class="inside_carousel text-white">-->
                    <!--        <h4>Shipping Plans</h4>-->
                    <!--        <p>Reducing shipment costs, offering Shipping Plans, extensive service coverage, and faster delivery times are our primary strengths in Ecommerce logistics.</p>-->

                    <!--        <a href="<?= base_url('plans');?>" class="btn btn-light mt-3">Shipping Plans</a>-->
                    <!--    </div>-->
                    <!--    <img src="<?= base_url('assets/images/index/slides/slide1.png');?>" class="d-block w-100" alt="#">-->
                    <!--</div>-->
                    
                    <div class="carousel-item active">
                        <div class="inside_carousel text-white">
                            <h4>Multiple Pickup Location</h4>
                            <p>Our pickup locations provide versatile options, adapting to the unique demands of diverse shipping scenarios.</p>
                            <a href="<?= base_url('warehouse');?>" class="btn btn-light mt-3">Multiple Pickup Location</a>
                        </div>
                        <img src="<?= base_url('assets/images/index/slides/slide2.png');?>" class="d-block w-100" alt="#">
                    </div>
              
                </div>
            </div>
        
            <div class="activites_dashboard">
                <h5>Your Bookings</h5>
                <div class="d-flex gap-4 mt-2">
                    <div class="activites_box mr-2">
                        <p>Today Orders</p>
                        <h4><?= $b2b("date like '%".date('Y-m-d')."%'") + $b2c("date like '%".date('Y-m-d')."%'");?></h4>
                    </div>
                    <div class="activites_box">
                        <p>Overall Orders</p>
                        <h4><?= $b2b("1=1")+$b2c("1=1");?></h4>
                    </div>
                </div>
            </div>

            <div class="remmitance_dashboard">
                <div class="flex-remmitance_dashboard">
                    <div class="rotate_class">
                        <h5>Remittance</h5>
                    </div>
                    <div class="remmitance_dashboard_card">
                        <div class="remmitance_box last ">
                            <p>Last Remittance</p>
                            <h4><i class="fa-solid fa-indian-rupee-sign"></i> <?= number_format((float) ($last_remittance ?? 0));?></h4>
                        </div>
                        <div class="remmitance_box next">
                            <p>Next Remittance</p>
                            <h4><i class="fa-solid fa-calendar"></i> <?= date('d M, Y', strtotime('next Friday'));?></h4>
                        </div>
                        <div class="remmitance_box upcoming">
                            <p>Upcoming Remittance</p>
                            <h4><i class="fa-solid fa-indian-rupee-sign"></i> <?= number_format((float) ($upcod ?? 0), 2);?></h4>
                        </div>
                    </div>
                </div>
            </div>

            
         
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12 mt-5">
            <div class="card shadow order_Details">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <h3>Order Performance </h3>                            
                        </div>
                    </div>
                    <div class="col-md-3 col-5">
                        <div class="today_order">
                            <svg xmlns="http://www.w3.org/2000/svg" width="49" height="50" viewBox="0 0 24 25" fill="none">
                                <path d="M9.5 16.0898V17.0898H8.5V16.0898H7.5V19.0898H10.5005L10.5 16.0898H9.5Z" fill="#7939CC" />
                                <path d="M9.5 5.08984H14.5C14.7759 5.08984 15 4.86548 15 4.58984V2.08984H9V4.58984C9 4.86548 9.22412 5.08984 9.5 5.08984Z"
                                    fill="#7939CC" />
                                <path d="M9.5 9.08984V10.0898H8.5V9.08984H7.5V12.0898H10.5005L10.5 9.08984H9.5Z"
                                    fill="#7939CC" />
                                <path
                                    d="M18.5 2.08984H16V4.58984C16 5.41699 15.3271 6.08984 14.5 6.08984H9.5C8.67285 6.08984 8 5.41699 8 4.58984V2.08984H5.5C4.67499 2.08984 4 2.76483 4 3.58984V20.5898C4 21.4148 4.67499 22.0898 5.5 22.0898H18.5C19.325 22.0898 20 21.4148 20 20.5898V3.58984C20 2.76483 19.325 2.08984 18.5 2.08984ZM11.5 19.0898C11.5 19.6414 11.0513 20.0898 10.5 20.0898H7.5C6.94873 20.0898 6.5 19.6414 6.5 19.0898V16.0898C6.5 15.5383 6.94873 15.0898 7.5 15.0898H10.5C11.0513 15.0898 11.5 15.5383 11.5 16.0898V19.0898ZM11.5 12.0898C11.5 12.6414 11.0513 13.0898 10.5 13.0898H7.5C6.94873 13.0898 6.5 12.6414 6.5 12.0898V9.08984C6.5 8.53833 6.94873 8.08984 7.5 8.08984H10.5C11.0513 8.08984 11.5 8.53833 11.5 9.08984V12.0898ZM17.5 19.0898H13.5V18.0898H17.5V19.0898ZM17.5 17.0898H13.5V16.0898H17.5V17.0898ZM17.5 12.0898H13.5V11.0898H17.5V12.0898ZM17.5 10.0898H13.5V9.08984H17.5V10.0898Z"
                                    fill="#7939CC" />
                            </svg>
                            <div>
                                <p>Total’s Order</p>
                                <h4><?= $b2b("1=1")+$b2c("1=1");?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9 col-7 step_part">
                        <div class="premium_part">
                            <div class="order_shipment_card today_order_number">
                                <div>
                                    <h5>Manifested</h5>
                                    <h2><?= $manifested= $b2b("awb_status='Manifested'") + $b2c("status='Manifested'") ;?></h2>
                                </div>
                                <div>
                                    <h5>In-Transit</h5>
                                    <h2><?= $transit= $b2b("awb_status='In Transit'") + $b2c("status='In Transit'") ;?></h2>
                                </div>                                
                                <div>
                                    <h5>Pending</h5>
                                    <h2><?= $pending = $b2b("awb_status='Pending'") + $b2c("status='Pending'") ;?></h2>
                                </div>
                                <div>
                                    <h5>OFD</h5>
                                    <h2><?= $ofd = $b2b("awb_status='Dispatched'") + $b2c("status='Dispatched'") ;?></h2>
                                </div>
                                <div>
                                    <h5>Delivered</h5>
                                    <h2><?= $delivered = $b2b("awb_status='Delivered'") + $b2c("status='Delivered'") ;?></h2>
                                </div>
                                <div>
                                    <h5>RTO</h5>
                                    <h2><?= $rto = $b2b("awb_status='RTO'") + $b2c("status='RTO'") ;?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            <h3>Order Revenue </h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-5">
                        <div class="today_order Revenue">
                            <svg xmlns="http://www.w3.org/2000/svg" width="49" height="50" viewBox="0 0 49 49"
                                fill="none">
                                <path d="M19.6016 19.4375H17.9141V29.5625H19.6016V19.4375Z" fill="#1325A5" />
                                <path d="M25.2266 23.1875H23.5391V29.5625H25.2266V23.1875Z" fill="#1325A5" />
                                <path
                                    d="M30.5248 13.2495L21.9453 4.66992V11.187C21.9459 11.7338 22.1634 12.258 22.5501 12.6447C22.9367 13.0313 23.461 13.2488 24.0078 13.2495H30.5248Z"
                                    fill="#1325A5" />
                                <path
                                    d="M34.1328 29C32.5382 29 30.9794 29.4729 29.6535 30.3588C28.3277 31.2447 27.2943 32.5039 26.684 33.9771C26.0738 35.4503 25.9141 37.0714 26.2252 38.6354C26.5363 40.1994 27.3042 41.636 28.4318 42.7635C29.5593 43.8911 30.9959 44.659 32.5599 44.9701C34.1239 45.2812 35.745 45.1215 37.2182 44.5113C38.6914 43.901 39.9506 42.8677 40.8365 41.5418C41.7225 40.2159 42.1953 38.6571 42.1953 37.0625C42.1896 34.9259 41.3383 32.8785 39.8276 31.3678C38.3168 29.857 36.2694 29.0057 34.1328 29ZM36.0598 34.7188H37.4141C37.5633 34.7188 37.7063 34.778 37.8118 34.8835C37.9173 34.989 37.9766 35.1321 37.9766 35.2812C37.9766 35.4304 37.9173 35.5735 37.8118 35.679C37.7063 35.7845 37.5633 35.8438 37.4141 35.8438H36.0598C35.9255 36.63 35.5176 37.3435 34.9081 37.8581C34.2987 38.3728 33.527 38.6554 32.7294 38.6562H32.2658L35.9314 42.1378C36.0399 42.2421 36.1031 42.3849 36.1073 42.5353C36.1115 42.6857 36.0564 42.8318 35.9539 42.9419C35.9016 42.9983 35.8384 43.0433 35.768 43.0744C35.6977 43.1054 35.6218 43.1218 35.545 43.1226C35.4015 43.1236 35.2632 43.0693 35.1587 42.9711L30.4652 38.5367C30.3831 38.4559 30.3265 38.3528 30.3023 38.2401C30.2781 38.1275 30.2873 38.0102 30.3289 37.9028C30.3697 37.7962 30.4411 37.7041 30.5341 37.638C30.6271 37.5719 30.7375 37.5348 30.8516 37.5312H32.7294C33.7777 37.5312 34.6606 36.875 34.9115 35.8438H30.8516C30.7024 35.8438 30.5593 35.7845 30.4538 35.679C30.3483 35.5735 30.2891 35.4304 30.2891 35.2812C30.2891 35.1321 30.3483 34.989 30.4538 34.8835C30.5593 34.778 30.7024 34.7188 30.8516 34.7188H34.9115C34.7802 34.2388 34.4962 33.8146 34.1026 33.5101C33.709 33.2057 33.227 33.0376 32.7294 33.0312H30.8516C30.7024 33.0312 30.5593 32.972 30.4538 32.8665C30.3483 32.761 30.2891 32.6179 30.2891 32.4688C30.2891 32.3196 30.3483 32.1765 30.4538 32.071C30.5593 31.9655 30.7024 31.9062 30.8516 31.9062H37.4141C37.5633 31.9062 37.7063 31.9655 37.8118 32.071C37.9173 32.1765 37.9766 32.3196 37.9766 32.4688C37.9766 32.6179 37.9173 32.761 37.8118 32.8665C37.7063 32.972 37.5633 33.0312 37.4141 33.0312H35.2438C35.6723 33.5036 35.9556 34.0895 36.0598 34.7188Z"
                                    fill="#1325A5" />
                                <path
                                    d="M8.25781 3.875C7.71099 3.87562 7.18675 4.09312 6.80009 4.47978C6.41343 4.86644 6.19593 5.39068 6.19531 5.9375V37.4375C6.19593 37.9843 6.41343 38.5086 6.80009 38.8952C7.18675 39.2819 7.71099 39.4994 8.25781 39.5H25.2742C24.6461 37.2157 24.9244 34.7771 26.0509 32.693C27.1774 30.6089 29.0652 29.0403 31.3203 28.3144V14.375H24.0078C23.1627 14.3741 22.3525 14.0379 21.755 13.4404C21.1574 12.8428 20.8213 12.0326 20.8203 11.1875V3.875H8.25781ZM22.4141 22.625C22.4141 22.4758 22.4733 22.3327 22.5788 22.2273C22.6843 22.1218 22.8274 22.0625 22.9766 22.0625H25.7891C25.9382 22.0625 26.0813 22.1218 26.1868 22.2273C26.2923 22.3327 26.3516 22.4758 26.3516 22.625V30.125C26.3516 30.2742 26.2923 30.4173 26.1868 30.5227C26.0813 30.6282 25.9382 30.6875 25.7891 30.6875H22.9766C22.8274 30.6875 22.6843 30.6282 22.5788 30.5227C22.4733 30.4173 22.4141 30.2742 22.4141 30.125V22.625ZM15.1016 30.125C15.1016 30.2742 15.0423 30.4173 14.9368 30.5227C14.8313 30.6282 14.6882 30.6875 14.5391 30.6875H11.7266C11.5774 30.6875 11.4343 30.6282 11.3288 30.5227C11.2233 30.4173 11.1641 30.2742 11.1641 30.125V24.5C11.1641 24.3508 11.2233 24.2077 11.3288 24.1023C11.4343 23.9968 11.5774 23.9375 11.7266 23.9375H14.5391C14.6882 23.9375 14.8313 23.9968 14.9368 24.1023C15.0423 24.2077 15.1016 24.3508 15.1016 24.5V30.125ZM20.7266 30.125C20.7266 30.2742 20.6673 30.4173 20.5618 30.5227C20.4563 30.6282 20.3132 30.6875 20.1641 30.6875H17.3516C17.2024 30.6875 17.0593 30.6282 16.9538 30.5227C16.8483 30.4173 16.7891 30.2742 16.7891 30.125V18.875C16.7891 18.7258 16.8483 18.5827 16.9538 18.4773C17.0593 18.3718 17.2024 18.3125 17.3516 18.3125H20.1641C20.3132 18.3125 20.4563 18.3718 20.5618 18.4773C20.6673 18.5827 20.7266 18.7258 20.7266 18.875V30.125Z"
                                    fill="#1325A5" />
                                <path d="M13.9766 25.0625H12.2891V29.5625H13.9766V25.0625Z" fill="#1325A5" />
                            </svg>
                            <div>
                                <p>Today’s Revenue</p>
                                <h4><small><i class="fa fa-inr"></i></small> <?php $tt= $revenue('order_waybills','lastpay',"date like '%".date('Y-m-d')."%' and awb_status!='Notpicked'")+$revenue('b2c_waybills','lastpay',"date like '%".date('Y-m-d')."%' and status!='Notpicked'");  echo round($tt/1000,2).'K';?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9 col-7 step_part">
                        <div class="premium_part">
                            <div class="order_shipment_card Revenue_number">
                                <div>
                                    <h5>This Week</h5>
                                    <h2><small><i class="fa fa-inr"></i></small> <?php
                                       $order_date2 = date('Y-m-d',strtotime("+1 days")); $order_date1 = date('Y-m-d',strtotime("-7 days"));
                                    $r= $revenue('order_waybills','lastpay',"date between  '".$order_date1."' and '".$order_date2."' and awb_status!='Notpicked'")+$revenue('b2c_waybills','lastpay',"date between  '".$order_date1."' and '".$order_date2."' and status!='Notpicked'");
                                    echo round($r/1000,2).'K';
                                    ?></h2>
                                </div>
                                <div>
                                    <h5>This Month</h5>
                                    <h2><small><i class="fa fa-inr"></i></small> <?php $m=$revenue('order_waybills','lastpay',"date like '%".date('Y-m')."%' and awb_status!='Notpicked'")+$revenue('b2c_waybills','lastpay',"date like '%".date('Y-m')."%' and status!='Notpicked'"); echo round($m/1000,2).'K'; ?></h2>
                                </div>
                                <div>
                                    <h5>Last 3 Month</h5>
                                    <h2><small><i class="fa fa-inr"></i></small> <?= round(($revenue('b2b_billing','amount',"end_date >= now()-interval 3 month and live='1'")+$revenue('b2c_billing','amount',"end_date >= now()-interval 3 month"))/1000); ?>K</h2>
                                </div>
                                <div>
                                    <h5>Last 6 Month</h5>
                                    <h2><small><i class="fa fa-inr"></i></small> <?= round(($revenue('b2b_billing','amount',"end_date >= now()-interval 6 month and live='1'")+$revenue('b2c_billing','amount',"end_date >= now()-interval 6 month"))/1000); ?>K</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div> <h3>Average Shipping</h3></div>
                    </div>
                    <div class="col-md-3 col-5">
                        <div class="today_order average">
                            <svg xmlns="http://www.w3.org/2000/svg" width="49" height="50" viewBox="0 0 49 49"
                                fill="none">
                                <g clip-path="url(#clip0_503_1478)">
                                    <path
                                        d="M32.1968 35.8613H25.5118C25.2368 35.8613 25.0117 36.0863 25.0117 36.3614V44.0015C25.0117 44.2765 25.2367 44.5015 25.5118 44.5015H32.1968C32.4718 44.5015 32.6969 44.2765 32.6969 44.0015V36.3614C32.6969 36.0863 32.4719 35.8613 32.1968 35.8613ZM30.7569 43.0164H26.9569C26.6769 43.0164 26.4569 42.7914 26.4569 42.5164C26.4569 42.2413 26.6769 42.0163 26.9569 42.0163H30.7569C31.032 42.0163 31.257 42.2413 31.257 42.5164C31.2569 42.7914 31.0319 43.0164 30.7569 43.0164ZM30.7569 40.6813H26.9569C26.6769 40.6813 26.4569 40.4562 26.4569 40.1812C26.4569 39.9063 26.6769 39.6812 26.9569 39.6812H30.7569C31.032 39.6812 31.257 39.9063 31.257 40.1812C31.2569 40.4562 31.0319 40.6813 30.7569 40.6813ZM30.7569 38.3463H26.9569C26.6769 38.3463 26.4569 38.1263 26.4569 37.8462C26.4569 37.5712 26.6769 37.3461 26.9569 37.3461H30.7569C31.032 37.3461 31.257 37.5712 31.257 37.8462C31.2568 38.1263 31.0319 38.3463 30.7569 38.3463Z"
                                        fill="#2A3EC7" />
                                    <path
                                        d="M14.8985 23.1436L16.0424 21.8022C16.1373 21.6909 16.2763 21.6266 16.4227 21.6266C16.5692 21.6266 16.7081 21.6909 16.8031 21.8022L18.4471 23.7302L20.0911 21.8022C20.186 21.6909 20.325 21.6266 20.4714 21.6266C20.6178 21.6266 20.7568 21.6909 20.8517 21.8022L21.9956 23.1436V13.7207H14.8984L14.8985 23.1436Z"
                                        fill="#2A3EC7" />
                                    <path
                                        d="M33.9955 23.8908C32.1341 24.5987 31.005 23.6876 29.9605 22.0107C27.8001 22.0107 26.6392 20.3777 26.7305 18.7808C26.7356 18.7557 26.7205 18.7357 26.7055 18.7307C24.9568 18.1339 24.161 16.1255 24.9554 14.5057C24.9655 14.4907 24.9605 14.4606 24.9404 14.4457C24.7054 14.2357 24.5005 13.9907 24.3404 13.7207H22.9954V24.5008C22.9954 24.7108 22.8654 24.8959 22.6653 24.9708C22.4783 25.0389 22.2519 24.9911 22.1153 24.8259L20.4702 22.8958L18.8252 24.8259C18.6353 25.0459 18.2552 25.0459 18.0652 24.8259L16.4202 22.8958L14.7751 24.8259C14.6401 24.9859 14.4201 25.0408 14.2251 24.9708C14.0251 24.8959 13.8951 24.7108 13.8951 24.5008V13.7207H2.69534C1.31534 13.7207 0.195312 14.8407 0.195312 16.2207V46.0009C0.195312 47.3809 1.31534 48.5009 2.69534 48.5009H34.1954C35.5754 48.5009 36.6955 47.3809 36.6955 46.0009V24.8208C35.956 24.9843 34.6594 24.2601 33.9955 23.8908ZM33.6954 44.0008C33.6954 44.8258 33.0204 45.5008 32.1954 45.5008H25.5103C24.6853 45.5008 24.0103 44.8258 24.0103 44.0008V36.3608C24.0103 35.5358 24.6853 34.8608 25.5103 34.8608H32.1954C33.0204 34.8608 33.6954 35.5358 33.6954 36.3608V44.0008Z"
                                        fill="#2A3EC7" />
                                    <path
                                        d="M38.6805 13.0625C38.1054 13.0625 37.8555 13.5626 37.8555 14.1326C37.8555 14.7025 38.1105 15.1975 38.6805 15.1975C39.2555 15.1975 39.5104 14.7025 39.5104 14.1326C39.5106 13.5626 39.2555 13.0625 38.6805 13.0625Z"
                                        fill="#2A3EC7" />
                                    <path
                                        d="M35.2565 10.2028C35.2565 9.63287 35.0015 9.13281 34.4266 9.13281C33.8566 9.13281 33.6016 9.63287 33.6016 10.2028C33.6016 10.7677 33.8566 11.2677 34.4266 11.2677C35.0016 11.2677 35.2565 10.7677 35.2565 10.2028Z"
                                        fill="#2A3EC7" />
                                    <path
                                        d="M36.553 5.19727C32.7222 5.19727 29.6055 8.32252 29.6055 12.1641C29.6055 16.0057 32.7221 19.131 36.553 19.131C40.3839 19.131 43.5003 16.0057 43.5003 12.1641C43.5003 8.32252 40.3837 5.19727 36.553 5.19727ZM32.6103 10.2015C32.6103 9.09145 33.2303 8.19642 34.4253 8.19642C35.6053 8.19642 36.2403 9.07636 36.2403 10.2015C36.2403 11.3164 35.6153 12.2015 34.4253 12.2015C33.2354 12.2014 32.6103 11.3115 32.6103 10.2015ZM34.9332 15.8533C34.7326 16.1763 34.314 16.2553 34.0296 15.9863C33.8334 15.8 33.7972 15.5007 33.9435 15.2746L38.2067 8.45498C38.4157 8.12077 38.8492 8.11017 39.095 8.31727C39.2681 8.4608 39.365 8.75892 39.1681 9.07289L34.9332 15.8533ZM38.6803 16.1315C37.4953 16.1315 36.8702 15.2465 36.8702 14.1315C36.8702 13.0164 37.4902 12.1264 38.6803 12.1264C39.8602 12.1264 40.4953 13.0064 40.4953 14.1315C40.4954 15.2365 39.8754 16.1315 38.6803 16.1315Z"
                                        fill="#2A3EC7" />
                                    <path
                                        d="M47.499 10.6297C47.149 10.3198 47.049 9.80474 47.254 9.38474C47.8029 8.26574 47.238 6.94649 46.079 6.54974C45.639 6.39983 45.349 5.96474 45.379 5.4897C45.4523 4.26505 44.455 3.2502 43.209 3.32474C42.744 3.35474 42.309 3.0597 42.159 2.61974C41.763 1.45274 40.4302 0.899423 39.329 1.44477C38.909 1.6498 38.399 1.5448 38.084 1.18977C37.2825 0.273829 35.8342 0.264642 35.019 1.19483C34.709 1.54986 34.1989 1.6498 33.7789 1.4398C32.6728 0.902517 31.3445 1.45386 30.9489 2.61974C30.799 3.0597 30.369 3.35474 29.8939 3.32474C28.6833 3.2413 27.6547 4.24405 27.7289 5.49467C27.7589 5.96464 27.4689 6.39467 27.0289 6.54964C25.8444 6.95511 25.3231 8.30267 25.8539 9.38464C26.0589 9.80464 25.9589 10.3196 25.6038 10.6346C24.6879 11.4361 24.6787 12.8844 25.6089 13.6997C25.9589 14.0147 26.0589 14.5247 25.8539 14.9447C25.3209 16.031 25.8499 17.381 27.0289 17.7847C27.4689 17.9346 27.759 18.3697 27.7289 18.8397C27.6557 20.0615 28.655 21.0836 29.8988 21.0098C30.3688 20.9749 30.7988 21.2698 30.9487 21.7148C31.3498 22.8863 32.6812 23.4335 33.7787 22.8899C33.7837 22.8899 33.7887 22.8848 33.7887 22.8848C34.2087 22.6848 34.7137 22.7849 35.0236 23.1398C35.825 24.0557 37.2734 24.065 38.0887 23.1348C38.3986 22.7848 38.9086 22.6848 39.3287 22.8898C40.4251 23.4329 41.7573 22.8874 42.1588 21.7147C42.3089 21.2698 42.7438 20.9797 43.2138 21.0097C44.4348 21.0835 45.4528 20.0758 45.3788 18.8396C45.3488 18.3696 45.6388 17.9346 46.0788 17.7846C47.2361 17.3884 47.803 16.0641 47.2538 14.9446C47.0489 14.5246 47.1488 14.0146 47.4988 13.7045L47.5038 13.6996C48.4233 12.8849 48.4255 11.4418 47.499 10.6297ZM36.5541 20.1317C32.1718 20.1317 28.6065 16.5577 28.6065 12.1649C28.6065 7.77205 32.1717 4.19811 36.5541 4.19811C40.9363 4.19811 44.5014 7.77205 44.5014 12.1649C44.5014 16.5577 40.9361 20.1317 36.5541 20.1317Z"
                                        fill="#2A3EC7" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_503_1478">
                                        <rect width="48" height="48" fill="white" transform="translate(0.195312 0.5)" />
                                    </clipPath>
                                </defs>
                            </svg>
                            <div>
                                <p>Today's Shipping</p>
                                <h4><?= $revenue('order_waybills','cweight',"date like '%".date('Y-m-d')."%' and awb_status!='Notpicked'")+$revenue('b2c_waybills','charged_weight',"date like '%".date('Y-m-d')."%' and status!='Notpicked'")/1000; ?> <small>Kg</small></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9 col-7 step_part">
                        <div class="premium_part">
                            <div class="order_shipment_card average_number">
                                 <div>
                                    <h5>This Week</h5>
                                    <h2><?= $revenue('order_waybills','cweight',"date between  '".$order_date1."' and '".$order_date2."' and awb_status!='Notpicked'")+$revenue('b2c_waybills','charged_weight',"date between  '".$order_date1."' and '".$order_date2."' and status!='Notpicked'")/1000; ?> <small>Kg</small></h2>
                                </div>
                                <div>
                                    <h5>This Month</h5>
                                    <h2><?= $revenue('order_waybills','cweight',"date like '%".date('Y-m')."%' and awb_status!='Notpicked'")+$revenue('b2c_waybills','charged_weight',"date like '%".date('Y-m')."%' and status!='Notpicked'")/1000; ?> <small>Kg</small></h2>
                                </div>
                                <div>
                                    <h5>Last 3 Month</h5>
                                    <h2><?php $l3= $revenue('order_waybills','cweight',"date >= now()-interval 3 month  and awb_status!='Notpicked'")+$revenue('b2c_waybills','charged_weight',"date >= now()-interval 3 month  and status!='Notpicked'")/1000;   echo round($l3/1000,2); ?> <small>Ton</small></h2>
                                </div>
                                <div>
                                    <h5>Last 6 Month</h5>
                                   <h2><?php $l6 = $revenue('order_waybills','cweight',"date >= now()-interval 3 month  and awb_status!='Notpicked'")+$revenue('b2c_waybills','charged_weight',"date >= now()-interval 3 month  and status!='Notpicked'")/1000;  echo round($l6/1000,2).'K'; ?> <small>Ton</small></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <section class="ndr_cod_number mt-5">
                <div class="container">
                    <div>
                        <h2>NDR & Exception</h2>
                    </div>
                    <div class="row ndr_number">
                        <div class="col-md-3">
                            <div class="today_order">
                                <svg width="101" height="101" viewBox="0 0 101 101" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect y="0.5" width="100.394" height="100" rx="50" fill="white" />
                                    <g clip-path="url(#clip0_2929_22371)">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M70.9148 50.5034C70.9148 52.1167 70.2937 53.7367 69.0585 54.972C67.8232 56.2073 66.2032 56.8283 64.5832 56.8283C62.9631 56.8283 61.3431 56.2073 60.1078 54.972C58.8726 53.7367 58.2583 52.1167 58.2583 50.5034C58.2583 48.8834 58.8726 47.2634 60.1078 46.0281C61.3431 44.7928 62.9631 44.1718 64.5832 44.1718C68.0797 44.1718 70.9148 47.0069 70.9148 50.5034ZM40.195 46.9056V31.6504H27.2888C26.6881 31.6504 26.1953 32.1499 26.1953 32.7507V68.2495C26.1953 68.8502 26.6881 69.3497 27.2888 69.3497H62.7944C63.3951 69.3497 63.8879 68.8502 63.8879 68.2495V61.4791C61.1271 61.3036 58.6431 60.1156 56.807 58.2796C54.8158 56.2883 53.5872 53.541 53.5872 50.5034C53.5872 47.4659 54.8158 44.7118 56.807 42.7206C58.6431 40.8845 61.1271 39.6965 63.8879 39.521V32.7507C63.8879 32.1499 63.3951 31.6504 62.7944 31.6504H49.8949V46.9056C49.8949 47.5334 49.1322 47.8304 48.7069 47.3916L45.1159 44.2798L41.3358 47.4389C40.8768 47.8169 40.195 47.4861 40.195 46.9056ZM51.0357 65.5697C51.0357 65.1849 51.3462 64.8744 51.731 64.8744H58.4608C58.8456 64.8744 59.1561 65.1849 59.1561 65.5697C59.1561 65.9544 58.8456 66.2649 58.4608 66.2649H51.731C51.3462 66.2649 51.0357 65.9544 51.0357 65.5697ZM31.6224 60.1021H40.7215C41.1063 60.1021 41.4168 60.4126 41.4168 60.7973C41.4168 61.1821 41.1063 61.4926 40.7215 61.4926H31.6224C31.2376 61.4926 30.9271 61.1821 30.9271 60.7973C30.9271 60.4126 31.2376 60.1021 31.6224 60.1021ZM30.9609 65.5697C30.9609 65.1849 31.2714 64.8744 31.6561 64.8744H40.7553C41.14 64.8744 41.4506 65.1849 41.4506 65.5697C41.4506 65.9544 41.14 66.2649 40.7553 66.2649H31.6561C31.2714 66.2649 30.9609 65.9544 30.9609 65.5697ZM48.5044 45.3869V31.6504H41.5788V45.4206L44.6433 42.8623C44.8999 42.6193 45.3049 42.6058 45.5749 42.8421L48.5044 45.3869ZM60.6343 53.4667L67.5532 46.5546C66.7229 45.9269 65.6969 45.5624 64.5832 45.5624C63.3209 45.5624 62.0586 46.0416 61.0934 47.0069C60.1281 47.9721 59.6421 49.2344 59.6421 50.5034C59.6421 51.5429 59.9728 52.5892 60.6343 53.4667ZM68.0797 53.9932C69.0382 53.028 69.5242 51.7657 69.5242 50.5034C69.5242 49.3897 69.153 48.3569 68.532 47.5334L61.6199 54.4522C62.4906 55.107 63.5369 55.4378 64.5832 55.4378C65.8454 55.4378 67.1144 54.9585 68.0797 53.9932ZM64.5832 60.1088C67.236 60.1088 69.639 59.0356 71.3805 57.294C73.122 55.5593 74.1953 53.1562 74.1953 50.5034C74.1953 47.8439 73.122 45.4409 71.3805 43.7061C69.639 41.9646 67.236 40.8913 64.5832 40.8913C61.9304 40.8913 59.5273 41.9646 57.7858 43.7061C56.051 45.4409 54.971 47.8439 54.971 50.5034C54.971 53.1562 56.051 55.5593 57.7858 57.294C59.5273 59.0356 61.9304 60.1088 64.5832 60.1088Z"
                                            fill="#5D4954" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_2929_22371">
                                            <rect width="48" height="48" fill="white"
                                                transform="translate(26.1953 26.5)" />
                                        </clipPath>
                                    </defs>
                                </svg>

                                <div class="m-auto">
                                    <p>Total NDR</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="transparent_ndr">
                                <p>Your NDR Request</p>
                                <h4><?=$ndr("1=1")?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="transparent_ndr">
                                <p>Reattempt Request</p>
                                <h4><?=$ndr("status='Pending'")?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="transparent_ndr">
                                <p>Open NDR </p>
                                <h4><?=$ndr("status='Open'")?></h4>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </section>

            <div class="card shadow mt-5">
                <div class="card-body">
                    <h4 class="card-title mb-5">Shipment Overview by Courier</h4>
                    <p>This is the total weight of shipments in <?= date('M'); ?> Month.</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Courier Name</th>
                                    <th>Manifested</th>
                                    <th>In-Transit</th>
                                    <th>Pending</th>
                                    <th>OFD</th>
                                    <th>Delivered</th>
                                    <th>RTO</th>
                                    <th>Lost</th>
                                    <th>Total Shipment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  $arr= ['delhivery','Rivigo','gati','Oxyzen','Ekart B2B','Smartr','DTDC B2B','Bluedart','Vxpress'];
                                foreach ($arr as $arr){
                                $part = $b2b("panel like '%".$arr."%'");
                                if($part>0){?>
                                <tr>
                                    <td><img src="https://truxcargo.com/assets/img/<?= explode(' ',($arr))[0];?>.png" alt="" width="80"> <b>B2B</b></td>
                                    <td><?= $b2b("panel like '%".$arr."%' and awb_status='Manifested'");?></td>
                                    <td><?= $b2b("panel like '%".$arr."%' and awb_status='In Transit'");?></td>
                                    <td><?= $b2b("panel like '%".$arr."%' and awb_status='Pending'");?></td>
                                    <td><?= $b2b("panel like '%".$arr."%' and awb_status='Dispatched'");?></td>
                                    <td><?= $b2b("panel like '%".$arr."%' and awb_status='Delivered'");?></td>
                                    <td><?= $b2b("panel like '%".$arr."%' and awb_status='RTO'");?></td>
                                    <td><?= $b2b("panel like '%".$arr."%' and awb_status='LOST'");?></td>
                                    <td><?= $part;?></td>
                                </tr>
                                <?php }}
                                 $we = $b2c("ship_with IN ('10','11','12','13','15','16','46')");
                                 if($we){?>
                                   <tr>
                                    <td><img src="https://truxcargo.com/assets/img/delhivery.png" alt="" width="80"> <b>B2C</b></td>
                                    <td><?= $b2c("ship_with IN ('10','11','12','13','15','16','46') and status='Manifested'");?></td>
                                    <td><?= $b2c("ship_with IN ('10','11','12','13','15','16','46') and status='In Transit'");?></td>
                                    <td><?= $b2c("ship_with IN ('10','11','12','13','15','16','46') and status='Pending'");?></td>
                                    <td><?= $b2c("ship_with IN ('10','11','12','13','15','16','46') and status='Dispatched'");?></td>
                                    <td><?= $b2c("ship_with IN ('10','11','12','13','15','16','46') and status='Delivered'");?></td>
                                    <td><?= $b2c("ship_with IN ('10','11','12','13','15','16','46') and status='RTO'");?></td>
                                    <td><?= $b2c("ship_with IN ('10','11','12','13','15','16','46') and status='LOST'");?></td>
                                    <td><?= $we;?></td>
                                </tr>
                                 <?php }
                                  $we = $b2c("ship_with IN ('22','23','24','25')");
                                 if($we){?>
                                   <tr>
                                    <td><img src="https://truxcargo.com/assets/img/Xpressbees.jpg" alt="" width="80"></td>
                                    <td><?= $b2c("ship_with IN ('22','23','24','25') and status='Manifested'");?></td>
                                    <td><?= $b2c("ship_with IN ('22','23','24','25') and status='In Transit'");?></td>
                                    <td><?= $b2c("ship_with IN ('22','23','24','25') and status='Pending'");?></td>
                                    <td><?= $b2c("ship_with IN ('22','23','24','25') and status='Dispatched'");?></td>
                                    <td><?= $b2c("ship_with IN ('22','23','24','25') and status='Delivered'");?></td>
                                    <td><?= $b2c("ship_with IN ('22','23','24','25') and status='RTO'");?></td>
                                    <td><?= $b2c("ship_with IN ('22','23','24','25') and status='LOST'");?></td>
                                    <td><?= $we;?></td>
                                </tr>
                                 <?php }
                                  $we = $b2c("ship_with IN ('27','28','29')");
                                 if($we){?>
                                   <tr>
                                    <td><img src="https://truxcargo.com/assets/img/amazon.png" alt="" width="80"></td>
                                    <td><?= $b2c("ship_with IN ('27','28','29') and status='Manifested'");?></td>
                                    <td><?= $b2c("ship_with IN ('27','28','29') and status='In Transit'");?></td>
                                    <td><?= $b2c("ship_with IN ('27','28','29') and status='Pending'");?></td>
                                    <td><?= $b2c("ship_with IN ('27','28','29') and status='Dispatched'");?></td>
                                    <td><?= $b2c("ship_with IN ('27','28','29') and status='Delivered'");?></td>
                                    <td><?= $b2c("ship_with IN ('27','28','29') and status='RTO'");?></td>
                                    <td><?= $b2c("ship_with IN ('27','28','29') and status='LOST'");?></td>
                                    <td><?= $we;?></td>
                                </tr>
                                 <?php }
                                  $we = $b2c("ship_with IN ('31','32','39','40')");
                                 if($we){?>
                                   <tr>
                                    <td><img src="https://truxcargo.com/assets/img/DTDC.png" alt="" width="80"></td>
                                    <td><?= $b2c("ship_with IN ('31','32','39','40') and status='Manifested'");?></td>
                                    <td><?= $b2c("ship_with IN ('31','32','39','40') and status='In Transit'");?></td>
                                    <td><?= $b2c("ship_with IN ('31','32','39','40') and status='Pending'");?></td>
                                    <td><?= $b2c("ship_with IN ('31','32','39','40') and status='Dispatched'");?></td>
                                    <td><?= $b2c("ship_with IN ('31','32','39','40') and status='Delivered'");?></td>
                                    <td><?= $b2c("ship_with IN ('31','32','39','40') and status='RTO'");?></td>
                                    <td><?= $b2c("ship_with IN ('31','32','39','40') and status='LOST'");?></td>
                                    <td><?= $we;?></td>
                                </tr>
                                 <?php }
                                  $we = $b2c("ship_with IN ('33','38','44')");
                                 if($we){?>
                                   <tr>
                                    <td><img src="https://truxcargo.com/assets/img/Ecom.png" alt="" width="80"></td>
                                    <td><?= $b2c("ship_with IN ('33','38','44') and status='Manifested'");?></td>
                                    <td><?= $b2c("ship_with IN ('33','38','44') and status='In Transit'");?></td>
                                    <td><?= $b2c("ship_with IN ('33','38','44') and status='Pending'");?></td>
                                    <td><?= $b2c("ship_with IN ('33','38','44') and status='Dispatched'");?></td>
                                    <td><?= $b2c("ship_with IN ('33','38','44') and status='Delivered'");?></td>
                                    <td><?= $b2c("ship_with IN ('33','38','44') and status='RTO'");?></td>
                                    <td><?= $b2c("ship_with IN ('33','38','44') and status='LOST'");?></td>
                                    <td><?= $we;?></td>
                                </tr>
                                 <?php } $we = $b2c("ship_with IN ('34','35','36','37')");
                                 if($we){?>
                                   <tr>
                                    <td><img src="https://truxcargo.com/assets/img/ekart.png" alt="" width="80"></td>
                                    <td><?= $b2c("ship_with IN ('34','35','36','37') and status='Manifested'");?></td>
                                    <td><?= $b2c("ship_with IN ('34','35','36','37') and status='In Transit'");?></td>
                                    <td><?= $b2c("ship_with IN ('34','35','36','37') and status='Pending'");?></td>
                                    <td><?= $b2c("ship_with IN ('34','35','36','37') and status='Dispatched'");?></td>
                                    <td><?= $b2c("ship_with IN ('34','35','36','37') and status='Delivered'");?></td>
                                    <td><?= $b2c("ship_with IN ('34','35','36','37') and status='RTO'");?></td>
                                    <td><?= $b2c("ship_with IN ('34','35','36','37') and status='LOST'");?></td>
                                    <td><?= $we;?></td>
                                </tr>
                                 <?php }  $we = $b2c("ship_with IN ('41','42','43')");
                                 if($we){?>
                                   <tr>
                                    <td><img src="https://truxcargo.com/assets/img/shree_maruti.png" alt="" width="80"></td>
                                    <td><?= $b2c("ship_with IN ('41','42','43') and status='Manifested'");?></td>
                                    <td><?= $b2c("ship_with IN ('41','42','43') and status='In Transit'");?></td>
                                    <td><?= $b2c("ship_with IN ('41','42','43') and status='Pending'");?></td>
                                    <td><?= $b2c("ship_with IN ('41','42','43') and status='Dispatched'");?></td>
                                    <td><?= $b2c("ship_with IN ('41','42','43') and status='Delivered'");?></td>
                                    <td><?= $b2c("ship_with IN ('41','42','43') and status='RTO'");?></td>
                                    <td><?= $b2c("ship_with IN ('41','42','43') and status='LOST'");?></td>
                                    <td><?= $we;?></td>
                                </tr>
                                 <?php }  $we = $b2c("ship_with IN ('45')");
                                 if($we){?>
                                   <tr>
                                    <td><img src="https://truxcargo.com/assets/img/Bluedart.png" alt="" width="80"></td>
                                    <td><?= $b2c("ship_with IN ('45') and status='Manifested'");?></td>
                                    <td><?= $b2c("ship_with IN ('45') and status='In Transit'");?></td>
                                    <td><?= $b2c("ship_with IN ('45') and status='Pending'");?></td>
                                    <td><?= $b2c("ship_with IN ('45') and status='Dispatched'");?></td>
                                    <td><?= $b2c("ship_with IN ('45') and status='Delivered'");?></td>
                                    <td><?= $b2c("ship_with IN ('45') and status='RTO'");?></td>
                                    <td><?= $b2c("ship_with IN ('45') and status='LOST'");?></td>
                                    <td><?= $we;?></td>
                                </tr>
                                 <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-6 col-sm-12">
            <div class="load-shipping mb-5">
            <div class="row mb-4">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h4 class="text-white mb-2">Performance Report</h4>
                    <p class="font-weight-500">The total number of shipments within the date range. It is the period time a user is actively engaged with our website</p>
                    <div id="sales-legend" class="chartjs-legend mt-4 mb-4"></div>
                    <canvas id="sales-chart" class="mb-4" style="width:100%"></canvas>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12">
                    <h4 class="text-white mb-4">Order Status Report</h4>
                     <?php  
                      $not_picked = $b2b("awb_status='Not Picked'") + $b2c("status='Not Picked'") ;
                    $dataPoints = array( 
                        array("y" => $not_picked,"label" => "Not Picked" ),
                        array("y" => $manifested,"label" => "Manifested" ),
                        array("y" => $transit,"label" => "In Transit" ),
                        array("y" => $pending,"label" => "Pending" ),
                        array("y" => $ofd,"label" => "OFD" ),
                        array("y" => $rto,"label" => "RTO" ),   
                        array("y" => $delivered,"label" => "Delivered" )
                    );?>
                         <script>
                             window.onload = function() {
                              var chart = new CanvasJS.Chart("chartContainer", {
                                  animationEnabled: true,
                                  title: {text: ""},
                                  subtitles: [{text: ""	}],
                                  data: [{
                                        type: "pie",
                                        yValueFormatString: "",
                                        indexLabel: "{label} ({y})",
                                        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	                          }]
                               });
                               chart.render();
                              }
                          </script>
                        <div id="chartContainer" style="height: 270px; width: 100%; border-radius: 8px;margin-bottom: 15px"></div>
                </div>
            </div>
            </div>     
        </div>
        
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="load-shipping">
                <h4 class="text-white mb-2">Load Against Shipping Partners</h4>
                <p>This is the total weight of shipments in <?= date('M');?> Month.</p>
                  <?php  $arr= ['delhivery','Rivigo','Gati','Oxyzen','Ekart B2B','Smartr','DTDC B2B','Bluedart','Vxpress'];
                                foreach ($arr as $arr){
                                $part = $partner($arr);
                                if($part>0){?>
                <div class="load_insider">
                    <img src="https://truxcargo.com/assets/img/<?= explode(' ',($arr))[0];?>.png" alt=""><b>B2B</b>
                     <p><?= $part;?> <small>Kg</small></p>
                </div>
                 <?php } }
                              $shi = $ship("'10','11','12','13','15','16'")/1000;
                              $we = round($shi,2);
                              if($we){?>
                                  <div class="load_insider">
                                      <img src="https://truxcargo.com/assets/img/delhivery.png" alt=""> <b>B2C</b>
                                     <p><?= $we;?> <small>Kg</small></p>
                                  </div>
                              <?php }
                              $shi1 = $ship("'22','23','24','25'")/1000;
                              $we1 = round($shi1,2);
                              if($we1){?>
                                    <div class="load_insider">
                                     <img src="<?= base_url('assets/images/partner_logo/xpessbees.png')?>" alt="">
                                         <p><?= $we1;?> <small>Kg</small></p>
                                    </div>
                             <?php }
                              $shi1 = $ship("'27','28','29'")/1000;
                              $we1 = round($shi1,2);
                              if($we1){?>
                                    <div class="load_insider">
                                    <img src="https://truxcargo.com/assets/img/amazon.png" alt="">
                                      <p><?= $we1;?> <small>Kg</small></p>
                                  </div>    
                              <?php }
                              $shi2 = $ship("'31','32','39','40'")/1000;
                              $we2 = round($shi2,2);
                              if($we2){?>
                                  <div class="load_insider">
                                     <img src="https://truxcargo.com/assets/img/DTDC.png" alt="">
                                     <p><?= $we2;?> <small>Kg</small></p>
                                  </div>
                             <?php }
                              $shi3 = $ship("'33','38','44'")/1000;
                              $we3 = round($shi3,2);
                              if($we3){?>
                                   <div class="load_insider">
                                    <img src="https://truxcargo.com/assets/img/Ecom.png" alt="" style="height: 30px">
                                  <p><?= $we3;?> <small>Kg</small></p>
                                 </div>
                              <?php }
                              $shi1 = $ship("'34','35','36','37'")/1000;
                              $we1 = round($shi1,2);
                              if($we1){?>
                                    <div class="load_insider">
                                     <img src="https://truxcargo.com/assets/img/ekart.png" alt="">
                                     <p><?= $we1;?> <small>Kg</small></p>
                                    </div>
                                <?php }
                              $shi1 = $ship("'41','42','43'")/1000;
                              $we1 = round($shi1,2);
                              if($we1){?>
                                    <div class="load_insider">
                                     <img src="https://truxcargo.com/assets/img/shree_maruti.png" alt="">
                                     <p><?= $we1;?> <small>Kg</small></p>
                                  </div>  
                                   <?php }
                              $shi1 = $ship("'45'")/1000;
                              $we1 = round($shi1,2);
                              if($we1){?>
                                    <div class="load_insider">
                                     <img src="https://truxcargo.com/assets/img/Bluedart.png" alt="">
                                     <p><?= $we1;?> <small>Kg</small></p>
                                  </div>  
                              <?php }?> 
            </div>
        </div>
    </div>  
     
</div>
     <?php if($ss=='gst'){?>
<div class="modal fade none-border show in" id="pp" style="display:block; background: rgb(0 0 0 / 51%);" >
    <div class="modal-dialog modal-md">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">     
                        <div class="mb-0 form-group text-center">
                            <p class="mb-3 mt-3">A valid <b>GSTIN is mandatory</b> for businesses engaged in B2B shipment movement. Therefore, if you deal with other businesses then you are required to have a GSTIN and it should be updated in your profile. </p>
                            
                           <img src="<?= base_url('assets/images/gst.jpg');?>" style="width: 100%">
                         </div>
                    </div>                       
                 </div>    
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-danger" style="margin:auto" onclick="close1();">OK</button>
             </div>            
        </div>
    </div>
</div>
<?php }?>
<div class="clearfix"></div>
 <?php if($ss=='plan'){?>
<div class="modal fade none-border show in" id="pp" style="display:block; background: rgb(0 0 0 / 51%);" >
    <div class="modal-dialog modal-md">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">     
                        <div class="mb-0 form-group text-center">
                            <a href="<?= base_url('plans');?>"">
                                <img src="<?= base_url('assets/images/new_plans.png');?>"></a>
                         </div>
                    </div>                       
                 </div>    
            </div>
            <div class="modal-footer">
                 <a class="btn btn-primary" style="margin:auto" href="<?= base_url('plans');?>"">Proceed</a>
             </div>            
        </div>
    </div>
</div>
<?php }?>
<!-- Footer -->
<div class="modal fade none-border show in" id="pp"></div>
<?php  if($ss=='wallet' && $user->wallet == 'Prepaid'){ ?>
<div class="modal fade none-border show in" id="step61" style="display:block; background: rgb(0 0 0 / 51%);" >
    <div class="modal-dialog modal-md" style="left: 13%;  top: 1%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                        <div class="mb-0 form-group text-center">
                            <img src="<?= base_url('assets/images/insufficient.jpg');?>" height="260">
                         </div>
                        <h4 class="text-center bold" style="color:red">Negative Wallet</h4>
                        <p class="text-center m-0">You do not have enough balance in your wallet to ship hassle-free shipments.</p>
                        
                    </div>                       
                 </div>    
            </div>
            <div class="modal-footer">
                <a class="btn btn-success rounded-pill m-auto mb-2" href="<?= base_url('wallet/')?>">Recharge Now &nbsp;<i class="fa fa-arrow-right"></i> </a>
             </div>            
        </div><div class="nxt_arrow61"></div>
    </div>
</div>
<?php } else if($ss=='new'){?>
<!--------- Panel Tour---------------->
<div class="modal fade none-border show in" id="step" style="display:block; background: rgb(0 0 0 / 51%);" >
    <div class="modal-dialog modal-md profile-step">
        <div class="modal-content ">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                        <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                        <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center"> 
                        <h4 class=" bold">Welcome To <?= $wconfig->company; ?></h4>
                        <p class="m-0">We will take you through our panel, this will not take more than a minute.</p>
                        <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step1()">Let's Go &nbsp;<i class="fa fa-arrow-right"></i> </button>
                        </div>
                        </div>                       
                 </div>    
            </div>                       
        </div>
    </div>
</div>
<?php }?>
<div class="modal fade none-border" id="step1" >
    <div class="modal-dialog modal-md profile-step" style="left: 23%; top: 6%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                         <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step1.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center"> 
                        <h4 class="text-center bold">Your Profile</h4>
                        <p class="text-center m-0">You can view your account details here.</p>
                         <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step2()">Next &nbsp;<i class="fa fa-arrow-right"></i> </button>
                        </div>
                    </div>                       
                 </div>    
            </div>                        
        </div><div class="nxt_arrow1"></div>
    </div>
</div>
<div class="modal fade none-border" id="step2" >
    <div class="modal-dialog modal-md profile-step" style="left: 24%;  top: 1%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                          <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step2.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center"> 
                        <h4 class="text-center bold">Pickup Points</h4>
                        <p class="text-center m-0">You can add multiple pickup point/warehouse from here.</p>
                         <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step3()">Next &nbsp;<i class="fa fa-arrow-right"></i> </button>
                        </div>
                    </div>                       
                 </div>    
            </div>                      
        </div><div class="nxt_arrow2"></div>
    </div>
</div>
<div class="modal fade none-border" id="step3" >
    <div class="modal-dialog modal-md profile-step" style="left: 16%;  top: 9%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                        <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step3.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center"> 
                        <h4 class="text-center bold">Create Your Order</h4>
                        <p class="text-center m-0">You can create big or small package within a minute.</p>
                        <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step4()">Next &nbsp;<i class="fa fa-arrow-right"></i> </button>
                        </div>
                        </div>                       
                 </div>    
            </div>
            </div><div class="nxt_arrow3"></div>
    </div>
</div>
<div class="modal fade none-border" id="step4" >
    <div class="modal-dialog modal-md profile-step" style="right: 17%;  top: 10%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                          <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step4.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center"> 
                        <h4 class="text-center bold">Pickup Request</h4>
                        <p class="text-center m-0">Once the order has been created, you need to create pickup request here</p>
                        <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step5()">Next &nbsp;<i class="fa fa-arrow-right"></i> </button>
                        </div>
                    </div>                       
                 </div>    
            </div>            
        </div><div class="nxt_arrow4"></div>
    </div>
</div>
<div class="modal fade none-border" id="step5" >
    <div class="modal-dialog modal-md profile-step" style="right: 17%;  top: 4%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                          <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step5.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center"> 
                        <h4 class="text-center bold">Your Orders</h4>
                        <p class="text-center m-0">You can see and track all your order at one page</p>
                         <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step6()">Next &nbsp;<i class="fa fa-arrow-right"></i> </button>
                        </div>
                    </div>                       
                 </div>    
            </div>                  
        </div><div class="nxt_arrow5"></div>
    </div>
</div>
<div class="modal fade none-border" id="step6" >
    <div class="modal-dialog modal-md profile-step" style="right: 11%;  top: 11%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                         <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step6.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center">
                        <h4 class="text-center bold"> Search Your Order</h4>
                        <p class="text-center m-0">You can search your LR/AWB No and get all details with current status.</p>
                          <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step61()">Next &nbsp;<i class="fa fa-arrow-right"></i> </button>
                        </div>   
                    </div>                       
                 </div>    
            </div>
                       
        </div><div class="nxt_arrow6"></div>
    </div>
</div>
<div class="modal fade none-border" id="step61" >
    <div class="modal-dialog modal-md profile-step" style="left: 7%;  top: 6%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                         <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/recharge.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center">
                        <h4 class="text-center bold"> Wallet</h4>
                        <p class="text-center m-0">Recharge your wallet and view your deductions , refunds and recharges history.</p>                        
                            <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step7()">Next &nbsp;<i class="fa fa-arrow-right"></i> </button>
             </div> 
                        </div>                       
                 </div>    
            </div>                       
        </div><div class="nxt_arrow61"></div>
    </div>
</div>
<div class="modal fade none-border" id="step7" >
    <div class="modal-dialog modal-md profile-step" style="right: 17%;  top: 20%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                        <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step7.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center">
                        <h4 class="text-center bold"> Remittance</h4>
                        <p class="text-center m-0">View your upcoming and previous remittance payments here.</p>   
                         <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step8()">Next &nbsp;<i class="fa fa-arrow-right"></i> </button>
             </div> 
                    </div>                       
                 </div>    
            </div>
                     
        </div><div class="nxt_arrow7"></div>
    </div>
</div>
<div class="modal fade none-border" id="step8" >
    <div class="modal-dialog modal-md profile-step" style="right: 17%;  top: 9%">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                       <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step8.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center">  <h4 class="text-center bold"> Billing</h4>
                        <p class="text-center m-0">View your billing invoice twice in a month all delivered shipments.</p>       
                         <button class="btn btn-primary btn-login mb-2 mt-3" type="button" onclick="step9()">Next &nbsp;<i class="fa fa-arrow-right"></i> </button>
             </div>  
                    </div>                       
                 </div>    
            </div>
                         
        </div><div class="nxt_arrow8"></div>
    </div>
</div>
<div class="modal fade none-border" id="step9" >
    <div class="modal-dialog modal-md profile-step">
        <div class="modal-content">
              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">  
                         <button type="button" class="btn skip pull-right" style="margin:auto" onclick="close1();">Skip <i class="fa fa-angle-double-right"></i></button>
                         <div class="mb-1 pd-7 form-group text-center">
                            <img src="<?= base_url('assets/images/step9.png');?>" class="w-50">
                         </div>
                        <div class="col-lg-7 text-center">
                        <h4 class="text-center bold">Before you start</h4>
                        <p class="text-center m-0">Recharge your wallet and start creating orders without any problem</p>
                        <a href="<?= base_url('wallet/')?>" class="btn btn-primary btn-login mb-2 mt-3">Recharge Now</a>
                <button type="button" class="btn btn-danger mb-2 mt-3"  onclick="close1();">Later</button>
             </div> 
                    </div>                       
                 </div>    
            </div>                   
        </div> 
    </div>
</div>
  <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
     <script>  
        function backdrop() {
          if ($(".modal-backdrop").length > -1) {
            $(".modal-backdrop").not(':first').remove();
          }
        }         
        function step1(){ $('#step').toggle();  $('#step1').modal('show'); backdrop();}
        function step2(){ $('#step1').toggle();  $('#step2').modal('show');backdrop();}
        function step3(){ $('#step2').toggle();  $('#step3').modal('show'); backdrop();}
        function step4(){ $('#step3').toggle();  $('#step4').modal('show');backdrop();}
        function step5(){ $('#step4').toggle();  $('#step5').modal('show');backdrop();}
        function step6(){ $('#step5').toggle();  $('#step6').modal('show');backdrop();}
        function step61(){ $('#step6').toggle();  $('#step61').modal('show');backdrop();}
        function step7(){ $('#step61').toggle();  $('#step7').modal('show');backdrop();}
        function step8(){ $('#step7').toggle();  $('#step8').modal('show');backdrop();}
        function step9(){ $('#step8').toggle();  $('#step9').modal('show');backdrop();}
        
     </script>
          
    <?php for ($i = 0; $i < 5; $i++) {
            $months[] = date("M-y", strtotime( date( 'Y-m-01' )." -$i months"));
            $date = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
            $b = $b2b("date like '%".$date."%'");
            $c = $b2c("date like '%".$date."%'");
            $t[] = $b + $c;
            $pb2b[] = $b;
            $pb2c[] = $c;
          } 
          $number = ceil(max($t)/ 10) * 10;
           ?>
    <script>
       (function($){
        'use strict';
        $(function() {
     if ($("#sales-chart").length) {
      var SalesChartCanvas = $("#sales-chart").get(0).getContext("2d");
      var SalesChart = new Chart(SalesChartCanvas, {
        type: 'bar',
        data: {
          labels: ["<?= $months[4];?>", "<?= $months[3];?>", "<?= $months[2];?>", "<?= $months[1];?>", "<?= $months[0];?>"],
          datasets: [{
              label: 'B2B Shipments',
              data: [<?= $pb2b[4];?>, <?= $pb2b[3];?>, <?= $pb2b[2];?>, <?= $pb2b[1];?>, <?= $pb2b[0];?>],
              backgroundColor: '#98BDFF'
            },
            {
              label: 'B2C Shipments',
              data: [<?= $pb2c[4];?>, <?= $pb2c[3];?>, <?= $pb2c[2];?>, <?= $pb2c[1];?>, <?= $pb2c[0];?>],
              backgroundColor: '#FFF'
            }
          ]
        },
        options: {
          cornerRadius: 10,
          responsive: true,
          maintainAspectRatio: true,
          layout: {
            padding: {
              left: 0,
              right: 0,
              top: 0,
              bottom: 0
            }
          },
          scales: {
            yAxes: [{
              display: true,
              gridLines: {
                display: true,
                drawBorder: false,
                color: "#FFF"
              },
              ticks: {
                display: true,
                min: 0,
                max: <?= $number;?>,
                callback: function(value, index, values) {
                  return  value ;
                },
                autoSkip: true,
                maxTicksLimit: 10,
                fontColor:"#FFF"
              }
            }],
            xAxes: [{
              stacked: false,
              ticks: {
                beginAtZero: true,
                fontColor: "#FFF"
              },
              gridLines: {
                color: "#fff",
                display: false
              },
              barPercentage: 1
            }]
          },
          legend: {
            display: false
          },
          elements: {
            point: {
              radius: 0
            }
          }
        }
      });
      document.getElementById('sales-legend').innerHTML = SalesChart.generateLegend();
    }  });
      })(jQuery);
   
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000/8));
        let expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    } 
    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {   c = c.substring(1);  }
            if (c.indexOf(name) == 0) {  return c.substring(name.length, c.length);  }
        }
        return "";
    }
    function checkCookie() {
        let Notify = getCookie("Notify");
        if (Notify!= "YES") {
            pushNotify();
            setCookie("Notify", "YES", 1);
        }
    }
    checkCookie();
     
    function pushNotify() {
        if (!("Notification" in window)) {
            alert("Web browser does not support desktop notification");
        }
        if (Notification.permission !== "granted")
              Notification.requestPermission();
        else {
            $.ajax({
                url: "<?php echo base_url() ?>index.php/notification",
                type: "POST",
                data:{[csrfName]: csrfHash},
                success: function(data, textStatus, jqXHR) {
                    if ($.trim(data)) {
                        var data = jQuery.parseJSON(data);
                        notification = createNotification(data.title, data.icon, data.body, data.url,data.image,);
                        setTimeout(function() {notification.close();}, 10000);
        	    }
                },
                error: function(jqXHR, textStatus, errorThrown) { }
            });
        }
    };

    function createNotification(title, icon, body, url,image) {
        var notification = new Notification(title, {
          icon: icon,
          image: image,
          body: body
        });
        notification.onclick = function() {window.open(url);};
        return notification;
    }
    </script>     
    
