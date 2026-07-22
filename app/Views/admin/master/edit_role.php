<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <h3 class="pull-left">Edit role</h3>
            <?= anchor('admin/role','View Role',['class'=>'btn btn-success pull-right']);?>

            <div class="clearfix"></div>

            <!-- Alert Message -->
            <div class="col-lg-12" style="margin-top:10px;">
                <?php
                    $error = $this->session->flashdata('error');
                    $error_class = $this->session->flashdata('error_class');
                    if ($error):
                ?>
                    <div class="alert alert-dismissible <?= $error_class; ?>">
                        <strong><?= $error; ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <div class="clearfix"></div>

            <div class="well1 white">
                <?= form_open_multipart('admin/role/update_role/'.$data->id, ['class' => 'form-floating']); ?>
                <fieldset>

                    <div class="row">

                        <!-- NAME -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Role Name</label>
                                <input type="text" name="name" class="form-control" value="<?= $data->name; ?>" required="">
                            </div>
                        </div>
                        <div class="col-md-6">

                        <div class="form-group">

                            <label class="control-label">
                                Parent Role
                            </label>

                            <select name="parent_id"
                                    class="form-control">

                                <option value="">
                                    Top Level
                                </option>

                                <?php foreach($role as $r){ ?>

                                    <?php
                                        if($r->id == $data->id){
                                            continue;
                                        }
                                    ?>

                                    <option value="<?= $r->id; ?>"
                                        <?= ($data->parent_id == $r->id)
                                            ? 'selected'
                                            : ''; ?>>

                                        <?= $r->name; ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                    </div>

                        <!-- STATUS -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Status</label><br>

                                <label class="radio-inline">
                                    <input type="radio" name="status" value="1" <?= ($data->status == 1 ? 'checked' : ''); ?>> Active
                                </label>

                                <label class="radio-inline" style="margin-left:15px;">
                                    <input type="radio" name="status" value="0" <?= ($data->status == 0 ? 'checked' : ''); ?>> Inactive
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- SAVE BUTTON -->
                    <div class="form-group">
                        <?= form_submit(['name' => 'submit', 'value' => 'Update', 'class' => 'btn btn-primary']); ?>
                    </div>

                </fieldset>
                <?= form_close(); ?>
            </div>

        </div>
    </div>
</div>
