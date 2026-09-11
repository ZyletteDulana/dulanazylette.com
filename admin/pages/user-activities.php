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

                        <div class="col-lg-12">

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
                                                                                    u.first_name AS 'user',
                                                                                    ul.*
                                                                                FROM user_logs_table ul
                                                                                LEFT JOIN users_table u
                                                                                ON ul.user_id = u.user_id
                                                                                WHERE ? = 'Admin' OR ul.user_id = ?
                                                                                ORDER BY ul.activity_date DESC
                                                                                ");
                                        $getActivitiesList->bind_param("si", $userType, $loggedInUser);
                                        $getActivitiesList->execute();

                                        $activitiesListResult = $getActivitiesList->get_result();

                                        while ($activityData = $activitiesListResult->fetch_array()) {
                                        ?>
                                            <tr>

                                                <td class="fw-bold">
                                                    <?php echo htmlspecialchars($activityData["user"]); ?>
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