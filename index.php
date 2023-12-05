<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}

// Define your database connection details
$hostName = "localhost"; // Replace with your database host
$dbUser = "root"; // Replace with your database username
$dbPassword = ""; // Replace with your database password
$dbName = "login_register"; // Replace with your database name

// Create a database connection
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the user's email from the session
$userEmail = $_SESSION["user"];

// Fetch user data from the database based on their email
$sql = "SELECT full_name, bicycle_id, bicycle_model, latitude, longitude, speed, current_latitude, current_longitude FROM users WHERE email = '$userEmail'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $fullName = $row['full_name'];
    $bicycleid = $row['bicycle_id'];
    $bicyclemodel = $row['bicycle_model'];
    $latitude = $row['latitude'];
    $longitude = $row['longitude'];
    $speed = $row['speed'];
    $currentlatitude = $row['current_latitude'];
    $currentlongitude = $row['current_longitude'];
    $parkingLocation = $latitude . ', ' . $longitude;
    $currentLocation = $currentlatitude . ', ' . $currentlongitude;
} else {
    // Handle the case where no data is found for the user.
}
 // Assume you have retrieved the existing latitude and longitude values from the database
 $existingLatitude = $latitude;
 $existingLongitude = $longitude;

 // Check if the values have changed
 if ($currentlatitude !== $existingLatitude || $currentlongitude !== $existingLongitude) {
     // Values have changed, insert data into bicycle_tracking
     $insertTrackingSql = "INSERT INTO bicycle_tracking (email, latitude, longitude, timestamp) VALUES (?, ?, ?, NOW())";
     $insertTrackingStmt = mysqli_stmt_init($conn);

     if (mysqli_stmt_prepare($insertTrackingStmt, $insertTrackingSql)) {
         mysqli_stmt_bind_param($insertTrackingStmt, "sdd", $userEmail, $currentlatitude, $currentlongitude);
         mysqli_stmt_execute($insertTrackingStmt);
     } else {
         // Handle the case where the statement preparation failed
     }
 }
 else {
 // Handle the case where no data is found for the user.
}
// Handle the "Mark as Parked" action
if (isset($_POST["mark_parked"])) {
    // Retrieve data from the current_latitude and current_longitude attributes in the database
    $currentLatitude = $currentlatitude;
    $currentLongitude = $currentlongitude;

    // Update the database with the retrieved latitude and longitude
    $updateSql = "UPDATE users SET latitude = ?, longitude = ? WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    $prepareStmt = mysqli_stmt_prepare($stmt, $updateSql);
    
    if ($prepareStmt) {
        mysqli_stmt_bind_param($stmt, "dds", $currentLatitude, $currentLongitude, $userEmail);
        if (mysqli_stmt_execute($stmt)) {
            // Success message or redirection after marking as parked
        } else {
            // Handle the case where the update failed
        }
    } else {
        // Handle the case where the statement preparation failed
    }
    
}
// Close the database connection
mysqli_close($conn);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv of` `="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>User Dashboard</title>
    <script>
        // Variables to store the previous location, speed, and parking location
        var currentLocation = "<?php echo $currentLocation; ?>";
        var currentSpeed = <?php echo $speed; ?>;
        var fullName = "<?php echo $fullName; ?>";
        var email = "<?php echo $userEmail; ?>";
        var bicycleid = "<?php echo $bicycleid; ?>";
    

        // Function to periodically check for changes
        function checkForChanges() {
            // Make an AJAX request to fetch the user's current location and speed
            
            var prevLocation = "<?php echo $parkingLocation; ?>"; // Replace with actual location
            var prevSpeed = 0;; // Replace with actual speed

            // Check for changes in location or speed
            if (currentLocation !== prevLocation || currentSpeed !== prevSpeed) {
                // Display an alert to the user
                alert("Your Bicycle is Moved");

            }

            
        }

         // Function to stop alerts
        function stopAlerts() {
            if (alertInterval) {
                clearInterval(alertInterval);
                alertInterval = null;
            }
        }
        // Periodically check for changes (e.g., every 5 seconds)
        alertInterval = setInterval(checkForChanges, 5000);
    </script>
</head>
<body>
    <div class="container">
        <h1>Welcome to Dashboard</h1>
        <p><strong>Fullname: </strong><?php echo $fullName; ?></p>
        <p><strong>Email: </strong><?php echo $userEmail; ?></p>
        <p><strong>BicycleId: </strong><?php echo $bicycleid; ?></p>
        <p><strong>Bicyclemodel: </strong><?php echo $bicyclemodel; ?></p>
        <p><strong>Saved Parking Location: </strong><?php echo $parkingLocation; ?></p>
        <p><strong>Current Location: </strong><?php echo $currentLocation; ?></p>
        <p><strong>Bicycle Current Speed: </strong><?php echo $speed; ?></p>
        <!-- Add the "Mark as Parked" form with hidden input fields -->
        <form action="index.php" method="post">
            <input type="hidden" name="current_latitude" value="<?php echo $currentLatitude; ?>">
            <input type="hidden" name="current_longitude" value="<?php echo $currentLongitude; ?>">
            <div class="form-btn">
                <button type="submit" name="mark_parked" class="btn btn-success">Mark as Parked</button>
            </div>
        </form>
         <div class="form-btn">
            <button id="unparkButton" class="btn btn-danger" onclick="stopAlerts()">Unpark</button>
        </div>

        <a href="logout.php" class="btn btn-warning">Logout</a>
        <div><p>Want to know your bicycle location history? <a href="query_history.php">Query Here</a></p></div>
    </div>
    </div>
</body>
</html>
