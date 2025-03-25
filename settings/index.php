<?php


$api_key = get_option('md_edu_key');
if (!$api_key) {
    if (isset($_GET['Token'])) {
        $data = array(
            'action' => 'verify_cloud',
            'token' => $_GET['Token']
        );

        $response = wp_remote_post(admin_url('admin-ajax.php'), array(
            'method' => 'POST',
            'body' => $data
        ));

        if (!is_wp_error($response)) {
            $response_body = wp_remote_retrieve_body($response);
            $data = json_decode($response_body, true);
            //var_dump($data['data']);die;
            if ($data['data'] == "Verified successfully!") {
                echo "<script type='text/javascript'>location.reload(); var successMessage = '" . $data['data'] . "';
        sessionStorage.setItem('successMessage', successMessage);</script>";
            } else {
                echo "<script type='text/javascript'> var errorMessage = '" . $data['data'] . "';
        sessionStorage.setItem('errorMessage', errorMessage);</script>";
            }
        } else {
            $error_message = $response->get_error_message();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Thư viện Bootstrap -->
    <style>
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }

        input[type=checkbox],
        input[type=radio] {
            background-color: #fff;
            background-repeat: no-repeat;
        }

        .underline-button {
            text-decoration: underline;
            cursor: pointer;
            color: blue;
        }
    </style>
</head>

<body>
    <div style="height: 150px;"></div>
    <div class="wrap">
        <h2>Media Cloud Integration Settings</h2>
        <form method="post" action="" name="check_key" id="check_key">

            <?php
            $status = 1 == get_option('md-status') ? 'checked' : '';
            echo '<div class="form-check form-switch">
			  <input class="form-check-input" type="checkbox" name="md-status" value="" id="flexSwitchCheckChecked" ' . $status . '>
			  <label class="form-check-label" for="flexSwitchCheckChecked">Enable/disable upload media</label>
			</div>';
            settings_fields('MC-plugin-settings');
            do_settings_sections('MC-plugin-settings');
            submit_button();
            ?>

        </form>
        <?php if ($status == 'checked') { ?>
            <center>
                <div class="col-6">
                    <div class="d-flex justify-content-around">
                        <div class="text-center">
                            <button type="button" class="btn btn-primary p-2 text-center" data-bs-toggle="modal" data-bs-target="#mediaModal" id="media">
                                Sync Media
                            </button>
                        </div>

                        <!--                     <div class="text-center">
                        <button type="button" class="btn btn-primary p-2 text-center" id="server">
                            Backup Server
                        </button>
                    </div> -->
                    </div>
                </div>
            </center>
            <!-- Modal -->

            <div class="modal fade" id="mediaModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-success" id="exampleModalLabel">Media Processing</h5>
                            <div class="spinner-border mx-2 check-load" style="width: 1.5em; height: 1.5em;" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            <div class="check-success" style="display:none;">
                                <i class='fs-1 bi bi-check-lg text-success px-2'></i>
                            </div>
                            <div class='check-warning' style="display:none">
                                <i class="fs-4 bi bi-exclamation-octagon-fill text-danger px-2"></i>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body image-upload" id="scrollOver" style="height: 500px; overflow: auto;">
                            <!-- <div style="height: 600px; overflow: auto;"> -->
                            <h5 class="text-danger text-center fw-bold fst-italic">Don't close this window while
                                process running !!</h5>
                            <!-- <div class="d-flex justify-content-center"> -->
                            <div id="count" class="">
                                <div id="fileProgress">
                                    <div class="d-flex align-items-center mb-2">
                                        <div id="each"></div>
                                        <div class="" id="total"></div>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="loading" style="display: none;">
                                <img src="<?php echo plugins_url('../img/Loader-animation-principle-freebie.gif', __FILE__); ?>" style="width:100%;">
                            </div>
                            <div id='data-passed'>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-around">
                            <button type="button" class="btn btn-success fs-5" id='upload'>Upload Media</button>
                            <button type="button" class="btn btn-primary fs-5" id='recover'>Recover Media</button>
                            <button type="button" class="btn btn-danger fs-5 px-2" id='delete'>Delete
                                Media</button>

                        </div>
                    </div>
                </div>
            </div>


        <?php } ?>
        <?php
        if (!$api_key) { ?>
            <div class="modal fade" id="forgot_Modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="forgot_Modal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content card">

                        <div class="card-header text-center bg-primary text-white text-uppercase font-weight-bold h2">
                            Your email is:
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                        </div>
                        <div class="form-group">
                            <label for="email" class='fw-normal'></label>
                            <input type="text" class="form-control" id="email" placeholder="example@gmail.com">
                            <span id="reminderEmail"></span>
                        </div>
                        <div class="p-3">
                            <button class="btn btn-primary submit fw-bold" id="submit_forgot" name="submit_forgot">Send</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="Verify_Modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="Verify_Modal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content card">

                        <div class="card-header text-center bg-primary text-white text-uppercase font-weight-bold h2">
                            Verify token is:
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                        </div>
                        <div class="form-group">
                            <label for="verify" class='fw-normal'></label>
                            <input type="text" class="form-control" id="verify" placeholder="">
                            <span id="reminderVerify"></span>
                        </div>
                        <div class="p-3">
                            <button class="btn btn-primary submit fw-bold" id="submit_Verify" name="submit_Verify">Send</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="Cr_Acc_Modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="Cr_Acc_Modal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content card">

                        <div class="card-header text-center bg-primary text-white text-uppercase font-weight-bold h2">
                            Sign up for account
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                        </div>
                        <form id="form" class="card-body" method="post" enctype="multipart/form-data" runat="server">
                            <div class="form-group">
                                <label for="Name" class='fw-normal'>Account name:</label>
                                <input type="text" class="form-control" id="Name" placeholder="Empty">
                                <span id="reminderName"></span>
                            </div>
                            <div class="form-group">
                                <label for="mail" class='fw-normal'>Email:</label>
                                <input type="text" class="form-control" id="mail" placeholder="example@gmail.com">
                                <span id="reminderMail"></span>
                            </div>

                            <div class="d-flex flex-nowrap">
                                <div class="form-group keep pb-2 header1 w-50">
                                    <label for="create" class='fw-normal'>Create password:</label>
                                    <input type="password" class="form-control" id="create" placeholder="Empty" required>
                                    <span class="show-btn"><i class="fa-solid fa-eye"></i></span>
                                    <span id="reminderCreate"></span>
                                </div>
                                <div class="form-group keep pb-2 header1 w-50 px-1">
                                    <label for="retry" class='fw-normal'>Retry password:</label>
                                    <input type="password" class="form-control" id="retry" placeholder="Empty" required>
                                    <span class="show-btn"><i class="fas fa-eye hide-btn"></i></span>
                                    <span id="reminderRetry"></span>
                                </div>
                            </div>
                        </form>
                        <div class="p-3">
                            <button class="btn btn-primary submit fw-bold" id="submit_sign" name="submit_sign">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div style="height: 150px;"></div>
    <script>

    </script>
</body>

</html>