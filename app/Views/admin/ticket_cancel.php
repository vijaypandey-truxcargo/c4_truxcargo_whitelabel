<?= view('admin/support_style'); ?>

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
                    <li class="active"><a href="<?= base_url('admin/support/cancel'); ?>"><i class="fa fa-ban"></i> Cancel</a></li>
                    <li><a href="<?= base_url('admin/support/closed'); ?>"><i class="fa fa-check-circle"></i> Closed</a></li>
                </ul>

                <div class="support-toolbar">
                    <div><span class="status-pill status-cancel">Cancel</span></div>
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
                                <th width="42"><input type="checkbox" name="select-all" id="select-all" /></th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Ticket No</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($open)): ?>
                                <?php foreach ($open as $row): ?>
                                    <tr>
                                        <td><input type="checkbox" name="ticket" class="mani" value="<?= $row->id; ?>"></td>
                                        <td><?= ! empty($row->date) ? date('d M Y h:i', strtotime($row->date)) : ''; ?></td>
                                        <td><?= $getName($row->login_id); ?></td>
                                        <td><span class="ticket-code"><?= esc($row->tno ?? ''); ?></span></td>
                                        <td><?= esc($row->category ?? ''); ?></td>
                                        <td><?= esc($row->sub ?? ''); ?></td>
                                        <td><div class="text-truncate-soft" title="<?= esc($row->subject ?? ''); ?>"><?= esc($row->subject ?? ''); ?></div></td>
                                        <td><span class="status-pill status-cancel"><?= esc($row->status ?? ''); ?></span></td>
                                        <td>
                                            <div class="support-actions">
                                                <a class="btn btn-primary btn-sm btn-icon" href="<?= base_url("admin/support/reply_ticket/{$row->id}"); ?>">
                                                    <i class="fa fa-pencil-square-o"></i> Reply
                                                </a>
                                                <?php if (in_array('Add Support', $GLOBALS['permission'])): ?>
                                                    <button class="btn btn-success btn-sm btn-icon" onclick="reply(<?= $row->id; ?>)" data-toggle="modal" data-target="#reply">
                                                        <i class="fa fa-refresh"></i> Status
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="empty-state"><i class="fa fa-ban"></i>No cancel ticket found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="support-pagination"><?= $links; ?></div>
            </div>
        </div>

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

        <div class="content-area-footer" id="footer">
            <div class="row">
                <div class="col-lg-6 text-left">
                    <label class="checked"><i class="fa fa-minus-square-o red" style="font-size: 18px" onClick="minus(this)"></i></label>
                    <span class="red" id="al"> &nbsp; All Tickets Selected </span>
                </div>
                <div class="col-lg-6 text-right">
                    <button class="btn btn-default btn-icon" style="margin-right: 10px;" id="pack" onclick="mark('Pending')">
                        <i class="fa fa-clock-o"></i> Pending
                    </button>
                    <button class="btn btn-danger btn-icon" onclick="mark('Closed')">
                        <i class="fa fa-check"></i> Closed
                    </button>
                </div>
            </div>
        </div>

        <?= view('admin/ticket_list_scripts'); ?>
    </div>
</div>
