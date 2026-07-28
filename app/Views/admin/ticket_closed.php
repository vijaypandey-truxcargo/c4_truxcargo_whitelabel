<?= view('admin/support_style'); ?>
<script>
function validate(form) {
    return confirm('Do you really want to Reopen this ticket ?');
}
</script>

<div id="page-wrapper" class="support-page">
    <div class="col-md-12 graphs">
        <div class="xs">
            <div class="support-header">
                <h3 class="support-title"><i class="fa fa-life-ring"></i> Customer Support</h3>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <?php
                    $error = session()->getFlashdata('error');
                    $error_class = session()->getFlashdata('error_class');
                    if ($error): ?>
                        <div class="alert alert-dismissible <?= esc($error_class); ?>">
                            <strong><?= esc($error); ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="support-panel">
                <ul class="nav nav-tabs support-tabs">
                    <li><a href="<?= base_url('admin/support/ticket'); ?>"><i class="fa fa-inbox"></i> Open</a></li>
                    <li><a href="<?= base_url('admin/support/pending'); ?>"><i class="fa fa-clock-o"></i> Pending</a></li>
                    <li><a href="<?= base_url('admin/support/cancel'); ?>"><i class="fa fa-ban"></i> Cancel</a></li>
                    <li class="active"><a href="<?= base_url('admin/support/closed'); ?>"><i class="fa fa-check-circle"></i> Closed</a></li>
                </ul>

                <div class="support-toolbar">
                    <div><span class="status-pill status-closed">Closed</span></div>
                    <div class="support-search">
                        <?= form_open('', ['method' => 'get']); ?>
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" value="<?= esc($search ?? ''); ?>" placeholder="Search by ticket no" required="required">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary" name="submit" title="Search">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        <?= form_close(); ?>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table support-table" id="example">
                        <thead>
                            <tr>
                                <th width="70">S.No</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Ticket No</th>
                                <th>Category</th>
                                <th>Subject</th>
                                <th>Rating</th>
                                <th width="160">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($count)) { $count = 0; } ?>
                            <?php if (! empty($closed)): ?>
                                <?php foreach ($closed as $row): $count++; ?>
                                    <tr>
                                        <td><?= $count; ?></td>
                                        <td><?= ! empty($row->date) ? date('d M Y h:i', strtotime($row->date)) : ''; ?></td>
                                        <td><?= $getName($row->login_id); ?></td>
                                        <td><span class="ticket-code"><?= esc($row->tno ?? ''); ?></span></td>
                                        <td><?= esc($row->category ?? ''); ?></td>
                                        <td><div class="text-truncate-soft" title="<?= esc($row->subject ?? ''); ?>"><?= esc($row->subject ?? ''); ?></div></td>
                                        <td>
                                            <?php if (! empty($row->rating)): ?>
                                                <img src="<?= base_url('assets/images/' . $row->rating . '.png'); ?>" width="50" alt="<?= esc($row->rating); ?>">
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="support-actions">
                                                <a class="btn btn-primary btn-sm btn-icon" href="<?= base_url("admin/support/reply_ticket/{$row->id}"); ?>">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                <?php if (in_array('Add Support', $GLOBALS['permission'])): ?>
                                                    <?= form_open('admin/support/reopen', ['onsubmit' => 'return validate(this);']); ?>
                                                        <?= form_hidden('id', $row->id); ?>
                                                        <button type="submit" name="submit" value="Reopen" class="btn btn-danger btn-sm btn-icon">
                                                            <i class="fa fa-undo"></i> Reopen
                                                        </button>
                                                    <?= form_close(); ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="empty-state"><i class="fa fa-check-circle"></i>No closed ticket found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="support-pagination"><?= $links; ?></div>
            </div>
        </div>
    </div>
</div>
