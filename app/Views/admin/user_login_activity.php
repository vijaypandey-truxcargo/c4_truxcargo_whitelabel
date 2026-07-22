<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0;">User Login Activity Report</h3>

                <div style="display:flex; align-items:center; gap:10px;">
                    <button id="exportBtn" type="button" class="btn btn-success">
                        Export All
                    </button>
                </div>
            </div>

            <div class="clearfix"></div>

            <!-- Flash Message -->
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    $error = session()->getFlashdata('error');
                    $error_class = session()->getFlashdata('error_class');

                    if ($error): ?>
                        <div class="alert alert-dismissible <?= $error_class; ?>">
                            <strong><?= $error; ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bs-example4" data-example-id="contextual-table">

            <?= form_open('admin/activityLog/user_login_activity'); ?>

            <div class="row" style="margin-bottom:15px;">

                <div class="col-md-4">
                    <input type="text" name="user_name" placeholder="Serach By Name" class="form-control"  value="<?= isset($selected_user_name) ? $selected_user_name : '' ?>">
                </div>
                <div class="col-md-4">
                    <select class="form-control" name="login_type">

                        <option value="">ALL Login Type</option>

                        <option value="Admin"
                            <?= (($selected_user_type ?? '') == 'Admin') ? 'selected' : '' ?>>
                            Admin
                        </option>

                        <option value="Customer"
                            <?= (($selected_user_type ?? '') == 'Customer') ? 'selected' : '' ?>>
                            Customer
                        </option>

                    </select>
                </div>

                <div class="col-md-2">
                    <input type="submit" name="apply" value="Apply" class="btn btn-primary" style="width:100%;">
                </div>
                <div class="col-md-2">
                    <input type="submit" name="reset" value="Reset" class="btn btn-danger" style="width:100%;">
                </div>

            </div>

            <?= form_close(); ?>

            <div class="bs-example4" data-example-id="contextual-table">

                <table class="table table-responsive" id="example">
                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>Name</th>
                            <th>Login As</th>
                            <th>Login Time</th>
                            <th>IP Address</th>
                            <th>Browser / Device</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($count)) { $count = 0; } ?>

                        <?php if (!empty($code)) { ?>
                            <?php foreach ($code as $key) { $count++; ?>

                                <tr class="active">
                                    <td><?= $count; ?></td>

                                    <td><?= esc($key->name); ?></td>

                                    <td><?= esc($key->login_as); ?></td>

                                    <td>
                                        <?= !empty($key->login_time)
                                            ? date('d-m-Y h:i A', strtotime($key->login_time))
                                            : '-'; ?>
                                    </td>

                                    <td><?= esc($key->ip_address); ?></td>

                                    <td style="max-width:250px; word-break:break-word;">
                                        <?= esc($key->user_agent); ?>
                                    </td>

                                    <td>
                                        <?php if ($key->status == 'LOGIN') { ?>
                                            <span class="text-success">Active</span>

                                        <?php } elseif ($key->status == 'LOGOUT') { ?>
                                            <span class="text-primary">Logout</span>

                                        <?php } elseif ($key->status == 'AUTO_LOGOUT') { ?>
                                            <span class="text-danger">Auto Logout</span>

                                        <?php } else { ?>
                                            <span>-</span>
                                        <?php } ?>
                                    </td> 
 
                                    <td>
                                        <?= !empty($key->created_at)
                                            ? date('d-m-Y h:i A', strtotime($key->created_at))
                                            : '-'; ?>
                                    </td>
                                </tr>

                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    No records found.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <p class="pagination"><?= $links; ?></p>

            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('exportBtn').addEventListener('click', function () {
    window.location.href = "<?= base_url('admin/export/export_user_login_activity'); ?>";
});
</script>
