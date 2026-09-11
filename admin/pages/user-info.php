<?php
    include_once "../includes/accessChecker.php";
    
    $userInfoId = htmlspecialchars(base64_decode($_GET["userId"])) ?? null;

    if(!$userInfoId || $userInfoId == null) {
        displayNotification("error", "Invalid User", "User not found!");
        header("Location: dashboard.php?page=users-list");
        exit();
    }

    $getUserData = $conn->prepare("SELECT * FROM users_table WHERE user_id = ? LIMIT 1");
    $getUserData->bind_param("i", $userInfoId);
    $getUserData->execute();

    $usersQueryResult = $getUserData->get_result();

    if($usersQueryResult->num_rows === 1) {
        $userInfo = $usersQueryResult->fetch_object();

        $userProfilePicture = $userInfo->profile_picture ?? "default-photo.png";
    }

    else {
        displayNotification("error", "Invalid User", "User not found!");
        header("Location: dashboard.php?page=users-list");
        exit();
    }

?>

<div class="content-page">

    <div class="container-fluid">

        <div class="row">

            <div class="col-sm-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between">

                        <div class="header-title d-flex justify-content-between align-items center">
                            <h4 class="card-title"> <?php echo htmlspecialchars($pageTitles[$pageName]); ?> </h4>
                        </div>

                    </div>

                    <div class="card-body">

                        <div class="row m-sm-0 px-3">

                            <div class="col-lg-4">

                                <div class="card card-block card-stretch card-height">

                                    <div class="card-body">

                                        <div class="d-flex align-items-center mb-3">

                                            <div class="profile-img position-relative">
                                                <img
                                                    src="<?php echo htmlspecialchars($profilePictureFolderPath . $userProfilePicture); ?>"
                                                    class="img-fluid rounded avatar-110"
                                                    alt="Profile Image" />
                                            </div>

                                            <div class="ml-3">

                                                <h4 class="mb-1">
                                                    <?php echo htmlspecialchars($userInfo->first_name . " " . $userInfo->last_name); ?>
                                                </h4>

                                                <p class="mb-2">
                                                    <?php echo htmlspecialchars($userInfo->user_type); ?>
                                                </p>

                                            </div>

                                        </div>

                                        <ul class="list-inline p-0 m-0">

                                            <li class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-geo-alt mr-2"></i>
                                                    <p class="mb-0"> <?php echo htmlspecialchars($userInfo->user_address ? $userInfo->user_address : "N/A"); ?> </p>
                                                </div>
                                            </li>

                                            <li class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-envelope mr-2"></i>
                                                    <p class="mb-0"> <?php echo htmlspecialchars($userInfo->email_address); ?> </p>
                                                </div>
                                            </li>

                                            <li class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-phone mr-2"></i>
                                                    <p class="mb-0"> <?php echo htmlspecialchars($userInfo->phone_number); ?> </p>
                                                </div>
                                            </li>

                                            <li class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-gender-ambiguous mr-2"></i>
                                                    <p class="mb-0"> <?php echo htmlspecialchars($userInfo->gender); ?> </p>
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

                            <div class="col-lg-8">

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
                                                    href="#userInformation"
                                                    role="tab"
                                                    a
                                                    ria-selected="false"> User Information </a>
                                            </li>

                                            <li class="nav-item">
                                                <a
                                                    id="view-btn"
                                                    class="nav-link"
                                                    data-toggle="pill"
                                                    href="#userActivityLog"
                                                    role="tab"
                                                    aria-selected="true"> <?php echo $userInfo->first_name . "'s";?> Activity Log </a>
                                            </li>

                                        </ul>

                                        <div class="profile-content tab-content">

                                            <div id="userInformation" class="tab-pane fade active show">

                                                <form action="#" method="POST" autocomplete="off" data-toggle="validator">

                                                    <div class="row">

                                                        <div class="col-md-4 mb-2">

                                                            <div class="form-group">

                                                                <label> First Name </label>

                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    value="<?php echo htmlspecialchars($userInfo->first_name); ?>"
                                                                    readonly
                                                                    disabled />

                                                            </div>

                                                        </div>

                                                        <div class="col-md-4 mb-2">

                                                            <div class="form-group">

                                                                <label> Middle Name </label>

                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    value="<?php echo htmlspecialchars($userInfo->middle_name); ?>"
                                                                    readonly
                                                                    disabled />

                                                            </div>

                                                        </div>

                                                        <div class="col-md-4 mb-2">

                                                            <div class="form-group">

                                                                <label> Last Name </label>

                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    value="<?php echo htmlspecialchars($userInfo->last_name); ?>"
                                                                    readonly
                                                                    disabled />

                                                            </div>

                                                        </div>

                                                        <div class="col-md-6 mb-2">

                                                            <div class="form-group">

                                                                <label> Gender </label>

                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    value="<?php echo htmlspecialchars($userInfo->gender); ?>"
                                                                    readonly
                                                                    disabled />

                                                            </div>
                                                            
                                                        </div>

                                                        <div class="col-md-6 mb-2">

                                                            <div class="form-group">

                                                                <label> Phone Number </label>

                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    value="<?php echo htmlspecialchars($userInfo->phone_number); ?>"
                                                                    readonly
                                                                    disabled />

                                                            </div>
                                                            
                                                        </div>

                                                        <div class="col-md-12 mb-2">

                                                            <div class="form-group">

                                                                <label> Address </label>

                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    value="<?php echo htmlspecialchars($userInfo->user_address); ?>"
                                                                    readonly
                                                                    disabled />

                                                            </div>
                                                            
                                                        </div>          

                                                    </div>

                                                </form>

                                            </div>

                                            <div id="userActivityLog" class="tab-pane fade">

                                                <div class="table-responsive rounded mb-3">

                                                    <table class="data-tables table mb-0 tbl-server-info text-center custom-table userActivitiesTable">

                                                        <thead class="bg-white text-uppercase">

                                                            <tr class="ligth ligth-data">

                                                                <th> User </th>
                                                                <th> Activity </th>
                                                                <th> Activity Description </th>
                                                                <th> Date </th>

                                                            </tr>

                                                        </thead>

                                                        <tbody class="light-body text-center">

                                                            <?php
                                                            $getActivitiesList = $conn->prepare("SELECT
                                                                                    ul.*,
                                                                                    ut.first_name
                                                                                FROM user_logs_table ul
                                                                                LEFT JOIN users_table ut
                                                                                ON ul.user_id = ut.user_id
                                                                                WHERE ul.user_id = ?
                                                                                ORDER BY ul.activity_date
                                                                                ");
                                                            $getActivitiesList->bind_param("i", $userInfoId);
                                                            $getActivitiesList->execute();

                                                            $activitiesListResult = $getActivitiesList->get_result();

                                                            while ($activityData = $activitiesListResult->fetch_array()) {
                                                            ?>
                                                                <tr>

                                                                    <td>
                                                                        <?php echo htmlspecialchars($activityData["first_name"]); ?>
                                                                    </td>

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

            </div>

        </div>

    </div>

</div>