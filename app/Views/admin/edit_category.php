<?= view('admin/support_style'); ?>

<div id="page-wrapper" class="support-page">
    <div class="col-md-12 graphs">
        <div class="xs">
            <div class="support-header">
                <h3 class="support-title"><i class="fa fa-pencil"></i> Edit Support Issue Category</h3>
                <?= anchor('admin/support/category', '<i class="fa fa-list"></i> View Issue Category', ['class' => 'btn btn-success btn-icon']); ?>
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
                <?= form_open("admin/support/update_category/{$data->id}", ['class' => 'form-floating']); ?>
                    <fieldset>
                        <div class="form-group">
                            <label class="control-label">Category</label>
                            <?= form_input(['name' => 'title', 'class' => 'form-control', 'value' => set_value('title', $data->title), 'required' => 'required']); ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" name="submit" value="Update" class="btn btn-primary btn-icon">
                                <i class="fa fa-save"></i> Update
                            </button>
                        </div>
                    </fieldset>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
