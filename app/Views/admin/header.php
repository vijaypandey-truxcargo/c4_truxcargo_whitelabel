    <!DOCTYPE HTML>
<html>
<head>
    <title><?php if(isset($page_title)){ echo $page_title;} else {echo 'Admin Panel';}?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.png');?>">
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

    <?= link_tag('backend/css/bootstrap.min.css')?>
    <?= link_tag('backend/css/style.css')?>
    <?= link_tag('backend/css/lines.css')?>
    <?= link_tag('backend/css/font-awesome.css')?>
    <?= link_tag('backend/css/custom.css')?>
    <?= link_tag('backend/css/clndr.css')?>
<script src="<?= base_url('backend/js/jquery.min.js');?>"></script>
<script src="<?= base_url('backend/js/metisMenu.min.js');?>"></script>
<script src="<?= base_url('backend/js/custom.js');?>"></script>
<script src="<?= base_url('backend/js/d3.v3.js');?>"></script>
<script src="<?= base_url('backend/js/rickshaw.js');?>"></script>
<script src="<?= base_url('backend/js/clndr.js');?>"></script>
<script src="<?= base_url('backend/js/bootstrap.min.js');?>"></script>
<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
 <link rel="stylesheet" href="<?=base_url('assets/css/print.css')?>" media="print">

<script language="javascript">
    
function checkInput(ob) {
  var invalidChars = /[^0-9.]/gi;
  if(invalidChars.test(ob.value)) {
            ob.value = ob.value.replace(invalidChars,"");
      }
  }
  var csrfName = '<?= csrf_token() ?>'; var csrfHash = '<?= csrf_hash() ?>';
  document.addEventListener("contextmenu", function(e){
    e.preventDefault();
  }, false);
  document.onkeydown = function(e) {
    if (e.ctrlKey && e.keyCode === 85) {
      return false;
    }
  };
</script>
<script type="text/javascript" src="<?= base_url('backend/ckeditor/ckeditor.js');?>"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    #example_filter, #example_paginate, #example2_filter, #example2_paginate{display: none}
    #example1_filter, #example1_paginate,#example1_wrapper table,#example1_info{display: none}
</style>
<link rel="stylesheet" href="<?=base_url('assets/css/print.css')?>" media="print">
<style>
    .select2-container { width: 100% !important;   max-width: 100% !important;}
    .select2-container .select2-selection--single {height: 34px !important;}
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 33px !important;}
 </style> 
</head>  
<body>
<div id="wrapper">  
    <nav class="top1 navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?= base_url('admin/dashboard');?>" class="navbar-brand"> <img src="<?= base_url("uploads/profile/{$wconfig->logo}");?>"></a>
          </div>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle avatar" data-toggle="dropdown">
                    <img src="<?= base_url('backend/images/pic2.png'); ?>">
                    <?= session()->get('admin_user')->userName ?? '' ?>
                    <img src="<?= base_url('backend/images/dots.png'); ?>" style="height: 21px; margin-left: 8px;">
                </a>
	        <ul class="dropdown-menu">
                    <li class="m_2"><?= anchor('admin/dashboard/changepassword','<i class="fa fa-wrench"></i> Change Password'); ?></li>
		    <li class="m_2"><?= anchor('admin/logout','<i class="fa fa-lock"></i> Logout'); ?></li>
	        </ul>
            </li>
 	    </ul>

        <div class="col-lg-6 mt-1" style="margin-left: 31px;">
           <?php
           
            $user_id = session()->get('user_id');
            
           // if($user_id == 30) {  
            ?>
            <form action="<?= base_url('admin/dashboard/summary');?>" method="post" accept-charset="utf-8">
                <input type="hidden" 
                       name="<?= csrf_token() ?>" 
                       value="<?= csrf_hash() ?>" />
            
                <div class="d-flex">
                    <p class="input-group">
                        <input type="text" class="form-control" name="lrn" placeholder="Search by Tracking No." required="">
                    </p>
                    <p class="input-group">
                        <input type="submit" name="apply" value="Get Summary" class="btn btn-warning">
                    </p>
                </div>
            </form>  
            <?php //} ?>
            
        </div>

        <?php  if(in_array("View Support", $GLOBALS['permission'])){?>
        <a href="<?= base_url('admin/support/ticket');?>" class="notification">
            <span><i class="fa fa-bell"></i></span>
            <span class="badge"><?= session()->get('notify'); ?></span>
        </a>
        <?php }?>

	<?php $permission =  $GLOBALS['permission'];?>
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li> <?= anchor('admin/dashboard','<i class="fa fa-dashboard nav_icon"></i>Dashboard'); ?></li>
                     <?php if(in_array("config", $GLOBALS['permission'])){ ?>
                      <li> <?= anchor('admin/dashboard/config','<i class="fa fa-cogs nav_icon"></i>Configuration'); ?></li>
                      <li> <?= anchor('admin/dashboard/plans','<i class="fa fa-cogs nav_icon"></i>Shipping Plans'); ?></li>
                     <?php }?>
                     <?php if(in_array("Top Users", $GLOBALS['permission'])){ ?>
                      <!--<li> <?= anchor('admin/dashboard/top_ten','<i class="fa fa-trophy nav_icon"></i>Top 10'); ?></li>-->
                     <?php }?>
                   
                    <li> <a href="#"><i class="fa fa-sitemap nav_icon"></i>Admin User<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><?= anchor('admin/users/add',' &raquo; Add User'); ?></li>
                            <li><?= anchor('admin/users/',' &raquo; List User'); ?></li>
                        </ul>
                    </li>

                    <li> <a href="#"><i class="fa fa-sitemap nav_icon"></i>Master<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php if(in_array("View Company", $permission)) {?>
                                <li><?= anchor('admin/company',' &raquo; Company'); ?></li>
                            <?php } ?>
                            <?php if(in_array("View Hub", $permission)) {?>
                                <li><?= anchor('admin/master/hub_masters',' &raquo; HUB'); ?></li>
                            <?php } ?>
                            <?php if(in_array("View Role", $permission)) {?>
                                <li><?= anchor('admin/role',' &raquo; Role'); ?></li>
                            <?php } ?>
                            <?php if(in_array("View Role", $permission)) {?>
                                 <li><?= anchor('admin/kycType',' &raquo; Kyc Type'); ?></li>
                            <?php } ?>
                           <?php if(in_array("View Role", $permission)) {?>
                                <li><?= anchor('admin/state',' &raquo; State'); ?></li>
                           <?php } ?>
                            <?php if(in_array("View Vendor", $permission)) {?>
                                <li><?= anchor('admin/vendor',' &raquo; Vendor'); ?></li>
                            <?php } ?>
                            <?php if(in_array("View Mode", $permission)) {?>
                                <li><?= anchor('admin/mode',' &raquo; Mode'); ?></li>
                            <?php } ?>
                            <?php if(in_array("View Service", $permission)) {?>
                                <li><?= anchor('admin/service',' &raquo; Service'); ?></li>
                            <?php } ?>
                            <?php if(in_array("View charge", $permission)) {?>
                                <li><?= anchor('admin/charge',' &raquo; Charge'); ?></li>
                            <?php } ?>
                            <?php if(in_array("View Rate Modifier", $permission)) {?>
                                 <li><?= anchor('admin/rateModifier',' &raquo; Rate Modifer'); ?></li>
                            <?php } ?>
                            <?php if(in_array("View Customer Service", $permission)) {?>
                                <li><?= anchor('admin/CustomerServiceCharge',' &raquo; Customer Service Charge'); ?></li>
                           <?php } ?>
                            <?php if(in_array("View Exception Master", $permission)) {?>
                                <li><?= anchor('admin/exceptionMaster',' &raquo; Exception Master'); ?></li>
                            <?php } ?>
                        </ul>
                    </li>

                   <?php if(in_array("View Hierarchy", $permission)) {?>
                    <!--<li> <a href="#"><i class="fa fa-sitemap nav_icon"></i>Team Hierarchy<span class="fa arrow"></span></a>-->
                    <!--    <ul class="nav nav-second-level">-->
                    <!--        <li><?= anchor('admin/hierarchy/',' &raquo; Team Hierarchy'); ?></li>-->
                    <!--       <?php if(in_array("Add Hierarchy", $permission)) {?> <li><?= anchor('admin/hierarchy/add',' &raquo; Add Team Hierarchy'); ?></li><?php }?>-->
                    <!--    </ul>-->
                    <!--</li>-->

                    <?php } if(in_array("View Pincode", $permission)) {?>
                    <li> <a href="#"><i class="fa fa-map-marker nav_icon"></i>Pincodes<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                           <!-- <li> <?= anchor('admin/support/pincodes','&raquo; Delhivery Pincodes'); ?></li> -->
                            <!-- <li> <?= anchor('admin/support/pincodes_rivigo','&raquo; Rivigo Pincodes'); ?></li> -->
                            <!-- <li> <?= anchor('admin/support/oxygen','&raquo; Oxygen Pincodes'); ?></li> -->
                            <!-- <li> <?= anchor('admin/support/ekart','&raquo; Ekart B2B'); ?></li> -->
                            <!-- <li> <?= anchor('admin/support/amazon_pins','&raquo; Amazon Pincodes'); ?></li> -->
                            <!-- <li> <?= anchor('admin/pincode/dtdc','&raquo; DTDC B2B/B2C Pincodes'); ?></li> -->
                            <!-- <li> <?= anchor('admin/pincode/ekartb2c','&raquo; Ekart B2C '); ?></li> -->
                            <!-- <li> <?= anchor('admin/pincode/ekart10','&raquo; Ekart 10Kg B2C '); ?></li> -->
                            <!-- <li> <?= anchor('admin/pincode/bluedart','&raquo; BlueDart Pincodes '); ?></li> -->
                            <!-- <li> <?= anchor('admin/pincode/vxpress','&raquo; Vxpress Pincodes '); ?></li> -->
                            <!-- <li> <?= anchor('admin/pincode/ecom','&raquo; Ecom Pincodes '); ?></li> -->
                            <!-- <li> <?= anchor('admin/pincode/bluedart_b2c','&raquo; Bluedart B2C Pincodes'); ?></li> -->
                            <li> <?= anchor('admin/pincode/criticalLog','&raquo; Criticallog Pincodes'); ?></li>
                            <li> <?= anchor('admin/pincode/bluedart_surface','&raquo; BlueDart Surface Pincodes'); ?></li>
                            <li> <?= anchor('admin/pincode/bluedart_air','&raquo; BlueDart Air Pincodes'); ?></li>
                            <li> <?= anchor('admin/pincode/bluedart_dP','&raquo; BlueDart DP Pincodes'); ?></li>
                            <li> <?= anchor('admin/pincode/delhivery_pincode','&raquo; Delhivery Pincodes'); ?></li>
                            <li> <?= anchor('admin/pincode/xpressbees_pincode','&raquo; Xpressbees Pincodes'); ?></li>
                            <li> <?= anchor('admin/pincode/sales_pincode','&raquo; Sales Pincodes'); ?></li>
                        </ul>
                    </li>
                    <?php } if(in_array("View Customer", $permission)) {?>
                    <li> <a href="#"><i class="fa fa-users nav_icon"></i>Customer<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <?= anchor('admin/customer/create','&raquo; Add Customer'); ?></li>
                            <li> <?= anchor('admin/customer/all/all','&raquo; All Customers'); ?></li>
                            <li> <?= anchor('admin/customer/bulk_client','&raquo; Upload Bulk Customers'); ?></li>
                            <li> <?= anchor('admin/dashboard/active_user/active','&raquo; Active Customers'); ?></li>
                            <!--<li> <?= anchor('admin/dashboard/active_user/rare','&raquo; Rare Customers'); ?></li>-->
                            <!--<li> <?= anchor('admin/dashboard/active_user/dead','&raquo; Lead Customers'); ?></li>-->
                            <li> <?= anchor('admin/customer/subscription/all','&raquo; Plan Subscription'); ?></li>
                            <?php if(in_array("Bank", $permission)) {?> <li> <?= anchor('admin/customer/bank/all','&raquo; Bank Info'); ?></li> <?php }?>
                            <?php if(in_array("Wallet Info", $permission)) {?> <li> <?= anchor('admin/customer/wallet/all','&raquo; Wallet Info'); ?></li> <?php }?>

                        </ul>
                    </li>
                    <?php } if(in_array("Pickup", $permission) || in_array("View Warehouse", $permission)) {?>
                    <li> <a href="#"><i class="fa fa-hand-o-up nav_icon"></i>Pickups & Warehouse<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                             <li> <?= anchor('admin/order/bluedart/all','&raquo; Bluedart Pickup'); ?></li>
                            <?php if(in_array("B2B Pickup", $permission)) {?> <li> <?= anchor('admin/order/pickup/all','&raquo; B2B Pickup'); ?></li> <?php }?>
                            <!--<?php if(in_array("B2C Pickup", $permission)) {?> <li> <?= anchor('admin/orderb2c/pickup/all','&raquo; B2C Pickup'); ?></li> <?php }?>-->
                            <?php if(in_array("Add Pickup", $permission)) {?>
                              <li> <?= anchor('admin/order/add_pickup','&raquo; Add Pickup'); ?></li>
                              <li> <?= anchor('admin/activity/penalty','&raquo; Pickup Penalty'); ?></li>
                            <?php }?>
                            <?php if(in_array("View Warehouse", $permission)) {?> <li><?= anchor('admin/order/warehouse/all','&raquo; All Warehouse'); ?></li><?php }?>
                        </ul>
                    </li> 
                    <?php }if(in_array("View LR Number", $permission)) {?>
                    <!--<li> <a href="#"><i class="fa fa-align-justify nav_icon"></i>LR Series<span class="fa arrow"></span></a>-->
                    <!--    <ul class="nav nav-second-level">-->
                    <!--        <li> <?= anchor('admin/lrnumber','&raquo; Delhivery LR No.'); ?></li>-->
                    <!--        <li> <?= anchor('admin/lrnumber/gati_lr','&raquo; Gati LR No.'); ?></li>-->
                    <!--        <li> <?= anchor('admin/lrnumber/gati_waybill','&raquo; Gati Waybill No.'); ?></li>-->
                    <!--        <li> <?= anchor('admin/lrnumber/dtdc','&raquo; DTDC LR No.'); ?></li>-->
                    <!--        <li> <?= anchor('admin/lrnumber/oxygen','&raquo; Oxygen LR No.'); ?></li>-->
                    <!--        <li> <?= anchor('admin/pincode/bluedart_series','&raquo; BlueDart Series '); ?></li>-->
                    <!--        <li><?= anchor('admin/matrix/india_post_series',' &raquo; India Post Series'); ?></li>--> 
                    <!--    </ul>-->
                    <!--</li>-->
                    <?php } if(in_array("B2B Order", $permission)) {?>  
                     <li> <?= anchor('admin/ticketingNdr/','<i class="fa fa-eye nav_icon"></i>All Ticket EDD'); ?></li>
                      <li> <?= anchor('admin/TrackingExceptionReport/','<i class="fa fa-eye nav_icon"></i>NDR'); ?></li>
                      <li> <a href="#"><i class="fa fa-truck nav_icon"></i>B2B Panel<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level"> 
                          <?php if(in_array("B2B Order", $permission)) {?>  <li><?= anchor('admin/order/index/all',' &raquo; All Order'); ?></li> <?php }?>
                            <?php if(in_array("B2B MIS", $permission)) {?>  <li><?= anchor('admin/order/mis/all',' &raquo; MIS Report'); ?></li>   <?php }?>  
                            <?php if(in_array("B2B MIS", $permission)) {?>  <li><?= anchor('admin/order/pod/all',' &raquo; B2B POD'); ?></li>   <?php }?>  
                            
                            <?php  if(in_array("Payment Mode", $permission)) {?>   <li><?= anchor('admin/order/payment_mode',' &raquo; Change Payment Mode'); ?></li> <?php }?>
                            <!--<?php if(in_array("Non Listed LRs", $permission)) {?>  <li><?= anchor('admin/order/non_listed',' &raquo; Non Listed LRs'); ?></li> <?php }?>-->
                            <li><?= anchor('admin/order/add_order_client/',' &raquo; Add order'); ?></li>
                            <li><?= anchor('admin/order/bulk_lr/',' &raquo; Bulk LR Entry'); ?></li>
                            <!--<li><?= anchor('admin/order/challan/',' &raquo; Challan Without Manifest'); ?></li>-->
                             <!--<li><?= anchor('admin/info/appointment/',' &raquo; Delhivery Appointment'); ?></li>-->
                            <!--<li><?= anchor('admin/truxcargo/waybill/',' &raquo; Waybill Copy'); ?></li>-->
                            <!--<li><?= anchor('admin/truxcargo/challan/',' &raquo; Delivery Challan'); ?></li>-->
                            <!--<li><?= anchor('admin/truxcargo/documents/',' &raquo; Manage Documents'); ?></li>-->
                            <?php if(in_array("B2B Activity", $permission)) {?>
                               <!--<li><?= anchor('admin/activity/transfer/',' &raquo; LR Transfer'); ?></li>-->
                               <!--<li><?= anchor('admin/activity/fix/',' &raquo; Fix Weight'); ?></li>-->
                             <?php }if(in_array("Tech", $permission)) {?>
                             <!--<li> <?= anchor('admin/order/not_picked','&raquo; Not Picked'); ?></li>-->
                             <!--<li> <?= anchor('admin/order/new_address','&raquo; Update Address'); ?></li>-->
                             <!--<li> <?= anchor('admin/remittance/profit_check','&raquo; Profit Check'); ?></li>-->
                             <?php }?>
                            <!--<li><?= anchor('admin/order/dtdc/',' &raquo; DTDC LRS'); ?></li>-->
                            <!--<li> <?= anchor('admin/order/waybill','&raquo; Find LR Against Waybill'); ?></li>-->
                        </ul>
                    </li>
                    <?php } if(in_array("B2C Order", $permission)) {?>
                    <!--<li> <a href="#"><i class="fa fa-truck nav_icon"></i>B2C Panel<span class="fa arrow"></span></a>-->
                    <!--    <ul class="nav nav-second-level">-->
                    <!--         <?php if(in_array("B2C Order", $permission)) {?>   <li><?= anchor('admin/orderb2c/index/all',' &raquo; All Order'); ?></li><?php  }?>-->
                    <!--        <?php if(in_array("B2C MIS", $permission)) {?>     <li><?= anchor('admin/orderb2c/mis/all',' &raquo; MIS Report'); ?></li>   <?php  }?>-->
                    <!--        <?php  if(in_array("Payment Mode", $permission)) {?>   <li><?= anchor('admin/orderb2c/payment_mode',' &raquo; Change Payment Mode'); ?></li> <?php }?>-->
                    <!--           <?php if(in_array("Tech", $permission)) {?>-->
                    <!--         <li> <?= anchor('admin/orderb2c/not_picked','&raquo; Not Picked'); ?></li>-->
                    <!--         <?php }?>-->
                    <!--    </ul>-->
                    <!--</li>-->

                    <?php } if(in_array("View Weight", $permission)) {?>
                    <li> <a href="#"><i class="fa fa-wrench nav_icon"></i>Weight Reconciliation<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><?= anchor('admin/weight/index/all',' &raquo; Weight Reconciliation'); ?></li>
                            <li><?= anchor('admin/weight/raised/',' &raquo; Disputes Raised'); ?></li>
                            <li><?= anchor('admin/weight/settled/',' &raquo; Disputes Settled'); ?></li>
                            <li><?= anchor('admin/weight/reopen_dispute/',' &raquo; Reopen Disputes'); ?></li>
                            <!--<li><?= anchor('admin/weight/b2c_settlement/',' &raquo; B2C Weight Reconciliation'); ?></li>-->
                            <!--<li><?= anchor('admin/weight/delhivery_charges/',' &raquo; Delhivery Charges'); ?></li>-->
                            <!--<li><?= anchor('admin/weight/topay_cases/',' &raquo; Recon. Topay Amt'); ?></li>-->
                            <!--<li><?= anchor('admin/weight/mode_change/all',' &raquo; MOP Affected'); ?></li>-->
                        </ul>
                    </li>

                    <?php } if(in_array("View Billing", $permission) || in_array("Profit & Loss", $permission) || in_array("View Remittance", $permission) || in_array("View Wallet", $permission)) {?>
                    <li> <a href="#"><i class="fa fa-folder nav_icon"></i>Accounts<span class="fa arrow"></span><?php if(session()->get('notify_cod') > 0){ echo '<i class="fa fa-bell red" style="background: #fff; padding: 3px 6px; border-radius: 12px;margin-left: 12px;"> </i>'; }?></a>
                     <ul class="nav nav-second-level">
                           <?php  if(in_array("View Wallet", $permission)) {?>
                           <li> <?= anchor('admin/wallet/index/all','<i class="fa fa-inr nav_icon"></i>E-Wallet'); ?></li>
                           <?php } if(in_array("View Billing", $permission)){?>
                        <li> <?= anchor('admin/billing/outstanding','<i class="fa fa-trophy nav_icon"></i>Top Outstanding '); ?></li>
                        <li> <?= anchor('admin/invoice/sale/all','<i class="fa fa-table nav_icon"></i> Overall Sale'); ?></li>

                        <li> <a href="#"><i class="fa fa-folder nav_icon"></i>Billing<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                           <li> <?= anchor('admin/invoice/b2b/all','&raquo; B2B Billing'); ?> </li>
                             <!--<li> <?= anchor('admin/invoice/third_party/all','&raquo; Third Party Billing'); ?></li>-->
                             <!--<li> <?= anchor('admin/invoice/memo/all','&raquo; B2B Memo'); ?></li>-->
                             <!--<li> <?= anchor('admin/invoice/b2c/all','&raquo; B2C Billing'); ?></li>-->
                             <li> <?= anchor('admin/cn/debit_note/all','&raquo; Debit Note'); ?></li>
                             <li> <?= anchor('admin/cn/index/all','&raquo; Credit Note'); ?></li>
                             <!--<li> <?= anchor('admin/cn/cn_pro/all','&raquo; CN Processing'); ?></li>-->
                             <!--<li> <?= anchor('admin/invoice/topay_collection/all','&raquo; Topay Collection'); ?></li>-->
                           <!--<li> <?= anchor('admin/invoice/online','<i class="fa fa-credit-card nav_icon"></i>Online Payment '); ?></li>-->
                           <!--<li> <?= anchor('admin/delhivery/check_bill','<i class="fa fa-edit nav_icon"></i>Invoice Against LR'); ?></li>-->
                           <!--<li> <?= anchor('admin/delhivery/','<i class="fa fa-edit nav_icon"></i>Delhivery Billing  '); ?></li>-->
                        </ul>
                     </li>
                      <?php } if(in_array("View Remittance", $permission)) {?>
                        <li> <a href="#"><i class="fa fa-bookmark nav_icon"></i>Remittance<span class="fa arrow"></span></a>
                          <ul class="nav nav-third-level">
                            <li><?= anchor('admin/remittance/index/all',' &raquo; COD Remittance'); ?></li>
                            <!--<li><?= anchor('admin/remittance/franchise/all',' &raquo; Franchise Remittance'); ?></li>-->
                            <!--<li><?= anchor('admin/remittance/cheque/all',' &raquo; Cheque Remittance'); ?></li>-->
                            <?php if(in_array("Tech", $permission)) {?>
                             <li> <?= anchor('admin/remittance/cod_calc','&raquo; COD Calc'); ?></li>
                             <?php }?>
                            <li><?= anchor('admin/remittance/cod_lrs','&raquo; Early COD LRs'); ?></li>
                            <?php if(in_array("Add Remittance", $permission)) {?>
                              <!--<li>  <a href="<?= base_url('admin/remittance/cod_request','');?>"> &raquo; Early COD Request <?php if(session()->get('notify_cod') > 0) { echo '<span class="red" style="background: #fff; font-weight: 700; padding: 3px 6px; border-radius: 12px;margin-left: 12px;"> '.session()->get('notify_cod').'</span>'; }?></a> </li>-->
                              <li><?= anchor('admin/remittance/remittance_update','&raquo; Remittance & Wallet Update'); ?></li>
                              <!--<li><?= anchor('admin/remittance/reconciliation','&raquo; COD Reconciliation'); ?></li>-->
                             <?php }?>
                          </ul>
                        </li>
                        <?php }if(in_array("Profit & Loss", $permission)) {?>
                        <li> <a href="#"><i class="fa fa-inr nav_icon"></i>Profit & Loss<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                           <li> <?= anchor('admin/profit/index/all','&raquo; Overall P&L'); ?></li>
                            <li> <?= anchor('admin/profit/b2b/all','&raquo; B2B P&L'); ?></li>
                           <li> <?= anchor('admin/profit/b2c/all','&raquo; B2C P&L'); ?></li>
                        </ul>
                     </li>
                        <?php } if(in_array("View Billing", $permission)){?>

                    <li> <a href="#"><i class="fa fa-file-o nav_icon"></i>Reports<span class="fa arrow"></span></a>
                           <ul class="nav nav-third-level">
                             <li> <?= anchor('admin/report/gstr1/all','&raquo; GSTR1'); ?> </li>
                           </ul>
                         </li>
                         <li> <a href="#"><i class="fa fa-adjust nav_icon"></i>Purchase<span class="fa arrow"></span></a>
                          <ul class="nav nav-third-level">
                            <li> <?= anchor('admin/purchase/party/all','&raquo; Party List'); ?></li>
                            <li> <?= anchor('admin/purchase/subparty/all','&raquo; Sub-Party List'); ?></li>
                            <li> <?= anchor('admin/purchase/bill/all','&raquo; Purchase Bill'); ?></li>
                            <li> <?= anchor('admin/purchase/record_payment','&raquo; Record Payment'); ?></li>
                            <li> <?= anchor('admin/purchase/import_purchase','&raquo; Import Purchase'); ?></li>
                          </ul>
                         </li>
                       <?php }?>
                     </ul>
                    </li>

                     <?php } if(in_array("View Support", $permission)) {?>
                    <li> <a href="#"><i class="fa fa-laptop nav_icon"></i>Support<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><?= anchor('admin/support/category',' &raquo; Issue Category'); ?></li>
                            <li><?= anchor('admin/support/sub_category',' &raquo; Issue Sub Category'); ?></li>
                            <li><?= anchor('admin/support/ticket',' &raquo; Support Ticket'); ?></li>
                        </ul>
                    </li>
                     <?php } if(in_array("Sales Mapping", $permission)) {?>
                       <li> <a href="#"><i class="fa fa-chain-broken nav_icon"></i>Sales Mapping<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <?php  if(in_array("Sales Head", $permission)) {?>
                            <li><?= anchor('admin/sales/target',' &raquo; Sales Target'); ?></li>
                           <?php }?>
                            <li><?= anchor('admin/sales/client',' &raquo; Listed Clients'); ?></li>
                            <li><?= anchor('admin/sales/summary/all',' &raquo; Overall Performance'); ?></li>
                            <li><?= anchor('admin/sales/performance/all',' &raquo; B2B Performance'); ?></li>
                            <!--<li><?= anchor('admin/sales/b2c_performance/all',' &raquo; B2C Performance'); ?></li>-->
                            <li><?= anchor('admin/sales/report/all',' &raquo; Sales Reports'); ?></li>
                            <?php  if(in_array("Sales Head", $permission)) {?>
                            <li><?= anchor('admin/sales/datewise/all',' &raquo; Datewise Report'); ?></li>
                           <?php } else {?>
                            <li><?= anchor('admin/sales/daily_target/all',' &raquo; Daily Target'); ?></li>
                           <?php } ?>

                        </ul>
                    </li>

                    <?php } if(in_array("View Additional Section", $permission)) {?>
                    <li> <a href="#"><i class="fa fa-plus nav_icon"></i>Additional Section<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <!--<?php if(in_array("View Payment Code", $permission)) {?>  <li> <?= anchor('admin/payment_code',' &raquo; Payment Code'); ?></li>  <?php }?>-->
                            <?php if(in_array("Terms & Policy", $permission)) {?>     <li><?= anchor('admin/support/terms',' &raquo; Terms & Policy'); ?></li> <?php }?>
                        </ul>
                    </li>
                    <?php } if(in_array("View Partner", $permission)) {?>
                    <li> <a href="#"><i class="fa fa-table nav_icon"></i>Partner<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><?= anchor('admin/partnerb2b/',' &raquo; B2B Partner'); ?></li>
                            <!--<li><?= anchor('admin/partner/',' &raquo; All B2C Partner'); ?></li>-->
                            <!--<li><?= anchor('admin/partner/pricing',' &raquo; B2C Pricing'); ?></li>-->
                            <!--<li><?= anchor('admin/matrix/b2cfuel',' &raquo;  B2C Fuel'); ?></li>-->

                        </ul> 
                    </li>
                    <?php } if(in_array("View Matrix", $permission)) {?>
                      <li> <a href="#"><i class="fa fa-table nav_icon"></i>B2B Matrix<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><?= anchor('admin/matrix/zone_list',' &raquo; Zone List'); ?></li>
                            <li><?= anchor('admin/matrix/',' &raquo;  16 Matrix Price'); ?></li>
                            <li><?= anchor('admin/matrix/charge',' &raquo;  B2B Charges'); ?></li>
                            <li><?= anchor('admin/price/bluedart',' &raquo; Bluedart Price'); ?></li>
                            <li><?= anchor('admin/price/delhivery',' &raquo; Delhivery Price'); ?></li>
                            <li><?= anchor('admin/price/criticalLog',' &raquo; Critical Log Price'); ?></li>
                            <li><?= anchor('admin/price/xpressbees',' &raquo; Xpressbees Price'); ?></li>


                            <!-- <li><?= anchor('admin/matrix/mini',' &raquo;  Minimum Charges'); ?></li> -->
                            <!-- <li><?= anchor('admin/matrix/',' &raquo; 24 Matrix Price'); ?></li> -->
                            <!-- <li><?= anchor('admin/matrix/zone',' &raquo;  24 Matrix Zone'); ?></li> -->
                           
                            <!-- <li><?= anchor('admin/matrix/air_price',' &raquo; 16 Matrix Price'); ?></li> -->
                            <!-- <li><?= anchor('admin/matrix/air_zone',' &raquo;  16 Matrix Zone'); ?></li>
                            <li><?= anchor('admin/matrix/zone_nine',' &raquo;  9 Matrix Zone'); ?></li>
                            <li><?= anchor('admin/price/',' &raquo; 5 Matrix Price'); ?></li>
                            <li><?= anchor('admin/zone/',' &raquo; 5 Matrix Zone'); ?></li>
                            <li><?= anchor('admin/rivigo/',' &raquo; Rivigo Price'); ?></li> 
                            <li><?= anchor('admin/rivigo/zone',' &raquo; Rivigo Zone'); ?></li>
                            <li><?= anchor('admin/gati/',' &raquo; Gati Price'); ?></li>
                            <li><?= anchor('admin/gati/zone',' &raquo; Gati Zone'); ?></li>
                            <li><?= anchor('admin/oxygen/zone',' &raquo;  Oxygen Zone'); ?></li>
                            <li><?= anchor('admin/oxygen/',' &raquo; Oxygen Price'); ?></li>
                             <li><?= anchor('admin/matrix/dtdc_price',' &raquo; DTDC Price'); ?></li>
                            <li><?= anchor('admin/vxpress/',' &raquo; Vxpress Price'); ?></li>
                            <li><?= anchor('admin/vxpress/zone',' &raquo; Vxpress Zone'); ?></li> --> 
                        </ul>
                    </li>

                    <?php } if(in_array("View Access Control", $permission)) {?>
                    <li> <a href="#"><i class="fa fa-cog nav_icon"></i>Access Control<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <!--<?php if(in_array("All Users", $permission)) {?> <li><?= anchor('admin/user/',' &raquo; All Users'); ?></li><?php }?>-->
                            <?php if(in_array("Access Permission", $permission)) {?> <li><?= anchor('admin/control/',' &raquo; Access Permission'); ?></li>   <?php }?>
                            <?php if(in_array("Login User Panel", $permission)) {?> <li><?= anchor('admin/control/login_as',' &raquo; Login User Panel'); ?></li><?php }?>
                            <?php if(in_array("Activity Log", $permission)) {?> <li><?= anchor('admin/activityLog',' &raquo; Activity Log'); ?></li><?php }?>
                        </ul>
                    </li>
                    
                     
                    <?php } if(in_array("View Screen & Login", $permission)) { ?>
                    <li> <a href="#"><i class="fa fa-cog nav_icon"></i>Software & Productivity Reports<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level"> 
                            <?php if(in_array("User Login Activity", $permission)) {?> <li><?= anchor('admin/activityLog/user_login_activity',' &raquo; User Login Activity'); ?></li>   <?php }?>
                             <?php if(in_array("Screen Time Report", $permission)) {?> <li><?= anchor('admin/activityLog/software_screen_time_report',' &raquo; Screen Time Report'); ?></li><?php }?>
                           
                        </ul>
                    </li>  
                    
                   <?php }?>
                    <li><?= anchor('admin/logout','<i class="fa fa-lock"></i> &nbsp;Logout'); ?></li>
                </ul>
            </div>
        </div>
    </nav>
