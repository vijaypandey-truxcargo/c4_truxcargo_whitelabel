<?= view('admin/support_style'); ?>

<script>
function validate(form) {
    return confirm('Do you really want to delete this record ?');
}
</script>

<div id="page-wrapper" class="support-page">
    <div class="col-md-12 graphs">
        <div class="xs">
            <div class="support-header">
                <h3 class="support-title"><i class="fa fa-file-text-o"></i> Terms & Policy</h3>
                <?php if (in_array('Add Additional Section', $GLOBALS['permission'])): ?>
                    <?= anchor('admin/support/add_terms', '<i class="fa fa-plus"></i> Add Terms & Policy', ['class' => 'btn btn-danger btn-icon']); ?>
                <?php endif; ?>
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
                <div class="table-responsive">
                    <table class="table support-table">
                        <thead>
                            <tr>
                                <th width="70">#</th>
                                <th>Section</th>
                                <th>Title</th>
                                <th>Detail</th>
                                <th width="190">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 0; ?>
                            <?php if (! empty($data)): ?>
                                <?php foreach ($data as $row): $count++; $rowId = (int) ($row->id ?? 0); ?>
                                    <tr>
                                        <td><?= $count; ?></td>
                                        <td><?= esc($row->category ?? ''); ?></td>
                                        <td><strong><?= esc($row->title ?? ''); ?></strong></td>
                                        <td><?= esc(substr(strip_tags($row->detail ?? ''), 0, 200)); ?></td>
                                        <td>
                                            <?php if (in_array('Add Additional Section', $GLOBALS['permission'])): ?>
                                                <div class="support-actions">
                                                    <?= anchor("admin/support/edit_terms/{$rowId}", '<i class="fa fa-pencil"></i> Edit', ['class' => 'btn btn-primary btn-sm btn-icon']); ?>
                                                    <?= form_open('admin/support/delete_terms', ['onsubmit' => 'return validate(this);']); ?>
                                                        <?= form_hidden('id', $rowId); ?>
                                                        <button type="submit" name="submit" value="Delete" class="btn btn-danger btn-sm btn-icon">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    <?= form_close(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="empty-state"><i class="fa fa-file-text-o"></i>No terms or policy found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
