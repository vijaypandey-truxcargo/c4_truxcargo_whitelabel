<main class="main-content">
    <div class="position-relative ">
       <?php $planName = $plan ?? ''; if(empty($planName)){$pic=base_url('assets/images/avatars/01.png');}
             else if($planName=='Startup'){$pic=base_url('assets/images/medal.png');}
             else if($planName=='Enterprise'){$pic=base_url('assets/images/crown.png');}
             else {$pic=base_url('assets/images/trophy.png');}?>
        <nav class="nav navbar navbar-expand-xl navbar-light iq-navbar">
                <div class="container-fluid navbar-inner">
                    <div class="navbar-brand">
                        <!--Logo start-->
                        <div class="logo-main d-flex justify-content-between gap-5">
                            <div class="logo-normal ms-3">
                                <a href="<?= base_url();?>"> <img src="<?= base_url('uploads/profile/' . ($wconfig->logo ?? '')) ?>" class="w-25" alt="" srcset="">   </a>         
                            </div>                            
                        </div>
                        <div>
                            <li class="dropdown" style="right:-20%">
                                <a class="py-0 d-flex align-items-center ps-3" href="#" id="profile-setting" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?= $pic;?>" alt="User-Profile" class="theme-color-default-img img-fluid avatar avatar-50 avatar-rounded" loading="lazy">
                                        &nbsp; <i class="right-icon">
                                        <svg width="22" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.4"
                                                d="M22 12C22 17.515 17.514 22 12 22C6.486 22 2 17.515 2 12C2 6.486 6.486 2 12 2C17.514 2 22 6.486 22 12Z"
                                                fill="currentColor"></path>
                                            <path
                                                d="M16.2211 10.5575C16.2211 10.7485 16.1481 10.9405 16.0021 11.0865L12.5321 14.5735C12.3911 14.7145 12.2001 14.7935 12.0001 14.7935C11.8011 14.7935 11.6101 14.7145 11.4691 14.5735L7.99707 11.0865C7.70507 10.7935 7.70507 10.3195 7.99907 10.0265C8.29307 9.73448 8.76807 9.73548 9.06007 10.0285L12.0001 12.9815L14.9401 10.0285C15.2321 9.73548 15.7061 9.73448 16.0001 10.0265C16.1481 10.1725 16.2211 10.3655 16.2211 10.5575Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile-setting">
                                    <li> <a class="dropdown-item" href="<?= base_url('dashboard/profile');?>"><i class="fa fa-user text-primary"></i>My Profile</a></li>
                                    <li>  <a class="dropdown-item" href="<?= base_url('plans/');?>"><i class="fa fa-bookmark  text-primary"></i>Shipping Plans</a>    </li>
                                    <?php  if($user->oauth_provider=="google" && $user->gpass==0){?>
                                        <li> <a class="dropdown-item" href="<?= base_url('dashboard/create_password');?>"><i class="fa fa-key  text-primary"></i>Create Password</a></li>
                                    <?php } else {?>
                                    <li> <a class="dropdown-item" href="<?= base_url('dashboard/change_password');?>"><i class="fa fa-key  text-primary"></i>Change Password</a></li>
                                    <?php }?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                     <li> <a class="dropdown-item" href="<?= base_url('dashboard/logout');?>"><i class="fa fa-power-off  text-primary"></i> Logout</a></li>
                                </ul>
                            </li>
                        </div>
                    </div>
                    <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                        <i class="icon hamburger d-flex">
                        <svg height="20" color="#fff" viewBox="0 0 32 32" width="20" xmlns="http://www.w3.org/2000/svg"><g id="Layer_13" data-name="Layer 13"><path d="m30 7a1 1 0 0 1 -1 1h-26a1 1 0 0 1 0-2h26a1 1 0 0 1 1 1zm-5 8h-22a1 1 0 0 0 0 2h22a1 1 0 0 0 0-2zm-9 9h-13a1 1 0 0 0 0 2h13a1 1 0 0 0 0-2z"/></g></svg>
                        </i>
                    </div>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0 ">
                        <li>
                                <?= form_open('order/search',['autocomplete'=>'off','class'=>'searchbox']);?>
                                <button type="submit" class="border-0" style="background:transparent; position: absolute " name="submit">  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 32 33"
                                        fill="none">
                                        <g clip-path="url(#clip0_925_4873)">
                                            <path
                                                d="M19.375 12.375C19.03 12.375 18.75 12.655 18.75 13C18.75 13.345 19.03 13.625 19.375 13.625C19.72 13.625 20 13.345 20 13C20 12.655 19.72 12.375 19.375 12.375Z"
                                                fill="#7939CC" />
                                            <path
                                                d="M31.2676 28.2324L22.9251 19.8899C24.2841 17.8428 25 15.4722 25 13C25 6.1075 19.3925 0.5 12.5 0.5C5.6075 0.5 0 6.1075 0 13C0 19.8925 5.6075 25.5 12.5 25.5C14.9722 25.5 17.3429 24.7841 19.3899 23.4251L21.6959 25.7311C21.6962 25.7314 21.6965 25.7317 21.6969 25.7321L27.7324 31.7676C28.2048 32.2399 28.8324 32.5 29.5 32.5C30.1676 32.5 30.7953 32.2399 31.2674 31.7677C31.7398 31.2957 32 30.6679 32 30C32 29.3321 31.7398 28.7043 31.2676 28.2324ZM19.096 22.1079L19.0959 22.1081C17.1667 23.5093 14.8859 24.25 12.5 24.25C6.29675 24.25 1.25 19.2033 1.25 13C1.25 6.79675 6.29675 1.75 12.5 1.75C18.7033 1.75 23.75 6.79675 23.75 13C23.75 15.3859 23.0093 17.6667 21.608 19.5959C20.9069 20.5618 20.0618 21.4069 19.096 22.1079ZM20.4072 22.6746C21.0541 22.1454 21.6454 21.5541 22.1746 20.9072L23.9478 22.6804C23.4085 23.3169 22.8169 23.9086 22.1804 24.4478L20.4072 22.6746ZM30.3837 30.8837C30.1474 31.1199 29.8336 31.25 29.5 31.25C29.1664 31.25 28.8526 31.1199 28.6163 30.8837L23.0666 25.334C23.6996 24.7908 24.2908 24.1996 24.8339 23.5667L30.3838 29.1166C30.6199 29.3524 30.75 29.6661 30.75 30C30.75 30.3339 30.6199 30.6476 30.3837 30.8837Z"
                                                fill="#7939CC" />
                                            <path
                                                d="M12.5 3C6.98594 3 2.5 7.48594 2.5 13C2.5 18.5141 6.98594 23 12.5 23C18.0141 23 22.5 18.5141 22.5 13C22.5 7.48594 18.0141 3 12.5 3ZM12.5 21.75C7.67525 21.75 3.75 17.8247 3.75 13C3.75 8.17525 7.67525 4.25 12.5 4.25C17.3247 4.25 21.25 8.17525 21.25 13C21.25 17.8247 17.3247 21.75 12.5 21.75Z"
                                                fill="#7939CC" />
                                            <path
                                                d="M19.5041 10.3171C18.9659 8.91631 18.0288 7.71869 16.7941 6.85375C15.5299 5.96813 14.0451 5.5 12.5 5.5C12.1549 5.5 11.875 5.77988 11.875 6.125C11.875 6.47012 12.1549 6.75 12.5 6.75C15.0687 6.75 17.4144 8.36362 18.3372 10.7654C18.4327 11.0139 18.6696 11.1664 18.9208 11.1664C18.9953 11.1664 19.0711 11.153 19.1448 11.1247C19.467 11.0009 19.6279 10.6393 19.5041 10.3171Z"
                                                fill="#7939CC" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_925_4873">
                                                <rect width="32" height="32" fill="white"
                                                    transform="translate(0 0.5)" />
                                            </clipPath>
                                        </defs>
                                    </svg></button> 
                                <input type="text" class="text search-input w-100 form-control rounded-pill" name="lrn" placeholder="Search BY LRN/AWB no" required="required">
                                   
                                <?= form_close();?>
                            </li>
                        <li> <?php if($wallet>0){$btn='text-primary';} else{ $btn='text-danger';}?>
                                <a href="<?= base_url('wallet/index/deduction/all');?>" class="btn btn-light btn-wallet rounded-pill <?= $btn;?>">
                                    <svg width="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="me-2">
                                        <defs>
                                            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" style="stop-color:#7939CC" />
                                                <stop offset="100%" style="stop-color:#008" />
                                            </linearGradient>
                                        </defs>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M21.9964 8.37513H17.7618C15.7911 8.37859 14.1947 9.93514 14.1911 11.8566C14.1884 13.7823 15.7867 15.3458 17.7618 15.3484H22V15.6543C22 19.0136 19.9636 21 16.5173 21H7.48356C4.03644 21 2 19.0136 2 15.6543V8.33786C2 4.97862 4.03644 3 7.48356 3H16.5138C19.96 3 21.9964 4.97862 21.9964 8.33786V8.37513ZM6.73956 8.36733H12.3796H12.3831H12.3902C12.8124 8.36559 13.1538 8.03019 13.152 7.61765C13.1502 7.20598 12.8053 6.87318 12.3831 6.87491H6.73956C6.32 6.87664 5.97956 7.20858 5.97778 7.61852C5.976 8.03019 6.31733 8.36559 6.73956 8.36733Z"
                                            fill="url(#grad1)"></path>
                                        <path opacity="0.4"
                                            d="M16.0374 12.2966C16.2465 13.2478 17.0805 13.917 18.0326 13.8996H21.2825C21.6787 13.8996 22 13.5715 22 13.166V10.6344C21.9991 10.2297 21.6787 9.90077 21.2825 9.8999H17.9561C16.8731 9.90338 15.9983 10.8024 16 11.9102C16 12.0398 16.0128 12.1695 16.0374 12.2966Z"
                                            fill="url(#grad1)"></path>
                                        <circle cx="18" cy="11.8999" r="1" fill="url(#grad1)"></circle>
                                    </svg> &nbsp;
                                     
                                    <span> 
                                        <i class="fa fa-inr " style="font-size: 13px"></i> <?= number_format((float) ($wallet ?? 0), 2);?>
                                    </span>
                                </a>
                        </li>
                        <?php $n=0; if($bank==0){$n++;} if(empty($user->gst) || $user->gst=='NA'){$n++;} 
                             $n=2;if($n>0){
                        ?>  
                                  <li class="nav-item dropdown border-end">
                                <a href="#" class="nav-link btn btn-light rounded-pill m-2"
                                    id="notification-drop" data-bs-toggle="dropdown">
                                    
                                    <svg class="icon-24" width="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.7695 11.6453C19.039 10.7923 18.7071 10.0531 18.7071 8.79716V8.37013C18.7071 6.73354 18.3304 5.67907 17.5115 4.62459C16.2493 2.98699 14.1244 2 12.0442 2H11.9558C9.91935 2 7.86106 2.94167 6.577 4.5128C5.71333 5.58842 5.29293 6.68822 5.29293 8.37013V8.79716C5.29293 10.0531 4.98284 10.7923 4.23049 11.6453C3.67691 12.2738 3.5 13.0815 3.5 13.9557C3.5 14.8309 3.78723 15.6598 4.36367 16.3336C5.11602 17.1413 6.17846 17.6569 7.26375 17.7466C8.83505 17.9258 10.4063 17.9933 12.0005 17.9933C13.5937 17.9933 15.165 17.8805 16.7372 17.7466C17.8215 17.6569 18.884 17.1413 19.6363 16.3336C20.2118 15.6598 20.5 14.8309 20.5 13.9557C20.5 13.0815 20.3231 12.2738 19.7695 11.6453Z"
                                            fill="currentColor"></path>
                                        <path opacity="0.4"
                                            d="M14.0088 19.2283C13.5088 19.1215 10.4627 19.1215 9.96275 19.2283C9.53539 19.327 9.07324 19.5566 9.07324 20.0602C9.09809 20.5406 9.37935 20.9646 9.76895 21.2335L9.76795 21.2345C10.2718 21.6273 10.8632 21.877 11.4824 21.9667C11.8123 22.012 12.1482 22.01 12.4901 21.9667C13.1083 21.877 13.6997 21.6273 14.2036 21.2345L14.2026 21.2335C14.5922 20.9646 14.8734 20.5406 14.8983 20.0602C14.8983 19.5566 14.4361 19.327 14.0088 19.2283Z"
                                            fill="currentColor"></path>
                                    </svg>
                                     <span class="badge bg-danger"><?=$n++;?></span>
                                </a>
                                <ul class="p-0 sub-drop dropdown-menu dropdown-menu-end"
                                    aria-labelledby="notification-drop">
                                    <li class="">
                                        <div
                                            class="p-3 card-header d-flex justify-content-between bg-primary rounded-top">
                                            <div class="header-title">
                                                <h5 class="mb-0 text-white">All Notifications</h5>
                                            </div>
                                        </div>
                                        <div class="p-0 card-body all-notification">                                           
                                             <?php if($bank==0){?>
                                             <a href="<?= base_url('dashboard/add_bank');?>" class="iq-sub-card">
                                                <div class="d-flex align-items-center">                                                     
                                                    <div class="ms-3 w-100">
                                                        <h6 class="mb-0 ">   <i class="fa fa-asterisk"></i>  Add Your Account Details to Get COD In Bank</h6>                                                        
                                                    </div>
                                                </div>
                                            </a><?php }?>
                                            <?php if(empty($user->gst) || $user->gst=='NA'){  ?>
                                            <a href="<?= base_url('kyc');?>" class="iq-sub-card">
                                                <div class="d-flex align-items-center">                                                     
                                                    <div class="ms-3 w-100">
                                                        <h6 class="mb-0 ">  <i class="fa fa-asterisk"></i>  Add Your GST Details for Invoice</h6>                                                        
                                                    </div>
                                                </div>
                                            </a>
                                            <?php } ?> 
                                        </div>
                                    </li>
                                </ul>
                            </li>
                             <?php }?>
                              <li class="nav-item dropdown">
                                <a class="py-0 nav-link d-flex align-items-center ps-3" href="#" id="profile-setting" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?= $pic;?>" alt="User-Profile"  class="theme-color-default-img img-fluid avatar avatar-50 avatar-rounded"  loading="lazy">
                                    
                                    <div class="caption ms-3 d-none d-md-block ">
                                        <h6 class="mb-0 caption-title nowrap"><?= $user->first; ?> </h6>
                                        <p class="mb-0 caption-sub-title"><?= $user->username; ?></p>
                                    </div>
                                   <i class="right-icon ms-2">
                                        <svg width="22" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.4"
                                                d="M22 12C22 17.515 17.514 22 12 22C6.486 22 2 17.515 2 12C2 6.486 6.486 2 12 2C17.514 2 22 6.486 22 12Z"
                                                fill="currentColor"></path>
                                            <path
                                                d="M16.2211 10.5575C16.2211 10.7485 16.1481 10.9405 16.0021 11.0865L12.5321 14.5735C12.3911 14.7145 12.2001 14.7935 12.0001 14.7935C11.8011 14.7935 11.6101 14.7145 11.4691 14.5735L7.99707 11.0865C7.70507 10.7935 7.70507 10.3195 7.99907 10.0265C8.29307 9.73448 8.76807 9.73548 9.06007 10.0285L12.0001 12.9815L14.9401 10.0285C15.2321 9.73548 15.7061 9.73448 16.0001 10.0265C16.1481 10.1725 16.2211 10.3655 16.2211 10.5575Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </i>

                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile-setting">
                                    <li> <a class="dropdown-item" href="<?= base_url('dashboard/profile');?>"><i class="fa fa-user text-primary"></i>My Profile</a></li>
                                    <?php if(empty($user->gst)  || $user->gst=='NA'){?>
                                    <li> <a class="dropdown-item" href="<?= base_url('kyc');?>"><i class="fa fa-user text-primary"></i>KYC</a></li>
                                    <?php }?>
                                    <li> <a class="dropdown-item" href="<?= base_url('dashboard/bank');?>"><i class="fa fa-bank  text-primary"></i>Bank Info</a></li>
                                    <li> <a class="dropdown-item" href="<?= base_url('plans/');?>"><i class="fa fa-bookmark  text-primary"></i>Shipping Plans</a>    </li>
                                    <li> <a class="dropdown-item" href="<?= base_url('dashboard/agreement');?>"><i class="fa fa-briefcase  text-primary"></i>Agreement</a></li>
                                    <li> <a class="dropdown-item" href="<?= base_url('training');?>"><i class="fa fa-desktop  text-primary"></i>Training</a></li>
                                    <li> <a class="dropdown-item" href="<?= base_url('dashboard/setting');?>"><i class="fa fa-cogs  text-primary"></i>Settings</a></li>
                                     <?php  if($user->oauth_provider=="google" && $user->gpass==0){?>
                                        <li> <a class="dropdown-item" href="<?= base_url('dashboard/create_password');?>"><i class="fa fa-key  text-primary"></i>Create Password</a></li>
                                    <?php } else {?>
                                    <li> <a class="dropdown-item" href="<?= base_url('dashboard/change_password');?>"><i class="fa fa-key  text-primary"></i>Change Password</a></li>
                                      <?php } ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li> <a class="dropdown-item" href="<?= base_url('dashboard/logout');?>"><i class="fa fa-power-off  text-primary"></i>Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>      