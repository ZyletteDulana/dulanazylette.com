<?php
    include_once "../includes/accessChecker.php";
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

                        <a href="dashboard.php?page=add-user" class="btn btn-success add-list"><i class="las la-plus mr-3"></i> Add User </a>

                    </div>

                    <div class="card-body">

                        <div class="col-lg-12">

                            <div class="table-responsive rounded mb-3">

                                <table class="data-tables table mb-0 tbl-server-info text-center custom-table productsTable">

                                    <thead class="bg-white text-uppercase">

                                        <tr class="ligth ligth-data">

                                            <th class="export-ignore"> User Photo </th>
                                            <th> Name </th>
                                            <th> User Type </th>
                                            <th> Email Address </th>
                                            <th> Created At </th>
                                            <th class="export-ignore"> Action </th>

                                        </tr>

                                    </thead>

                                    <tbody class="light-body text-center">

                                        <?php
                                        $getUsersList = $conn->prepare("SELECT * FROM users_table WHERE user_type = 'Staff'");
                                        $getUsersList->execute();

                                        $usersQueryResult = $getUsersList->get_result();

                                        while ($userData = $usersQueryResult->fetch_array()) {
                                            $userImage = $userData["profile_picture"] ?? "default-photo.png";

                                        ?>
                                            <tr>
                                                <td class="export-ignore">
                                                    <img src="<?php echo htmlspecialchars($profilePictureFolderPath . $userImage); ?>" alt="Profile Picture" class="img-fluid rounded avatar-75 mr-3" width="50" height="50">
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($userData["first_name"] . " " . $userData["last_name"]); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($userData["user_type"]); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($userData["email_address"]); ?>
                                                </td>

                                                <td class="export-date">
                                                    <span class="d-none">
                                                        <?php echo $userData["created_at"]; ?>
                                                    </span>

                                                    <?php echo htmlspecialchars(formatTimestamp($userData["created_at"])); ?>
                                                </td>

                                                <td class="export-ignore">
                                                    <div class="d-flex align-items-center justify-content-center">

                                                        <div>
                                                            <a
                                                                class="btn btn-warning btn-sm px-2 mr-2"
                                                                data-toggle="tooltip"
                                                                data-placement="top"
                                                                title=""
                                                                data-original-title="View Profile"
                                                                href="dashboard.php?page=user-info&userId=<?php echo htmlspecialchars(base64_encode($userData["user_id"])); ?>"
                                                                >
                                                                <i class="bi bi-info-circle square mr-0"></i>
                                                            </a>
                                                        </div>

                                                        <div>
                                                            <form action="../process/userManagement.php" method="POST" id="deleteUserForm">

                                                                <input type="hidden" name="deleteUser" value="1">
                                                                <input type="hidden" name="userName" value="<?php echo htmlspecialchars($userData["first_name"] . " " . $userData["last_name"]); ?>">
                                                                <input type="hidden" name="deleteUserId" value="<?php echo htmlspecialchars($userData["user_id"]); ?>">

                                                                <button
                                                                    type="submit"
                                                                    class="btn btn-danger btn-sm px-2 mr-2"
                                                                    data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    title=""
                                                                    data-original-title="Delete User"
                                                                    onclick="confirmAction(
                                                                    event,
                                                                    this.form,
                                                                    'deleteUserForm',
                                                                    'Delete User: <?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?>?',
                                                                    'warning',
                                                                    'Are you sure you want to delete this user and their data?',
                                                                    'Delete User',
                                                                    '#E08DB4'
                                                                )">
                                                                    <i class="ri-delete-bin-line mr-0"></i>
                                                                </button>
                                                            </form>
                                                        </div>

                                                    </div>
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