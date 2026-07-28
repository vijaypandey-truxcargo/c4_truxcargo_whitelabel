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
                <h3 class="support-title"><i class="fa fa-sitemap"></i> Support Issue Sub Category</h3>
                <?php if (in_array('Add Support', $GLOBALS['permission'])): ?>
                    <?= anchor('admin/support/add_sub_category', '<i class="fa fa-plus"></i> Add Sub Category', ['class' => 'btn btn-danger btn-icon']); ?>
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
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th width="190">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($count)) { $count = 0; } ?>
                            <?php if (! empty($key)): ?>
                                <?php foreach ($key as $row): $count++; ?>
                                    <tr>
                                        <td><?= $count; ?></td>
                                        <td><?= esc($row->category ?? ''); ?></td>
                                        <td><strong><?= esc($row->title ?? ''); ?></strong></td>
                                        <td>
                                            <?php if (in_array('Add Support', $GLOBALS['permission'])): ?>
                                                <div class="support-actions">
                                                    <?= anchor("admin/support/edit_sub_category/{$row->id}", '<i class="fa fa-pencil"></i> Edit', ['class' => 'btn btn-primary btn-sm btn-icon']); ?>
                                                    <?= form_open('admin/support/delete_sub_category', ['onsubmit' => 'return validate(this);']); ?>
                                                        <?= form_hidden('id', $row->id); ?>
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
                                    <td colspan="4" class="empty-state"><i class="fa fa-sitemap"></i>No sub category found.</td>
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
