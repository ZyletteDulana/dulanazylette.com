<div class="content-page">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-12">

                <div class="card car-transparent">
                    <div class="card-body p-0">
                        <div class="profile-image position-relative">
                            <img
                                src="../assets/custom/images/web-image-1.png"
                                class="rounded w-100"
                                alt="profile-image" height="500px" />
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="row m-sm-0 px-3">

            <div class="col-lg-4 card-profile">

                <div class="card card-block card-stretch card-height">

                    <div class="card-body">

                        <div class="d-flex align-items-center mb-3">

                            <div class="profile-img position-relative">
                                <img
                                    src="<?php echo htmlspecialchars($profilePictureFolderPath . $profilePicture); ?>"
                                    class="img-fluid rounded avatar-110"
                                    alt="Profile Image" />
                            </div>

                            <div class="ml-3">

                                <h4 class="mb-1">
                                    <?php echo htmlspecialchars($firstName . " " . $lastName); ?>
                                </h4>

                                <p class="mb-2">
                                    <?php echo htmlspecialchars($userType); ?>
                                </p>

                            </div>

                        </div>

                        <ul class="list-inline p-0 m-0">

                            <li class="mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt mr-2"></i>
                                    <p class="mb-0"> <?php echo htmlspecialchars($address); ?> </p>
                                </div>
                            </li>

                            <li class="mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-envelope mr-2"></i>
                                    <p class="mb-0"> <?php echo htmlspecialchars($emailAddress); ?> </p>
                                </div>
                            </li>

                            <li class="mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-phone mr-2"></i>
                                    <p class="mb-0"> <?php echo htmlspecialchars($phoneNumber); ?> </p>
                                </div>
                            </li>

                            <li class="mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-gender-ambiguous mr-2"></i>
                                    <p class="mb-0"> <?php echo htmlspecialchars($gender); ?> </p>
                                </div>
                            </li>

                            <li class="mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-check mr-2"></i>
                                    <p class="mb-0"> <?php echo htmlspecialchars($userType); ?> </p>
                                </div>
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

            <div class="col-lg-8 card-profile">

                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <ul
                            class="d-flex nav nav-pills mb-3 text-center profile-tab"
                            id="profile-pills-tab"
                            role="tablist">

                            <li class="nav-item">
                                <a
                                    class="nav-link active show"
                                    data-toggle="pill"
                                    href="#updateInformation"
                                    role="tab"
                                    a
                                    ria-selected="false"> Update Information </a>
                            </li>

                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    data-toggle="pill"
                                    href="#updatePhoto"
                                    role="tab"
                                    aria-selected="false"> Update Photo </a>
                            </li>


                            <li class="nav-item">
                                <a
                                    id="view-btn"
                                    class="nav-link"
                                    data-toggle="pill"
                                    href="#updatePassword"
                                    role="tab"
                                    aria-selected="true"> Update Password </a>
                            </li>

                            <li class="nav-item">
                                <a
                                    id="view-btn"
                                    class="nav-link"
                                    data-toggle="pill"
                                    href="#activityLog"
                                    role="tab"
                                    aria-selected="true"> Activity Log </a>
                            </li>

                        </ul>

                        <div class="profile-content tab-content">

                            <div id="updateInformation" class="tab-pane fade active show">

                                <form action="../process/updateProfile.php" method="POST" autocomplete="off" data-toggle="validator">

                                    <div class="row">

                                        <div class="col-md-4 mb-2">

                                            <div class="form-group">

                                                <label> First Name </label>

                                                <input
                                                    type="text"
                                                    name="firstName"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($firstName); ?>"
                                                    data-errors="Please Enter First Name."
                                                    required />

                                                <div class="help-block with-errors"></div>

                                            </div>

                                        </div>

                                        <div class="col-md-4 mb-2">

                                            <div class="form-group">

                                                <label> Last Name </label>

                                                <input
                                                    type="text"
                                                    name="lastName"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($lastName); ?>"
                                                    data-errors="Please Enter Last Name."
                                                    required />

                                                <div class="help-block with-errors"></div>

                                            </div>

                                        </div>

                                        <div class="col-md-4 mb-2">

                                            <div class="form-group">

                                                <label> Middle Name </label>

                                                <input
                                                    type="text"
                                                    name="middleName"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($middleName); ?>"
                                                    data-errors="Please Enter Middle Name."
                                                    />

                                                <div class="help-block with-errors"></div>

                                            </div>

                                        </div>

                                        <div class="col-md-6 mb-2">

                                            <div class="form-group">

                                                <label> Gender </label>

                                                <div class="input-group mb-2">

                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text" for="itemsList"> Gender </label>
                                                    </div>

                                                    <select id="productType" class="custom-select" name="gender" required>
                                                        <option value="Male" <?php echo htmlspecialchars($gender === "Male" ? "selected" : ""); ?>> Male </option>
                                                        <option value="Female" <?php echo htmlspecialchars($gender === "Female" ? "selected" : ""); ?>> Female </option>
                                                        <option value="Others" <?php echo htmlspecialchars($gender === "Others" ? "selected" : ""); ?>> Others </option>
                                                    </select>

                                                </div>

                                                <div class="help-block with-errors"></div>

                                            </div>

                                        </div>

                                        <div class="col-md-6 mb-2">

                                            <div class="form-group">

                                                <label> Phone Number </label>

                                                <input
                                                    type="text"
                                                    name="phoneNumber"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($phoneNumber); ?>"
                                                    data-errors="Please Enter Phone Number."
                                                    data-bs-toggle="tooltip"
                                                    data-bs-html="true"
                                                    data-bs-placement="bottom"
                                                    data-bs-title='
                                                    <span style="font-weight:bold;"> Your <b>Phone Number</b> Must:</span>
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

                                        <div class="col-md-12 mb-2">

                                            <div class="form-group">

                                                <label> Address </label>

                                                <input
                                                    type="text"
                                                    name="address"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($address); ?>"
                                                    data-errors="Please Enter Address."
                                                    required />

                                                <div class="help-block with-errors"></div>

                                            </div>

                                        </div>

                                    </div>

                                    <button type="submit" class="btn btn-success mr-2" name="updateInformation">
                                        <i class="bi bi-check-lg"></i>
                                        Update Profile
                                    </button>

                                </form>

                            </div>

                            <div id="updatePhoto" class="tab-pane fade">

                                <form action="../process/updateProfile.php" method="POST" autocomplete="off" data-toggle="validator" enctype="multipart/form-data">

                                    <div class="col-md-12">

                                        <div class="mb-3">

                                            <label class="form-label"> Product Picture </label>

                                            <!-- Image Preview -->
                                            <div class="mb-3 text-center">
                                                <img id="imagePreview"
                                                    src="<?php echo htmlspecialchars($profilePictureFolderPath . $profilePicture); ?>"
                                                    alt="Image Preview"
                                                    class="img-thumbnail"
                                                    style="max-height:200px;"
                                                    width="200px"
                                                    height="200">
                                            </div>

                                            <!-- File Input -->
                                            <div class="custom-file">
                                                <input
                                                    type="file"
                                                    class="custom-file-input"
                                                    id="imageInput"
                                                    name="profileImage"
                                                    accept=".jpg, .jpeg, .png, .gif"
                                                    required>

                                                <div class="input-group-prepend">
                                                    <label class="custom-file-label" for="imageInput" id="fileName"> Choose File </label>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <button type="submit" class="btn btn-success mr-2" name="updateProfilePicture">
                                        <i class="bi bi-check-lg"></i>
                                        Update Profile Picture
                                    </button>

                                </form>

                            </div>

                            <div id="updatePassword" class="tab-pane fade">

                                <form action="../process/updateProfile.php" method="POST" autocomplete="off" data-toggle="validator">

                                    <div class="row mb-2">

                                        <div class="col-md-12">

                                            <div class="form-group">

                                                <label> Current Password </label>

                                                <input
                                                    type="password"
                                                    name="currentPassword"
                                                    class="form-control password-input-field"
                                                    data-errors="Please Enter Current Password."
                                                    required />

                                                <div class="help-block with-errors"></div>

                                            </div>

                                        </div>

                                        <div class="col-md-12">

                                            <div class="form-group">

                                                <label> New Password </label>

                                                <input
                                                    type="password"
                                                    name="newPassword"
                                                    class="form-control password-input-field"
                                                    data-errors="Please Enter New Password."
                                                    required
                                                    data-bs-toggle="tooltip"
                                                    data-bs-html="true"
                                                    data-bs-placement="bottom"
                                                    data-bs-title='
                                                        <span style="font-weight:bold;"> Your <b>Password</b> Must have:</span>
                                                        <ul class="fw-bold mb-0" style="margin:0; list-style-type:disc;">
                                                            <li>At least 1 uppercase letter</li>
                                                            <li>At least 1 lowercase letter</li>
                                                            <li>At least 1 digit</li>
                                                            <li>At least 1 special character (!@#$%^&*)</li>
                                                            <li>8-16 characters in total</li>
                                                        </ul>
                                                    '
                                                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,16}$" />

                                                <div class="help-block with-errors"></div>

                                            </div>

                                        </div>

                                        <div class="col-md-12">

                                            <div class="form-group">

                                                <label> Confirm Password </label>

                                                <input
                                                    type="password"
                                                    name="confirmPassword"
                                                    class="form-control password-input-field"
                                                    data-errors="Please Enter Confirm Password."
                                                    required />

                                                <div class="help-block with-errors"></div>

                                            </div>

                                        </div>

                                        <div class="col-lg-6 d-flex align-items-center mb-3">
                                            <div class="custom-control custom-checkbox mb-0">
                                                <input type="checkbox" class="custom-control-input" id="customCheck1"
                                                    onclick="showPasswords()">
                                                <label class="custom-control-label control-label-1" for="customCheck1">
                                                    Show Password
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                                    <button type="submit" class="btn btn-success mr-2" name="updatePassword">
                                        <i class="bi bi-check-lg"></i>
                                        Update Password
                                    </button>

                                </form>

                            </div>

                            <div id="activityLog" class="tab-pane fade">
                                <div class="table-responsive rounded mb-3">

                                    <table class="data-tables table mb-0 tbl-server-info text-center custom-table userLogsTable">

                                        <thead class="bg-white text-uppercase">

                                            <tr class="ligth ligth-data">

                                                <th> Activity </th>
                                                <th> Activity Description </th>
                                                <th> Date </th>

                                            </tr>

                                        </thead>

                                        <tbody class="light-body text-center">

                                            <?php
                                            $getActivitiesList = $conn->prepare("SELECT
                                                                                    *
                                                                                FROM user_logs_table
                                                                                WHERE user_id = ?
                                                                                ORDER BY activity_date
                                                                                ");
                                            $getActivitiesList->bind_param("i", $loggedInUser);
                                            $getActivitiesList->execute();

                                            $activitiesListResult = $getActivitiesList->get_result();

                                            while ($activityData = $activitiesListResult->fetch_array()) {
                                            ?>
                                                <tr>

                                                    <td>
                                                        <?php echo htmlspecialchars($activityData["activity"]); ?>
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars($activityData["activity_description"]); ?>
                                                    </td>

                                                    <td>
                                                        <span class="d-none">
                                                            <?php echo $activityData["activity_date"]; ?>
                                                        </span>
                                                        <?php echo htmlspecialchars(formatTimestamp($activityData["activity_date"])); ?>
                                                    </td>

                                                </tr>
                                            <?php
                                            }
                                            ?>

                                        </tbody>

                                    </table>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>