<style>
.form-group {
	margin-right: 10px !important;
}

#topButtonsAbs .btn {
	margin-left: 6px;
}

.hub-container {
	background: #fff;
	border: 1px solid #ccc;
	padding: 10px 0 0 0;
	border-radius: 2px;
}

.hub-title {
	font-weight: bold;
	font-size: 15px;
	text-transform: uppercase;
	margin: 10px 15px;
	border-bottom: 2px solid #000;
	padding-bottom: 5px;
}

.hub-table {
	border: 1px solid #ccc;
	border-top: none;
	margin: 0 10px 10px 10px;
}

.hub-row {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 6px 15px;
	border-bottom: 1px solid #ddd;
	font-size: 14px;
	background-color: #fff;
}

.hub-row:hover {
	background-color: #f9f9f9;
}

.hub-row:last-child {
	border-bottom: none;
}

.hub-header {
	background-color: #f5f5f5;
	font-weight: bold;
}

.hub-name {
	flex: 1;
}

.hub-checkbox {
	width: 80px;
	text-align: center;
}
</style>

<div id="page-wrapper">
	<div class="col-md-12 graphs">
		<div class="xs">

        <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">

            <!-- LEFT: TITLE -->
            <h3 style="margin:0;">Add User</h3>

            <!-- RIGHT SIDE BUTTONS -->
            <div style="display:flex; align-items:center; gap:10px;">

                <?php if (in_array("Add Access Control", $GLOBALS['permission'])): ?>
                    <?= anchor('admin/users/', 'List User', [
                        'class' => 'btn btn-danger'
                    ]); ?>
                <?php endif; ?>

                <?= form_close(); ?>

            </div>
        </div>
        <div class="clearfix"></div>
        <!-- ERROR MESSAGE -->
        <div class="row">
            <div class="col-lg-12">
                <?php
                $error = session()->getFlashdata('error');
                $error_class = session()->getFlashdata('error_class');
                if ($error): ?>
                    <div class="alert alert-dismissible <?= $error_class; ?>">
                        <strong><?= $error; ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
			<div class="well1 white"> <?= form_open_multipart('admin/users/insert', ['class' => 'form-horizontal', 'id' => 'addUserForm']) ?> <ul class="nav nav-tabs" id="userTab" role="tablist">
					<li class="nav-item active">
						<a class="nav-link active" data-toggle="tab" href="#profile" role="tab">👤 PROFILE</a>
					</li>
					<li class="nav-item">
						<a class="nav-link disabled-tab" id="hub_tabs" data-toggle="tab" href="#hubs" role="tab">🏠 HUBS</a>
					</li>
				</ul>
				<div class="tab-content" id="userTabContent">
					<div class="tab-pane fade in active" id="profile" role="tabpanel">
						<br>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-style">
									<label>Name <span class="text-danger">*</span></label>
									<input type="text" name="userName" class="form-control" required>
								</div>
								<div class="form-group">
									<label>Email ID <span class="text-danger">*</span></label>
									<input type="email" name="userEmail" class="form-control" required>
								</div>
								<div class="form-group">
									<label>Contact No. <span class="text-danger">*</span></label>
									<input type="text" name="userPhone" class="form-control" required>
								</div>
								<div class="form-group">
									<label>Role <span class="text-danger">*</span></label>
									<select name="userRole" class="form-control" required>
										<option value="">Select Role</option>
                              <?php foreach($role as $user_role) { ?>
                                 <option value="<?php echo $user_role->id ?>"><?php echo $user_role->name ?></option>
                              <?php } ?>
									</select>
								</div>
								<div class="form-group">
									<label>Birth Date</label>
									<input type="date" name="birthDate" class="form-control">
								</div>
								<div class="form-group">
									<label>Joining Date</label>
									<input type="date" name="joinDate" class="form-control">
								</div>
								<div class="form-group">
									<label>Default User Department</label>
									<select name="user_assign_hub[]" class="form-control select2" multiple data-placeholder="Select hubs">
										<option></option>
										<option value="all">Select All</option>
										<option value="BILLING">BILLING</option>
										<option value="CUSTOMER SERVICE">CUSTOMER SERVICE</option>
										<option value="OPERATIONS">OPERATIONS</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>KYC Type</label>
									<select name="kycType" class="form-control">
										<option value="all">Select KYC Type</option>
										<?php foreach($kyc as $kyc_name) { ?>
                                 <option value="<?php echo $kyc_name->id ?>"><?php echo $kyc_name->name ?></option>
                              <?php } ?>
									</select>
								</div>
								<div class="form-group">
									<label>KYC No</label>
									<input type="text" name="kycNo" class="form-control">
								</div>
								<div class="form-group">
									<label>KYC Document</label>
									<input type="file" name="kycDoc" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf">
								</div>
								<div class="form-group">
									<label>Profile Photo</label>
									<input type="file" name="profilePhoto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
								</div>
								<div class="form-group">
									<label>Password <span class="text-danger">*</span></label>
									<input type="password" name="password" class="form-control" required>
								</div>
								<div class="form-group">
									<label>Confirm Password <span class="text-danger">*</span></label>
									<input type="password" name="confirmPassword" class="form-control" required>
								</div>
								<div class="form-group">
									<label>Status</label><br>
									<label class="radio-inline">
										<input type="radio" name="status" value="1" checked> Active </label>
									<label class="radio-inline">
										<input type="radio" name="status" value="0"> Inactive </label>
								</div>
							</div>
						</div>
						<div class="text-right">
							<button type="button" class="btn btn-primary" id="goToHubs">Next →</button>
						</div>
						<br>
					</div>
					<div class="tab-pane fade" id="hubs" role="tabpanel">
						<div class="hub-container mt-4">
							<h4 class="hub-title">SELECT HUB</h4>
							<div id="hubErrorMsg"></div>
							<div class="hub-table">
								<div class="hub-row hub-header">
									<div class="hub-name"><b>SELECT ALL</b></div>
									<div class="hub-checkbox">
										<input type="checkbox" id="selectAllHubs">
									</div>
								</div> <?php
                        foreach ($hubs as $hub): ?> <div class="hub-row">
									<div class="hub-name"><?= $hub->name; ?></div>
									<div class="hub-checkbox">
										<input type="checkbox" name="userHubs[]" value="<?= $hub->id; ?>">
									</div>
								</div> <?php endforeach; ?>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-6">
								<button type="button" class="btn btn-secondary" id="goToProfile">← Back</button>
							</div>
							<div class="col-md-6 text-right">
								<button type="submit" class="btn btn-primary" id="submitUserForm">Save</button>
							</div>
						</div>
					</div>
				</div> <?= form_close(); ?>
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
		if(pass.value.trim() === "") {
			showError(pass, "Enter password");
			valid = false;
		}
		if(cpass.value.trim() === "" || pass.value !== cpass.value) {
			showError(cpass, "Passwords do not match");
			valid = false;
		}
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
	</script>
