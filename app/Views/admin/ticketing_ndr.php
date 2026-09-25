<?php
$filters = $filters ?? [];
$selectedLoginId = (string) ($filters['login_id'] ?? 'All');
$selectedStatus = (string) ($filters['status'] ?? 'All');
$selectedDate = (string) ($filters['date'] ?? 'All');
$date1 = (string) ($filters['date1'] ?? '');
$date2 = (string) ($filters['date2'] ?? '');
$showCustomDate = $selectedDate === 'Custom Range';
?>

<style>
    .edit-icon {
        color: #007bff;
        cursor: pointer;
        font-size: 18px;
        transition: 0.2s;
    }

    .edit-icon:hover {
        color: #0056b3;
    }

    .mail-icon {
        color: #ff9800;
        cursor: pointer;
        font-size: 18px;
        margin-left: 8px;
        transition: 0.2s;
    }

    .mail-icon:hover {
        color: #e68900;
    }

    .view-icon {
        color: #214322;
        cursor: pointer;
        font-size: 20px;
        transition: 0.2s ease-in-out;
    }

    .view-icon:hover {
        color: #0056b3;
        transform: scale(1.1);
    }
</style>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
            <h3 class="pull-left"><?= esc($page_title ?? 'All Ticket EDD'); ?></h3>
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-lg-12">
                    <?php
                    $error = session()->getFlashdata('error');
                    $errorClass = session()->getFlashdata('error_class');
                    if ($error): ?>
                        <div class="alert alert-dismissible <?= esc($errorClass); ?>">
                            <strong><?= esc($error); ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bs-example4" style="overflow-x: scroll; width:96%;">
                <div class="custom-tab">
                    <div class="tab-content">
                        <div class="tab-pane active" id="1">
                            <div class="col-lg-12">
                                <?= form_open('admin/ticketingNdr'); ?>
                                    <div class="col-lg-3">
                                        <select class="form-control" name="login_id">
                                            <option value="All">All Members</option>
                                            <?php foreach (($key ?? []) as $member): ?>
                                                <?php $memberId = (string) ($member->id ?? ''); ?>
                                                <option value="<?= esc($memberId, 'attr'); ?>" <?= $selectedLoginId === $memberId ? 'selected' : ''; ?>>
                                                    <?= esc(trim(($member->first ?? '') . ' ' . ($member->last ?? '')) . ' (' . ($member->username ?? '') . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-3">
                                        <input type="text"
                                               class="form-control"
                                               name="lrn"
                                               value="<?= esc($filters['lrn'] ?? '', 'attr'); ?>"
                                               placeholder="Search by AWB No.">
                                    </div>

                                    <div class="col-lg-2">
                                        <select class="form-control" name="status">
                                            <option value="All">Status</option>
                                            <option value="Open" <?= $selectedStatus === 'Open' ? 'selected' : ''; ?>>Open</option>
                                            <option value="Pending" <?= $selectedStatus === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Close" <?= $selectedStatus === 'Close' ? 'selected' : ''; ?>>Close</option>
                                            <option value="Closed" <?= $selectedStatus === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                        </select>
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
                                        <input type="date" name="date2" class="form-control" value="<?= esc($date2, 'attr'); ?>" style="margin-top: 6px;">
                                    </div>

                                    <div class="col-lg-3" id="apply" style="margin-top: 6px;">
                                        <input type="submit" name="apply" value="Apply" class="btn btn-primary">
                                        <a href="<?= base_url('admin/ticketingNdr/all'); ?>" class="btn btn-default" style="margin-left:10px;">Reset</a>
                                        <button id="exportBtn" type="button" class="btn btn-success" style="margin-left:10px;">Export All</button>
                                    </div>
                                <?= form_close(); ?>
                            </div>

                            <table class="table table-responsive" id="example">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th width="15%">Date</th>
                                        <th>UserId</th>
                                        <th>Tracking No</th>
                                        <th>EDD</th>
                                        <th width="10%">Panel</th>
                                        <th width="10%">Payment Mode</th>
                                        <th>Status</th>
                                        <th width="10%">Comment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (! empty($res)): ?>
                                        <?php foreach ($res as $index => $row): ?>
                                            <tr>
                                                <td><?= (int) ($count ?? 0) + $index + 1; ?></td>
                                                <td><?= ! empty($row->created_date) ? esc(date('d M Y', strtotime($row->created_date))) : ''; ?></td>
                                                <td><?= $user($row->login_id ?? 0); ?></td>
                                                <td>
                                                    <a href="javascript:void(0);" onclick="view_awb('<?= esc($row->awb_no ?? '', 'js'); ?>')">
                                                        <?= esc($row->awb_no ?? ''); ?>
                                                    </a>
                                                </td>
                                                <td><?= esc($row->expected_date ?? ''); ?></td>
                                                <td><?= esc($row->panel ?? $row->vendor_name ?? ''); ?></td>
                                                <td><?= esc($row->mode ?? ''); ?></td>
                                                <td><?= esc($row->status ?? ''); ?></td>
                                                <td>
                                                    <i class="fa fa-comment view-icon"
                                                       onclick="view_comment(<?= (int) $row->id; ?>)"
                                                       data-toggle="modal"
                                                       data-target="#view-comment"></i>
                                                </td>
                                                <td>
                                                    <i class="fa fa-edit edit-icon"
                                                       onclick="reply(this, <?= (int) $row->id; ?>)"
                                                       data-awb="<?= esc($row->awb_no ?? '', 'attr'); ?>"
                                                       data-toggle="modal"
                                                       data-target="#reply"></i>
                                                    <i class="fa fa-envelope mail-icon"
                                                       onclick="openMailConfirm(this, <?= (int) $row->id; ?>)"
                                                       data-awb="<?= esc($row->awb_no ?? '', 'attr'); ?>"
                                                       title="Send Mail"></i>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No EDD ticket found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <p class="pagination"><?= $links ?? ''; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="loader" id="loader">
    <img src="<?= base_url('assets/images/loading.gif'); ?>">
</div>

<div class="modal fade" id="success">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center p-3">
            <div class="modal-body">
                <div style="font-size:50px; color:green;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <h4>Success</h4>
                <p id="success-body"></p>
                <button class="btn btn-success btn-sm" onclick="location.reload()">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mailConfirmModal">
    <div class="modal-dialog modal-sm" style="margin:0 auto;top:50%;transform:translateY(-50%);">
        <div class="modal-content">
            <div class="modal-header" style="display:flex;align-items:center;justify-content:space-between;">
                <h4 class="modal-title" style="margin:0;">Confirm Mail</h4>
                <button type="button" class="close" data-dismiss="modal" style="margin:0;line-height:1;">&times;</button>
            </div>
            <div class="modal-body">
                <p id="mailConfirmText" style="margin-bottom:0;"></p>
                <input type="hidden" id="mailConfirmId">
                <input type="hidden" id="mailConfirmAwb">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="sendTicketMail()">Send Mail</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reply">
    <div class="modal-dialog">
        <div class="modal-content" id="reply1"></div>
    </div>
</div>

<div class="modal fade" id="view-comment">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="view-comment1"></div>
    </div>
</div>

<div class="modal fade" id="awbModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">AWB Details</h4>
                <button type="button" class="close" data-dismiss="modal" style="margin-top: -31px; font-size: 30px; color: red;">&times;</button>
            </div>
            <div class="modal-body" id="awbModalBody">Loading...</div>
        </div>
    </div>
</div>

<script>
function refreshTicketingCsrf() {
    var match = document.cookie.match(new RegExp('(?:^|; )<?= config('Security')->cookieName; ?>=([^;]*)'));
    if (match) {
        csrfHash = decodeURIComponent(match[1]);
    }
}

$(document).ajaxComplete(refreshTicketingCsrf);

function reply(button, id) {
    var awb = $(button).data('awb');

    $.post('<?= base_url('admin/ticketingNdr/reply'); ?>', {
        [csrfName]: csrfHash,
        id: id,
        awb: awb
    }, function (data) {
        $('#reply1').html(data);
    }).fail(function () {
        alert('fail');
    });
}

function openMailConfirm(button, id) {
    var awb = $(button).data('awb');
    var safeAwb = $('<div/>').text(awb).html();

    $('#mailConfirmId').val(id);
    $('#mailConfirmAwb').val(awb);
    $('#mailConfirmText').html('Do you want to send delayed delivery mail for AWB <strong>' + safeAwb + '</strong>?');
    $('#mailConfirmModal').modal('show');
}

function sendTicketMail() {
    var id = $('#mailConfirmId').val();
    var awb = $('#mailConfirmAwb').val();

    $.ajax({
        type: 'POST',
        url: '<?= base_url('admin/ticketingNdr/send_ticket_mail'); ?>',
        dataType: 'json',
        data: {
            [csrfName]: csrfHash,
            id: id,
            awb: awb
        },
        beforeSend: function () {
            $('#loader').show();
        },
        success: function (res) {
            $('#loader').hide();
            $('#mailConfirmModal').modal('hide');
            refreshTicketingCsrf();

            if (res && res.status) {
                $('#success-body').html(res.message);
                $('#success').modal('show');
            } else {
                alert((res && res.message) ? res.message : 'Mail send failed.');
            }
        },
        error: function () {
            $('#loader').hide();
            $('#mailConfirmModal').modal('hide');
            alert('fail');
        }
    });
}

function submitComment(id) {
    var comment = $('#comment').val();
    var status = $('#status').val();

    $.ajax({
        type: 'POST',
        url: '<?= base_url('admin/ticketingNdr/submit'); ?>',
        data: {
            [csrfName]: csrfHash,
            id: id,
            comment: comment,
            status: status
        },
        beforeSend: function () {
            $('#loader').show();
        },
        success: function (data) {
            $('#loader').hide();
            $('#success').modal('show');
            $('#reply').modal('hide');
            $('#success-body').html(data);
            refreshTicketingCsrf();
        }
    });
}

function view_comment(id) {
    $.post('<?= base_url('admin/ticketingNdr/view_comment'); ?>', {
        [csrfName]: csrfHash,
        id: id
    }, function (data) {
        $('#view-comment1').html(data);
        refreshTicketingCsrf();
    });
}

$('#exportBtn').click(function () {
    window.location.href = '<?= base_url('admin/ticketingNdr/export_ticket_ndr'); ?>';
});

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
            refreshTicketingCsrf();
        },
        error: function () {
            alert('fail');
        }
    });
}
</script>
