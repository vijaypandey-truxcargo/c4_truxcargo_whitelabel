<?php
$filters = $filters ?? [];
$selectedDate = (string) ($filters['date'] ?? 'All');
$date1 = (string) ($filters['date1'] ?? '');
$date2 = (string) ($filters['date2'] ?? '');
$showCustomDate = $selectedDate === 'Custom Range';
?>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
            <h3 class="pull-left"><?= esc($page_title ?? 'Tracking Exception Report'); ?></h3>
            <div class="clearfix"></div>

            <div class="bs-example4" style="overflow-x: scroll; width:96%;">
                <div class="col-lg-12">
                    <?= form_open('admin/TrackingExceptionReport'); ?>
                        <div class="col-lg-2">
                            <input type="text" class="form-control" name="awb" value="<?= esc($filters['awb'] ?? '', 'attr'); ?>" placeholder="AWB No.">
                        </div>
                        <div class="col-lg-2">
                            <input type="text" class="form-control" name="event_code" value="<?= esc($filters['event_code'] ?? '', 'attr'); ?>" placeholder="Event Code">
                        </div>
                        <div class="col-lg-3">
                            <input type="text" class="form-control" name="event_desc" value="<?= esc($filters['event_desc'] ?? '', 'attr'); ?>" placeholder="Event Desc">
                        </div>
                        <div class="col-lg-2">
                            <select class="form-control" name="date" id="date" onchange="dateSearch()">
                                <option value="All" <?= $selectedDate === 'All' ? 'selected' : ''; ?>>All</option>
                                <option value="Today" <?= $selectedDate === 'Today' ? 'selected' : ''; ?>>Today</option>
                                <option value="Yesterday" <?= $selectedDate === 'Yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                                <option value="7 Days" <?= $selectedDate === '7 Days' ? 'selected' : ''; ?>>Last 7 Days</option>
                                <option value="30 Days" <?= $selectedDate === '30 Days' ? 'selected' : ''; ?>>Last 30 Days</option>
                                <option value="This Month" <?= $selectedDate === 'This Month' ? 'selected' : ''; ?>>This Month</option>
                                <option value="Last Month" <?= $selectedDate === 'Last Month' ? 'selected' : ''; ?>>Last Month</option>
                                <option value="Custom Range" <?= $selectedDate === 'Custom Range' ? 'selected' : ''; ?>>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-lg-2" id="show" style="display:<?= $showCustomDate ? 'block' : 'none'; ?>;">
                            <input type="date" name="date1" class="form-control" value="<?= esc($date1, 'attr'); ?>">
                        </div>
                        <div class="col-lg-2" id="show1" style="display:<?= $showCustomDate ? 'block' : 'none'; ?>;">
                            <input type="date" name="date2" class="form-control" value="<?= esc($date2, 'attr'); ?>" style="margin-top:6px;">
                        </div>
                        <div class="col-lg-3" style="margin-top:6px;">
                            <input type="submit" name="apply" value="Apply" class="btn btn-primary">
                            <a href="<?= base_url('admin/TrackingExceptionReport/all'); ?>" class="btn btn-default" style="margin-left:10px;">Reset</a>
                        </div>
                    <?= form_close(); ?>
                </div>

                <table class="table table-responsive" id="example">
                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>Date</th>
                            <th>AWB</th>
                            <th>Customer</th>
                            <th>Vendor</th>
                            <th>Event Code</th>
                            <th>Event Desc</th>
                            <th>Expected Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($res)): ?>
                            <?php foreach ($res as $index => $row): ?>
                                <tr>
                                    <td><?= (int) ($count ?? 0) + $index + 1; ?></td>
                                    <td><?= ! empty($row->created_date) ? esc(date('d M Y', strtotime($row->created_date))) : ''; ?></td>
                                    <td><?= esc($row->awb_no ?? ''); ?></td>
                                    <td><?= esc($row->customer_name ?? ''); ?></td>
                                    <td><?= esc($row->vendor_name ?? ''); ?></td>
                                    <td><?= esc($row->event_code ?? ''); ?></td>
                                    <td><?= esc($row->event_desc ?? ''); ?></td>
                                    <td><?= esc($row->expected_date ?? ''); ?></td>
                                    <td><?= esc($row->status ?? ''); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="view_awb('<?= esc($row->awb_no ?? '', 'js'); ?>')">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No NDR record found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <p class="pagination"><?= $links ?? ''; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="awbModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">AWB Details</h4>
                <button type="button" class="close" data-dismiss="modal" style="margin-top:-31px;font-size:30px;color:red;">&times;</button>
            </div>
            <div class="modal-body" id="awbModalBody">Loading...</div>
        </div>
    </div>
</div>

<script>
function refreshTrackingCsrf() {
    var match = document.cookie.match(new RegExp('(?:^|; )<?= config('Security')->cookieName; ?>=([^;]*)'));
    if (match) {
        csrfHash = decodeURIComponent(match[1]);
    }
}

$(document).ajaxComplete(refreshTrackingCsrf);

function dateSearch() {
    var opt = $('#date').val();
    if (opt === 'Custom Range') {
        $('#show, #show1').show();
    } else {
        $('#show, #show1').hide();
    }
}

function view_awb(awb) {
    $.ajax({
        type: 'POST',
        url: '<?= base_url('admin/TrackingExceptionReport/view_awb'); ?>',
        data: {[csrfName]: csrfHash, awb: awb},
        success: function (data) {
            $('#awbModalBody').html(data);
            $('#awbModal').modal('show');
            refreshTrackingCsrf();
        },
        error: function () {
            alert('fail');
        }
    });
}
</script>
