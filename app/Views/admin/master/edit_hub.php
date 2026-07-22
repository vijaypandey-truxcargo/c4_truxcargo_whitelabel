<?php if(is_array($data)) $data = (object)$data;  //echo '<pre>'; print_r($data);  ?>

<style>
.disabled { background: #f1f1f1; }
</style>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <h3 class="pull-left">Edit Hub</h3>
            <?= anchor('admin/master/hub_masters','View Hub',['class'=>'btn btn-success pull-right']); ?>

            <div class="col-lg-12">
                <?php
                $error = session()->getFlashdata('error');
                $error_class = session()->getFlashdata('error_class');
                if($error): ?>
                    <div class="alert alert-dismissible <?= $error_class; ?>">
                        <strong><?= $error; ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <div class="clearfix"></div>

            <div class="well1 white">

                <?= form_open_multipart("admin/master/update_hub/{$data->id}", ['class'=>'form-floating']) ?>

                <fieldset>

                    <div class="form-group">
                        <label class="control-label">Code</label>
                        <input type="text" name="code"
                               value="<?= set_value('code', $data->code) ?>"
                               class="form-control1" required="">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Name</label>
                        <input type="text" name="name"
                               value="<?= set_value('name', $data->name) ?>"
                               class="form-control1" required="">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Hub Type</label>
                        <select class="form-control" name="type" id="hub_type" required>
                            <option value="">SELECT...</option>
                            <?php
                                $types = [
                                    "HUB","FRANCHISE","BRANCH","DISTRICT HUB",
                                    "MASTER DISTRICT HUB","ROUTING HUB",
                                    "STATE CAPITAL HUB","ZONAL HUB"
                                ];
                                foreach($types as $t){
                                    $sel = ($data->type == $t) ? "selected" : "";
                                    echo "<option value='$t' $sel>$t</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Phone</label>
                        <input type="text" name="phone"
                               value="<?= set_value('phone', $data->phone) ?>"
                               class="form-control1" required="">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <input type="email" name="email_id"
                               value="<?= set_value('email_id', $data->email_id) ?>"
                               class="form-control1" required="">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Company</label>
                        <select class="form-control" name="company" id="company_id" required>
                            <option value="">SELECT...</option>
                            <?php foreach ($companies as $c): ?>
                                <option value="<?= $c->id ?>" <?= ($data->company_id == $c->id) ? 'selected' : '' ?>>
                                    <?= $c->code ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Address</label>
                        <textarea class="form-control" name="address" rows="3"><?= set_value('address', $data->address) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Status</label><br>

                        <label class="radio-inline">
                            <input type="radio" name="status" value="1"
                                <?= ($data->status == 1) ? "checked" : "" ?>> Active
                        </label>

                        <label class="radio-inline" style="margin-left:15px;">
                            <input type="radio" name="status" value="0"
                                <?= ($data->status == 0) ? "checked" : "" ?>> Inactive
                        </label>
                    </div>

                    <div class="form-group">
                        <?= form_submit(['name'=>'Update','value'=>'Update','class'=>'btn btn-primary']) ?>
                    </div>

                </fieldset>

                <?= form_close(); ?>

            </div>
        </div>
    </div>
</div>
