<?php
    include_once "../includes/accessChecker.php";
?>

<div class="content-page">

    <div class="container-fluid add-form-list">

        <div class="row">

            <div class="col-sm-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between">

                        <div class="header-title d-flex justify-content-between align-items center">
                            <h4 class="card-title"> <?php echo htmlspecialchars($pageTitles[$pageName]); ?> </h4>
                        </div>

                    </div>

                    <div class="card-body">

                        <form action="../process/userManagement.php" method="POST" autocomplete="off" data-toggle="validator">

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label> First Name <span class="text-danger">*</span> </label>

                                        <input
                                            type="text"
                                            name="firstName"
                                            class="form-control"
                                            placeholder="Enter First Name"
                                            data-errors="Please Enter First Name."
                                            required />

                                        <div class="help-block with-errors"></div>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label> Last Name <span class="text-danger">*</span> </label>

                                        <input
                                            type="text"
                                            name="lastName"
                                            class="form-control"
                                            placeholder="Enter Last Name"
                                            data-errors="Please Enter Last Name."
                                            required />

                                        <div class="help-block with-errors"></div>

                                    </div>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label> Middle Name </label>

                                        <input
                                            type="text"
                                            name="middleName"
                                            class="form-control"
                                            placeholder="Enter Middle Name"
                                            data-errors="Please Enter Middle Name."
                                            />

                                        <div class="help-block with-errors"></div>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="mb-3">

                                        <label class="form-label" for="gender"> 
                                            Gender
                                        </label>

                                        <div class="input-group mb-2">

                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="gender"> Gender </label>
                                            </div>

                                            <select id="gender" class="custom-select" name="gender">
                                                <option value="" selected disabled> Select Gender </option>
                                                <option value="Male"> Male </option>
                                                <option value="Female"> Female </option>
                                                <option value="Others"> Others </option>
                                            </select>

                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label> Email Address <span class="text-danger">*</span> </label>

                                        <input
                                            type="email"
                                            name="emailAddress"
                                            class="form-control"
                                            placeholder="Enter Email Address"
                                            data-errors="Please Enter Email Address."
                                            required/>

                                        <div class="help-block with-errors"></div>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="mb-3">

                                        <label class="form-label" for="userType"> 
                                            User Type <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group mb-2">

                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="userType"> User Type </label>
                                            </div>

                                            <select id="userType" class="custom-select" name="userType" required>
                                                <option value="" selected disabled> Select User Type </option>
                                                <option value="Admin"> Admin </option>
                                                <option value="Staff"> Staff </option>
                                            </select>

                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label> User Address </label>

                                        <input
                                            type="text"
                                            name="userAddress"
                                            class="form-control"
                                            placeholder="Enter User Address"
                                            data-errors="Please Enter User Address."
                                            />

                                        <div class="help-block with-errors"></div>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label> Phone Number <span class="text-danger">*</span> </label>

                                        <input
                                            type="text"
                                            name="phoneNumber"
                                            class="form-control"
                                            placeholder="Enter Phone Number"
                                            data-errors="Please Enter Phone Number."
                                            data-bs-toggle="tooltip"
                                            data-bs-html="true"
                                            data-bs-placement="bottom"
                                            data-bs-title='
                                                <span style="font-weight:bold;"> <b>Phone Number</b> Must:</span>
                                                <ul class="fw-bold mb-0" style="margin:0; padding-left:18px; list-style-type:disc;">
                                                    <li>Start with +63 or 09</li>
                                                    <li>Contain 10-11 digits</li>
                                                    <li>Example: +639123456789 or 09123456789</li>
                                                </ul>
                                            '
                                            pattern="^(\+63\d{10}|09\d{9})$"
                                            required />

                                        <div class="help-block with-errors"></div>

                                    </div>

                                </div>

                            </div>

                            <button type="submit" class="btn btn-success mr-2" name="addNewUser">
                                <i class="bi bi-plus-lg"></i>
                                Add New User
                            </button>

                            <button type="reset" class="btn btn-danger"> 
                                <i class="bi bi-x-lg"></i>
                                Clear
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
