<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <h3 class="pull-left">Add Role</h3>

            <?= anchor('admin/role','View Role',
                ['class'=>'btn btn-success pull-right']); ?>

            <div class="clearfix"></div>

            <div class="col-lg-12">

                <?php
                $error = session()->getFlashdata('error');
                $error_class = session()->getFlashdata('error_class');

                if ($error):
                ?>

                <div class="alert alert-dismissible <?= $error_class; ?>">
                    <strong><?= $error; ?></strong>
                </div>

                <?php endif; ?>

            </div>

            <div class="clearfix"></div>

            <div class="well1 white">

                <?= form_open_multipart(
                    'admin/role/insert_role',
                    ['class'=>'form-floating']
                ); ?>

                <fieldset>

                    <div class="row">

                        <!-- Role Name -->
                        <div class="form-group col-lg-6">
                            <label class="control-label">
                                Name
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   required="">
                        </div>

                        <!-- Parent Role -->
                        <div class="form-group col-lg-6">

                            <label class="control-label">
                                Parent Role
                            </label>

                            <select name="parent_id"
                                    class="form-control">

                                <option value="">
                                    Top Level
                                </option>

                                <?php foreach($role as $r){ ?>

                                    <option value="<?= $r->id; ?>">
                                        <?= $r->name; ?>
                                    </option>

                                <?php } ?>

                            </select>
                        </div>

                        <!-- Status -->
                        <div class="form-group col-lg-12">

                            <label class="control-label">
                                Status
                            </label>

                            <br>

                            <label class="radio-inline">

                                <input type="radio"
                                       name="status"
                                       value="1"
                                       checked>

                                Active
                            </label>

                            <label class="radio-inline"
                                   style="margin-left:15px;">

                                <input type="radio"
                                       name="status"
                                       value="0">

                                Inactive
                            </label>

                        </div>

                    </div>

                    <div class="form-group">

                        <?= form_submit([
                            'name'=>'submit',
                            'value'=>'Save',
                            'class'=>'btn btn-primary'
                        ]); ?>

                    </div>

                </fieldset>

                <?= form_close(); ?>

            </div>
        </div>
    </div>
</div>
