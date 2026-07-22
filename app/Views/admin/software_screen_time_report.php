<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">

                <h3 style="margin:0;">Software Screen Time Report</h3>

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

            <?= form_open('admin/activityLog/software_screen_time_report'); ?>

            <div class="row" style="margin-bottom:15px;">

                <div class="col-md-8">
                    <input type="text" name="user_name" placeholder="Serach By Name" class="form-control"  value="<?= isset($selected_user_name) ? $selected_user_name : '' ?>">
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
                             <th>User name</th>
                            <th>Module Name</th>
                            <th>Page Title</th>
                            <th>Start Time</th>
                            <th>Last Active Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($count)) { $count = 0; } ?>

                        <?php if (!empty($code)) { ?>
                            <?php foreach ($code as $key) { $count++; ?>

                                <?php
                                $seconds = (int) $key->duration_seconds;

                                $hours = floor($seconds / 3600);
                                $minutes = floor(($seconds % 3600) / 60);
                                $secs = $seconds % 60;

                                $duration = sprintf(
                                    '%02d:%02d:%02d',
                                    $hours,
                                    $minutes,
                                    $secs
                                );
                                ?>

                                <tr class="active">
                                    <td><?= $count; ?></td>
                                    <td><?= esc($user_map[$key->user_id] ?? 'N/A'); ?></td>

                                    <td>
                                        <?= !empty($key->module_name)
                                            ? esc($key->module_name)
                                            : '-'; ?>
                                    </td>

                                    <td>
                                        <?= !empty($key->page_title)
                                            ? esc($key->page_title)
                                            : '-'; ?>
                                    </td>

                                    <td>
                                        <?= !empty($key->start_time)
                                            ? date('d-m-Y h:i:s A', strtotime($key->start_time))
                                            : '-'; ?>
                                    </td>

                                    <td>
                                        <?= !empty($key->last_active_time)
                                            ? date('d-m-Y h:i:s A', strtotime($key->last_active_time))
                                            : '-'; ?>
                                    </td>

                                    <td><?= $duration; ?></td>

                                    <td>
                                        <?php if ($key->status == 'ACTIVE') { ?>
                                            <span class="text-success">Active</span>

                                        <?php } elseif ($key->status == 'CLOSED') { ?>
                                            <span class="text-primary">Closed</span>

                                        <?php } elseif ($key->status == 'INACTIVE') { ?>
                                            <span class="text-danger">Inactive</span>
 
                                        <?php } else { ?>
                                            <span>-</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?= !empty($key->created_at)
                                            ? date('d-m-Y h:i:s A', strtotime($key->created_at))
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
    window.location.href = "<?= base_url('admin/export/export_software_screen_time_report'); ?>";
});
</script> 
