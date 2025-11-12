<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>DeskApp - Bootstrap Admin Dashboard HTML Template</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="vendors/images/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="vendors/images/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="vendors/images/favicon-16x16.png" />

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="vendors/styles/core.css" />
    <link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/jquery-steps/jquery.steps.css" />
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css" />

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-GBZ3SGGX85"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag("js", new Date());

    gtag("config", "G-GBZ3SGGX85");
    </script>

</head>

<body class="register-page">

    <?php
	// Include the database connection file
	include('connect.php');

	// Check if the form is submitted
	if (isset($_POST['register'])) {
		// Sanitize the input data
		$name = $conn->real_escape_string($_POST['name']);
		$email = $conn->real_escape_string($_POST['email']);
		$phone_no = $conn->real_escape_string($_POST['phone_no']);
		$password = $conn->real_escape_string($_POST['password']); // Hash the password
	
		// Function to check if email exists
		function checkEmailExists($email, $conn)
		{
			$query = "SELECT id FROM user WHERE email = '$email'";  // Check if the email already exists
			$result = $conn->query($query);
			return $result->num_rows > 0;  // If email exists, return true
		}

		// Check if email already exists
		if (checkEmailExists($email, $conn)) {
			echo '<script>
                alert("Error: The email address is already in use!");
                window.location.href = "register.php"; // Redirect back to registration page
              </script>';
		} else {
			// SQL query to insert data into the 'user' table
			$sql = "INSERT INTO user ( name, email, phone_no,pass)
                VALUES ('$name','$email', '$phone_no','$password')";

			// Execute the query
			if ($conn->query($sql) === TRUE) {
				echo '<script>
                    alert("Registration Successful!");
                    window.location.href = "login.php";
                  </script>';
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}

		// Close the connection
		$conn->close();
	}



	?>

    <div class="login-header box-shadow">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <h1>iLot Location</h1>

            </div>
            <div class="login-menu">
                <ul>
                    <li><a href="index.php">Login</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="register-page-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 col-lg-5">
                    <img src="vendors/images/register-page-img1.png" alt="Registration Image" />
                </div>
                <div class="col-md-5 col-lg-7">

                    <div class="pd-20 card-box mb-30">
                        <div class="login-title">
                            <h2 class="text-center text-primary">Register To Get Started</h2>
                        </div>
                        <form method="POST" action=" ">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="name" class="col-sm-12 col-form-label">Fullt Name*</label>
                                        <input id="name" name="name" type="text" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="phone_no" class="col-sm-12 col-form-label"> Phone
                                            Number*</label>
                                        <input id="phone_no" name="phone_no" type="text" class="form-control"
                                            required />
                                    </div>
                                </div>

                            </div>

                            <div class="row">


                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="email" class="col-sm-12 col-form-label">E-mail Address*</label>
                                        <input id="email" name="email" type="email" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="password" class="col-sm-8 col-form-label">Password*</label>

                                        <input id="password" name="password" type="password" class="form-control"
                                            required />

                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="cpassword" class="col-md-12sm-12	 col-form-label">Confirm
                                            Password*</label>
                                        <input id="cpassword" type="password" name="cpassword" class="form-control"
                                            required />
                                    </div>
                                </div>
                            </div>




                            <div class="row">



                            </div>
                            <button class="btn btn-primary btn-lg btn-block" type="submit" name="register">Sign
                                up</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- success Popup html Start -->


    <!-- success Popup html End -->

    <!-- js -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="src/plugins/jquery-steps/jquery.steps.js"></script>
    <script src="vendors/scripts/steps-setting.js"></script>

</body>

</html>