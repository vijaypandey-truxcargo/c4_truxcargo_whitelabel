<?= view('admin/support_style'); ?>

<div id="page-wrapper" class="support-page">
    <div class="col-md-12 graphs">
        <div class="xs">
            <div class="support-header">
                <h3 class="support-title"><i class="fa fa-file-text-o"></i> Add Terms & Policy</h3>
                <?= anchor('admin/support/terms', '<i class="fa fa-list"></i> View Terms & Policy', ['class' => 'btn btn-success btn-icon']); ?>
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
                <?= form_open('admin/support/insert_terms', ['class' => 'form-floating']); ?>
                    <fieldset>
                        <div class="form-group">
                            <label class="control-label">Section</label>
                            <select name="category" required="required" class="form-control">
                                <option value="Terms and Conditions" <?= set_value('category') === 'Terms and Conditions' ? 'selected="selected"' : ''; ?>>Terms and Conditions</option>
                                <option value="Privacy Policy" <?= set_value('category') === 'Privacy Policy' ? 'selected="selected"' : ''; ?>>Privacy Policy</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Title</label>
                            <?= form_input(['name' => 'title', 'class' => 'form-control', 'value' => set_value('title'), 'required' => 'required']); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Description</label>
                            <textarea name="detail" class="ckeditor form-control"><?= esc(set_value('detail')); ?></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" name="submit" value="Save" class="btn btn-primary btn-icon">
                                <i class="fa fa-save"></i> Save
                            </button>
                        </div>
                    </fieldset>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
