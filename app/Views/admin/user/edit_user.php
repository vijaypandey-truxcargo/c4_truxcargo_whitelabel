<?php
// Convert data to object if array
if (is_array($data)) $data = (object)$data;

// assigned hubs array
$assigned_hubs = [];
if (!empty($data->assign_hub)) {
    $assigned_hubs = explode(",", $data->assign_hub);
}
?>

<style>
.form-group { margin-right: 10px !important; }
#topButtonsAbs .btn { margin-left: 6px; }
.hub-container { background: #fff; border: 1px solid #ccc; padding: 10px 0 0 0; border-radius: 2px; }
.hub-title { font-weight: bold; font-size: 15px; text-transform: uppercase; margin: 10px 15px; border-bottom: 2px solid #000; padding-bottom: 5px; }
.hub-table { border: 1px solid #ccc; border-top: none; margin: 0 10px 10px 10px; }
.hub-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 15px; border-bottom: 1px solid #ddd; font-size: 14px; background-color: #fff; }
.hub-row:hover { background-color: #f9f9f9; }
.hub-row:last-child { border-bottom: none; }
.hub-header { background-color: #f5f5f5; font-weight: bold; }
.hub-name { flex: 1; }
.hub-checkbox { width: 80px; text-align: center; }
.disabled-tab { pointer-events:none; opacity:0.4; }
</style>

<div id="page-wrapper">
<div class="col-md-12 graphs">
<div class="xs">

<div style="margin-bottom:15px;display:flex;justify-content:space-between">
    <h3>Edit User</h3>
    <?= anchor('admin/users/', 'List Users', ['class'=>'btn btn-danger']); ?>
</div>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert <?= session()->getFlashdata('error_class') ?>">
        <strong><?= session()->getFlashdata('error') ?></strong>
    </div>
<?php endif; ?>

<div class="well1 white">

<?= form_open_multipart('admin/users/update_user/'.$data->id, ['class'=>'form-horizontal', 'id'=>'editUserForm']) ?>

<ul class="nav nav-tabs">
    <li class="active"><a class="nav-link active" data-toggle="tab" href="#profile">👤 PROFILE</a></li>
    <li><a class="nav-link disabled-tab" id="hub_tabs" data-toggle="tab" href="#hubs">🏠 HUBS</a></li>
</ul>

<div class="tab-content">
<div class="tab-pane fade in active" id="profile">

<br>
<div class="row">

<div class="col-md-6">

    <div class="form-group">
        <label>Name *</label>
        <input type="text" name="userName" class="form-control" required
               value="<?= $data->userName ?>">
    </div>

    <div class="form-group">
        <label>Email *</label>
        <input type="email" name="userEmail" class="form-control" required
               value="<?= $data->userEmail ?>">
    </div>

    <div class="form-group">
        <label>Phone *</label>
        <input type="text" name="userPhone" class="form-control" required
               value="<?= $data->userPhone ?>">
    </div>

    <div class="form-group">
        <label>Role *</label>
        <select name="userRole" class="form-control" required>
            <option value="">Select Role</option>
            <?php foreach($role as $r): ?>
                <option value="<?= $r->id ?>" <?= ($data->role == $r->id ? 'selected':'') ?>>
                    <?= $r->name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="birthDate" class="form-control"
               value="<?= $data->dob ?>">
    </div>

    <div class="form-group">
        <label>Joining Date</label>
        <input type="date" name="joinDate" class="form-control"
               value="<?= $data->joining_date ?>">
    </div>
    <?php
    $selected_dept = [];
    // print_r($data->default_user_hub); die();
    if (!empty($data->default_user_hub)) {
        $selected_dept = explode(",", $data->default_user_hub);
    }
    ?>

</div>

<div class="col-md-6">

    <div class="form-group">
        <label>KYC Type</label>
        <select name="kycType" class="form-control">
            <option value="">Select</option>
            <?php foreach($kyc as $k): ?>
                <option value="<?= $k->id ?>" <?= ($data->kyc_type == $k->id ? 'selected':'') ?>>
                    <?= $k->name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>KYC Number</label>
        <input type="text" name="kycNo" class="form-control"
               value="<?= $data->kyc_no ?>">
    </div>

    <div class="form-group">
        <label>KYC Document</label>
        <input type="file" name="kycDoc" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf">
        <?php if($data->kyc_document): ?>
            <br><a href="<?= base_url('uploads/users/kyc/'.$data->kyc_document) ?>" target="_blank">View KYC Document</a>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>Profile Photo</label>
        <input type="file" name="profilePhoto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        <?php if($data->profile_photo): ?>
            <br><img src="<?= base_url('uploads/users/profile_photo/'.$data->profile_photo) ?>" width="80" alt="Profile Photo">
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label>Default User Department</label>
        <select name="default_user_hub[]"  class="form-control select2" multiple  data-placeholder="Select Department">
            <!-- <option></option> -->
            <option value="all">Select All</option>
            <?php
            $departments = ["BILLING", "CUSTOMER SERVICE", "OPERATIONS"];

            foreach ($departments as $dept): ?>
                <option value="<?= $dept ?>"
                    <?= in_array($dept, $selected_dept) ? "selected" : "" ?>>
                    <?= $dept ?>
                </option>
            <?php endforeach; ?>

        </select>
    </div>


	<div class="form-group" style="<?= ($data->id == 30) ? 'display:none' : 'display:block' ?>">
		<label>New Password (optional)</label>
		<div style="position: relative;">
			<input type="password" id="password" name="password" class="form-control" value="<?= $data->password ?>">
			<button type="button" onclick="togglePassword()" 
					style="position:absolute; right:10px; top:50%; transform:translateY(-50%); border:none; background:none; cursor:pointer;">
				Show
			</button>
		</div>
	</div>

    <div class="form-group">
        <label>Status</label><br>
        <label><input type="radio" name="status" value="1" <?= ($data->status==1?'checked':'') ?>> Active</label>
        <label><input type="radio" name="status" value="0" <?= ($data->status==0?'checked':'') ?>> Inactive</label>
    </div>

</div>

</div>

<div class="text-right">
    <button type="button" class="btn btn-primary" id="goToHubs">Next →</button>
</div>

</div>

<div class="tab-pane fade" id="hubs">

<div class="hub-container">

<h4 class="hub-title">Select Hubs</h4>
<div id="hubErrorMsg"></div>

<div class="hub-table">

<div class="hub-row hub-header">
    <div class="hub-name"><b>Select All</b></div>
    <div class="hub-checkbox"><input type="checkbox" id="selectAllHubs"></div>
</div>

<?php
$assigned_hubs = explode(',', $data->user_assigned_hub);
foreach($hubs as $h):
?>
<div class="hub-row">
    <div class="hub-name"><?= $h->name ?></div>
    <div class="hub-checkbox">
        <input type="checkbox"
            name="userHubs[]"
            value="<?= $h->id ?>"
            <?= in_array($h->id, $assigned_hubs) ? 'checked' : '' ?>>
    </div>
</div>
<?php endforeach; ?>


</div>
</div>

<br>

<div class="row">
    <div class="col-md-6">
        <button type="button" class="btn btn-secondary" id="goToProfile">← Back</button>
    </div>
    <div class="col-md-6 text-right">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</div>

</div>

</div>
<?= form_close(); ?>

</div>
</div>
</div>


<script>
	document.getElementById('selectAllHubs').addEventListener('change', function() {
		document.querySelectorAll('input[name="userHubs[]"]').forEach(box => {
			box.checked = this.checked;
		});
	});
	document.getElementById('goToProfile').addEventListener('click', function() {
		document.querySelector('a[href="#profile"]').click();
	});
	$(document).ready(function() {
		$(".select2").select2({
			placeholder: "Select hubs",
			allowClear: true,
			closeOnSelect: false
		});
		$("#hub_tabs").addClass("disabled-tab").css("pointer-events", "none").css("opacity", "0.5")
		$(".select2").on("change.select2", function() {
			removeError(this);
		});
		$("#profile input, #profile select").on("keyup change", function() {
			removeError(this);
		});
		$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
			if($(e.target).attr("href") === "#profile") {
				$(".select2").select2('destroy').select2({
					placeholder: "Select hubs",
					allowClear: true,
					closeOnSelect: false
				});
			}
		});
		$('.select2').on('select2:select', function(e) {
			if(e.params.data.id === "all") {
				let all = [];
				$(this).find('option').each(function() {
					if($(this).val() !== "all" && $(this).val() !== "") {
						all.push($(this).val());
					}
				});
				$(this).val(all).trigger("change");
			}
		});
	});

	function showError(input, message) {
		removeError(input);
		let error = document.createElement("small");
		error.classList.add("text-danger", "error-text");
		error.innerText = message;
		input.classList.add("is-invalid");
		input.parentNode.appendChild(error);
	}

	function removeError(input) {
		input.classList.remove("is-invalid");
		let error = input.parentNode.querySelector(".error-text");
		if(error) error.remove();
	}

	function isValidEmail(email) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
	}

	function isValidMobile(mobile) {
		return /^[0-9]{10}$/.test(mobile);
	}

	function validateProfileTab() {
		let valid = true;
		let name = document.querySelector('input[name="userName"]');
		let email = document.querySelector('input[name="userEmail"]');
		let phone = document.querySelector('input[name="userPhone"]');
		let role = document.querySelector('select[name="userRole"]');
		let pass = document.querySelector('input[name="password"]');
		let cpass = document.querySelector('input[name="confirmPassword"]');
		if(name.value.trim() === "") {
			showError(name, "Please enter name");
			valid = false;
		}
		if(!isValidEmail(email.value.trim())) {
			showError(email, "Enter valid email");
			valid = false;
		}
		if(!isValidMobile(phone.value.trim())) {
			showError(phone, "Enter valid 10-digit number");
			valid = false;
		}
		if(role.value === "") {
			showError(role, "Please select role");
			valid = false;
		}
		// if(pass.value.trim() === "") {
		// 	showError(pass, "Enter password");
		// 	valid = false;
		// }
		// if(cpass.value.trim() === "" || pass.value !== cpass.value) {
		// 	showError(cpass, "Passwords do not match");
		// 	valid = false;
		// }
		return valid;
	}
	document.getElementById('goToHubs').addEventListener('click', function() {
		if(validateProfileTab()) {
			$("#hub_tabs").removeClass("disabled-tab").css("pointer-events", "auto").css("opacity", "1");
			document.querySelector('a[href="#hubs"]').click();
		}
	});

	function validateHubsTab() {
		document.getElementById("hubErrorMsg").innerHTML = "";
		let checked = document.querySelectorAll('input[name="userHubs[]"]:checked');
		if(checked.length === 0) {
			document.getElementById("hubErrorMsg").innerHTML = "<small class='text-danger'>Please select at least one hub</small>";
			return false;
		}
		return true;
	}
	document.getElementById('submitUserForm').addEventListener('click', function(e) {
		if(!validateHubsTab()) {
			e.preventDefault();
			return false;
		}
	});
	document.getElementById("selectAllHubs").addEventListener("change", function() {
		const isChecked = this.checked;
		document.querySelectorAll("input[name='userHubs[]']").forEach(cb => {
			cb.checked = isChecked;
		});
		document.getElementById("hubErrorMsg").innerHTML = "";
	});
	document.querySelectorAll("input[name='userHubs[]']").forEach(cb => {
		cb.addEventListener("change", function() {
			document.getElementById("hubErrorMsg").innerHTML = "";
		});
	});

	function validateHubs() {
		const hubs = document.querySelectorAll("input[name='userHubs[]']:checked");
		if(hubs.length === 0) {
			document.getElementById("hubErrorMsg").innerHTML = "<small class='text-danger'>Please select at least one hub</small>";
			return false;
		}
		return true;
	}
	document.getElementById("submitUserForm").addEventListener("click", function(e) {
		if(!validateHubs()) {
			e.preventDefault();
		}
	});
	
	function togglePassword() {
        const passwordField = document.getElementById("password");
        const button = event.target;
    
        if (passwordField.type === "password") {
            passwordField.type = "text";
            button.textContent = "Hide";
        } else {
            passwordField.type = "password";
            button.textContent = "Show";
        }
    }
	</script>
