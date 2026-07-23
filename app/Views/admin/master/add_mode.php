<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <h3 class="pull-left">Add Mode</h3>
            <?= anchor('admin/mode','View mode',['class'=>'btn btn-success pull-right']);?>

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
                <?= form_open_multipart('admin/mode/insert_mode', ['class' => 'form-floating']); ?>
                <fieldset>
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label">Name</label>
                            <input type="text" name="name" class="form-control" required="">
                        </div>

                        <div class="form-group">
                            <label class="control-label">Status</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="status" value="1" checked> Active
                            </label>
                            <label class="radio-inline" style="margin-left:15px;">
                                <input type="radio" name="status" value="0"> Inactive
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= form_submit(['name' => 'submit', 'value' => 'Save', 'class' => 'btn btn-primary']); ?>
                    </div>

                </fieldset>
                <?= form_close(); ?>


        </div>
    </div>
</div>
