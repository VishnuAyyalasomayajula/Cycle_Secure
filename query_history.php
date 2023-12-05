<?php
// Include the database connection code
require_once "database.php";

// Check if the user is logged in
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Get the user's email from the session
$userEmail = $_SESSION["user"];

// Handle the form submission for querying history
if (isset($_POST["query_history"])) {
    // Retrieve the user-selected time interval 
    $startDateTime = $_POST["start_datetime"];
    $endDateTime = $_POST["end_datetime"];


    // Query the bicycle tracking history within the specified time span
    $queryHistorySql = "SELECT * FROM bicycle_tracking WHERE email = ? AND timestamp BETWEEN ? AND ?";
    $queryHistoryStmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($queryHistoryStmt, $queryHistorySql)) {
        mysqli_stmt_bind_param($queryHistoryStmt, "sss", $userEmail, $startDateTime, $endDateTime);
        mysqli_stmt_execute($queryHistoryStmt);
        $result = mysqli_stmt_get_result($queryHistoryStmt);

        // Fetch and display the results
        if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Process each row of the result
            // Output the relevant data, e.g., timestamp, latitude, longitude
            echo "Timestamp: " . $row['timestamp'] . "<br>";
            echo "Latitude: " . $row['latitude'] . "<br>";
            echo "Longitude: " . $row['longitude'] . "<br>";
            echo "<hr>";
        }
    }
    else {
        // Fetch the last updated data when no data is found
        $lastUpdateSql = "SELECT * FROM bicycle_tracking WHERE email = ? ORDER BY timestamp DESC LIMIT 1";
        $lastUpdateStmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($lastUpdateStmt, $lastUpdateSql)) {
            mysqli_stmt_bind_param($lastUpdateStmt, "s", $userEmail);
            mysqli_stmt_execute($lastUpdateStmt);
            $lastUpdateResult = mysqli_stmt_get_result($lastUpdateStmt);

            if ($lastUpdateRow = mysqli_fetch_assoc($lastUpdateResult)) {
                // Display the last updated data with a message
                echo "No Records Found<br>";
                echo "Your bicycle's last updated location:<br>";
                echo "Timestamp: " . $lastUpdateRow['timestamp'] . "<br>";
                echo "Latitude: " . $lastUpdateRow['latitude'] . "<br>";
                echo "Longitude: " . $lastUpdateRow['longitude'] . "<br>";
            } else {
                // Display a message if no data is found
                echo "No bicycle tracking data found.";
            }
    
    } else {
        // Handle the case where the statement preparation failed
        echo "Query preparation failed";
    }}

    // Close the statement
    mysqli_stmt_close($queryHistoryStmt);
}}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Query History</title>
</head>
<body>
    <div class="container">
        <h1>View Bicycle History</h1>
        <!-- Add a form for querying history -->
        <form action="query_history.php" method="post">
            <div class="form-group">
                <label for="start_datetime">Start Date and Time:</label>
                <input type="datetime-local" class="form-control" name="start_datetime" required>
            </div>
            <div class="form-group">
                <label for="end_datetime">End Date and Time:</label>
                <input type="datetime-local" class="form-control" name="end_datetime" required>
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Query History" name="query_history">
            </div>
        </form>
        <a href="index.php" class="btn btn-warning">Back to Dashboard</a>
    </div>
</body>
</html>
