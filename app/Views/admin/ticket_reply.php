<?= view('admin/support_style'); ?>
<?php
$status = (string) ($data->status ?? '');
$statusClass = $status === 'Closed' ? 'status-closed' : ($status === 'Pending' ? 'status-pending' : 'status-open');
?>

<div id="page-wrapper" class="support-page">
    <div class="col-md-12 graphs">
        <div class="xs">
            <div class="support-header">
                <h3 class="support-title"><i class="fa fa-comments"></i> Customer Support</h3>
                <button onclick="window.history.back()" class="btn btn-success btn-icon"><i class="fa fa-arrow-left"></i> Back</button>
            </div>

            <div class="support-panel" style="margin-bottom:14px;">
                <div class="support-header" style="margin-bottom:12px;">
                    <h4 style="margin:0;"><strong>Ticket Info</strong></h4>
                    <span class="status-pill <?= $statusClass; ?>"><?= esc($status); ?></span>
                </div>

                <div class="support-detail-grid">
                    <div class="support-detail-item">
                        <span class="support-label">User ID</span>
                        <?= $getName($data->login_id); ?>
                    </div>
                    <div class="support-detail-item">
                        <span class="support-label">Created Date</span>
                        <?= ! empty($data->date) ? date('d M Y h:i', strtotime($data->date)) : ''; ?>
                    </div>
                    <div class="support-detail-item">
                        <span class="support-label">Ticket ID</span>
                        <span class="ticket-code"><?= esc($data->tno ?? ''); ?></span>
                    </div>
                    <div class="support-detail-item">
                        <span class="support-label">Category</span>
                        <?= esc($data->category ?? ''); ?>
                    </div>
                    <div class="support-detail-item">
                        <span class="support-label">Sub Category</span>
                        <?= esc($data->sub ?? ''); ?>
                    </div>
                    <div class="support-detail-item wide">
                        <span class="support-label">Subject</span>
                        <?= esc($data->subject ?? ''); ?>
                    </div>
                    <div class="support-detail-item wide">
                        <span class="support-label">Query</span>
                        <?= esc($data->detail ?? ''); ?>
                    </div>
                    <?php if (! empty($data->image)): ?>
                        <div class="support-detail-item">
                            <span class="support-label">Attachment</span>
                            <a href="<?= base_url('uploads/support/' . $data->image); ?>" target="_blank">
                                <img src="<?= base_url('uploads/support/' . $data->image); ?>" class="attachment-thumb" data-toggle="modal" data-target="#request" alt="Attachment">
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($status === 'Closed' && ! empty($data->rating)): ?>
                        <div class="support-detail-item">
                            <span class="support-label">Rating</span>
                            <img src="<?= base_url('assets/images/' . $data->rating . '.png'); ?>" width="50" alt="<?= esc($data->rating); ?>">
                            <?= esc($data->rating); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($status !== 'Closed' && in_array('Add Support', $GLOBALS['permission'])): ?>
                    <div style="margin-top:12px;">
                        <button class="btn btn-success btn-icon" onclick="reply(<?= $data->id; ?>)" data-toggle="modal" data-target="#reply">
                            <i class="fa fa-refresh"></i> Status
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="support-panel">
                <div class="support-header" style="margin-bottom:12px;">
                    <h4 style="margin:0;"><strong>Ticket Reply</strong></h4>
                </div>

                <div class="reply-thread">
                    <?php if (! empty($reply)): ?>
                        <?php foreach ($reply as $row): ?>
                            <?php $isAdmin = ($row->user ?? '') === 'Admin'; ?>
                            <div class="reply-item <?= $isAdmin ? 'admin' : ''; ?>">
                                <div class="reply-meta">
                                    <strong><?= $isAdmin ? 'Admin' : 'User'; ?></strong>
                                    <span><?= ! empty($row->date) ? date('d M Y h:i A', strtotime($row->date)) : ''; ?></span>
                                </div>
                                <div class="reply-body"><?= esc($row->answer ?? ''); ?></div>
                                <?php if (! empty($row->image)): ?>
                                    <a href="<?= base_url('uploads/support/' . $row->image); ?>" target="_blank">
                                        <img src="<?= base_url('uploads/support/' . $row->image); ?>" class="attachment-thumb" alt="Attachment">
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state"><i class="fa fa-comment-o"></i>No reply found.</div>
                    <?php endif; ?>
                </div>

                <?php if ($status !== 'Closed'): ?>
                    <hr>
                    <form action="" method="post" name="myForm" id="upload_form" enctype="multipart/form-data">
                        <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />
                        <input type="hidden" name="ticket_id" value="<?= $data->id; ?>">
                        <input type="hidden" name="user" value="Admin">

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label mb-1">Reply <span class="red">*</span></label>
                                    <textarea name="answer" class="form-control" required="required" rows="5"></textarea>
                                    <span class="alert-danger mt-10" id="error1"></span>
                                </div>

                                <div class="form-group">
                                    <label class="control-label mt-1">Add Attachment</label>
                                    <input type="file" name="image" class="form-control" id="image">
                                    <small>(Max size: 100KB)</small>
                                </div>

                                <?php if (in_array('Add Support', $GLOBALS['permission'])): ?>
                                    <button type="submit" id="submit" value="Submit" class="btn btn-primary btn-icon">
                                        <i class="fa fa-send"></i> Submit
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if (! empty($data->image)): ?>
            <div class="modal fade none-border" id="request">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title"><strong>Attachment</strong></h4>
                        </div>
                        <div class="modal-body">
                            <img src="<?= base_url('uploads/support/' . $data->image); ?>" class="attachment-preview" alt="Attachment">
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="clearfix"></div>
        <div class="loader" id="loader"><img src="<?= base_url('assets/images/loading.gif'); ?>"></div>

        <div class="modal fade none-border" id="success">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header"><button type="button" class="close" onclick="location.reload()">&times;</button><br></div>
                    <div class="modal-body">
                        <div class="row"><div class="col-md-12 text-center" id="success-body" style="color:green"></div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade none-border" id="reply">
            <div class="modal-dialog">
                <div class="modal-content" id="reply1"></div>
            </div>
        </div>

        <script>
        function refreshSupportCsrf() {
            var match = document.cookie.match(new RegExp('(?:^|; )<?= config('Security')->cookieName; ?>=([^;]*)'));
            if (match) {
                csrfHash = decodeURIComponent(match[1]);
            }
        }

        $(document).ajaxComplete(refreshSupportCsrf);

        $(document).ready(function() {
            $('#upload_form').on('submit', function(e) {
                e.preventDefault();
                if (document.myForm.answer.value.trim() === "") {
                    document.getElementById("error1").innerHTML = "Please Enter Reply!";
                    return false;
                }

                document.getElementById("error1").innerHTML = "";
                refreshSupportCsrf();
                $(this).find('input[name="' + csrfName + '"]').val(csrfHash);

                $.ajax({
                    url: "<?= base_url('admin/support/submit_reply'); ?>",
                    method: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('#loader').css('display', 'block');
                    },
                    success: function(data) {
                        $('#loader').css('display', 'none');
                        alert(data);
                        location.reload();
                    }
                });
            });
        });

        function reply(id) {
            refreshSupportCsrf();
            $.ajax({
                type: "POST",
                url: "<?= base_url('admin/support/reply'); ?>",
                data: {[csrfName]: csrfHash, id: id},
                success: function(data) {
                    $('#reply1').html(data);
                },
                error: function() {
                    alert('fail');
                }
            });
        }

        function submit(id) {
            refreshSupportCsrf();
            var status = $('#status').val();
            $.ajax({
                type: "POST",
                url: "<?= base_url('admin/support/submit'); ?>",
                data: {[csrfName]: csrfHash, id: id, status: status},
                beforeSend: function() {
                    $('#loader').css('display', 'block');
                },
                success: function(data) {
                    $('#loader').css('display', 'none');
                    $('#reply').modal('toggle');
                    window.history.back();
                },
                error: function() {
                    alert('fail');
                }
            });
        }
        </script>
    </div>
</div>
