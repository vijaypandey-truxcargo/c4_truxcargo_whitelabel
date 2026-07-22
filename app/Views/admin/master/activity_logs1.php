<div id="page-wrapper">

    <style>

        .log-table td,
        .log-table th{
            font-size:12px;
            vertical-align:top !important;
        }

        .activity-box{
            max-height:220px;
            overflow:auto;
            border:1px solid #ddd;
            border-radius:5px;
            padding:5px;
            background:#fff;
        }

        .activity-box table{
            margin-bottom:0px;
        }

        .activity-box th{
            background:#f5f5f5;
            width:35%;
            font-weight:600;
        }

        .new-val{
            color:green;
            font-weight:600;
        }

        .old-val{
            color:#d9534f;
            font-weight:600;
        }

        .badge-action{
            padding:5px 10px;
            border-radius:4px;
            color:#fff;
            font-size:11px;
        }

        .badge-insert{
            background:#28a745;
        }

        .badge-update{
            background:#007bff;
        }

        .badge-delete{
            background:#dc3545;
        }

        .badge-other{
            background:#f0ad4e;
        }

    </style>

    <div class="col-md-12 graphs">

        <div class="xs">

            <div style="
                margin-bottom:15px;
                width:100%;
                display:flex;
                justify-content:space-between;
                align-items:center;
            ">

                <h3 style="margin:0;">
                    Activity Logs
                </h3>

            </div>

            <div class="clearfix"></div>

            <!-- FLASH MESSAGE -->
            <div class="row">

                <div class="col-lg-12">

                    <?php
                    $error = $this->session->flashdata('error');

                    $error_class = $this->session->flashdata('error_class');

                    if ($error):
                    ?>

                    <div class="alert alert-dismissible <?= $error_class; ?>">

                        <strong><?= $error; ?></strong>

                    </div>

                    <?php endif; ?>

                </div>

            </div>

            <!-- TABLE -->
            <div class="bs-example4"
                 data-example-id="contextual-table">

                <table class="table table-bordered table-hover log-table" id="example">

                    <thead>

                        <tr>

                            <th>Sr.No.</th>

                            <th>User</th>

                            <th>Module</th>

                            <th>Action</th>

                            <th>IP Address</th>

                            <th>Date</th>

                            <th width="25%">New Data</th>

                            <th width="25%">Old Data</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        if (empty($count)) {
                            $count = 0;
                        }

                        foreach ($logs as $log):

                            $count++;

                            $new_data = json_decode($log->new_data, true);

                            $old_data = json_decode($log->old_data, true);

                        ?>

                        <tr class="active">

                            <td>
                                <?= $count; ?>
                            </td>

                            <td>
                                <?= $log->user_name; ?>
                            </td>

                            <td>
                                <?= $log->module_name; ?>
                            </td>

                            <td>

                                <?php if($log->action_type == 'INSERT'){ ?>

                                    <span class="badge-action badge-insert">
                                        <?= $log->action_type; ?>
                                    </span>

                                <?php } elseif($log->action_type == 'UPDATE'){ ?>

                                    <span class="badge-action badge-update">
                                        <?= $log->action_type; ?>
                                    </span>

                                <?php } elseif($log->action_type == 'DELETE'){ ?>

                                    <span class="badge-action badge-delete">
                                        <?= $log->action_type; ?>
                                    </span>

                                <?php } else { ?>

                                    <span class="badge-action badge-other">
                                        <?= $log->action_type; ?>
                                    </span>

                                <?php } ?>

                            </td>

                            <td>
                                <?= $log->ip_address; ?>
                            </td>

                           <td>
                                <?= date('d M Y h:i:s A', strtotime($log->created_at)); ?>
                            </td>

                            <!-- NEW DATA -->
                            <td>

                                <div class="activity-box">

                                    <?php if(!empty($new_data)){ ?>

                                        <table class="table table-bordered table-striped">

                                            <?php foreach($new_data as $key => $value){ ?>

                                                <tr>

                                                    <th>
                                                        <?= ucwords(str_replace('_',' ',$key)); ?>
                                                    </th>

                                                    <td class="new-val">

                                                        <?php
                                                        if(is_array($value)){
                                                            echo '<pre>';
                                                            print_r($value);
                                                            echo '</pre>';
                                                        }else{
                                                            echo $value;
                                                        }
                                                        ?>

                                                    </td>

                                                </tr>

                                            <?php } ?>

                                        </table>

                                    <?php } else { ?>

                                        <center>
                                            No Data
                                        </center>

                                    <?php } ?>

                                </div>

                            </td>

                            <!-- OLD DATA -->
                            <td>

                                <div class="activity-box">

                                    <?php if(!empty($old_data)){ ?>

                                        <table class="table table-bordered table-striped">

                                            <?php foreach($old_data as $key => $value){ ?>

                                                <tr>

                                                    <th>
                                                        <?= ucwords(str_replace('_',' ',$key)); ?>
                                                    </th>

                                                    <td class="old-val">

                                                        <?php
                                                        if(is_array($value)){
                                                            echo '<pre>';
                                                            print_r($value);
                                                            echo '</pre>';
                                                        }else{
                                                            echo $value;
                                                        }
                                                        ?>

                                                    </td>

                                                </tr>

                                            <?php } ?>

                                        </table>

                                    <?php } else { ?>

                                        <center>
                                            No Data
                                        </center>

                                    <?php } ?>

                                </div>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

                <p class="pagination">
                    <?= $links; ?>
                </p>

            </div>

        </div>

    </div>

</div>