<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST["submit"])) {
           $fullName = $_POST["fullname"];
           $email = $_POST["email"];
           $bicycleid= $_POST["bicycleid"];
           $bicyclemodel = $_POST["bicyclemodel"];
           $password = $_POST["password"];
           $passwordRepeat = $_POST["repeat_password"];

           
           $passwordHash = password_hash($password, PASSWORD_DEFAULT);
           $latitude = isset($_POST["latitude"]) ? $_POST["latitude"] : '';
           $longitude = isset($_POST["longitude"]) ? $_POST["longitude"] : '';


           $errors = array();
           
           if (empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat) OR empty($bicycleid) OR empty($bicyclemodel)) {
            array_push($errors,"All fields are required");
           }
           if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
           }
           if (strlen($password)<8) {
            array_push($errors,"Password must be at least 8 charactes long");
           }
           if ($password!==$passwordRepeat) {
            array_push($errors,"Password does not match");
           }
           require_once "database.php";
           $sql = "SELECT * FROM users WHERE email = '$email'";
           $result = mysqli_query($conn, $sql);
           $rowCount = mysqli_num_rows($result);
           if ($rowCount>0) {
            array_push($errors,"Email already exists!");
           }
           if (count($errors)>0) {
            foreach ($errors as  $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
           }else{
            
            $sql = "INSERT INTO users (full_name, email, bicycle_id, bicycle_model, password, latitude, longitude, current_latitude, current_longitude) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt,"sssssdddd",$fullName, $email, $bicycleid, $bicyclemodel, $passwordHash, $latitude, $longitude, $latitude, $longitude);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>You are registered successfully.</div>";
            }else{
                die("Something went wrong");
            }
           }
          

        }
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:">
            </div>
            <div class="form-group">
                <input type="emamil" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="bicycleid" placeholder="Bicycle Id:">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="bicyclemodel" placeholder="Bicycle Model:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:">
            </div>
            <!-- Add the location container, hidden input fields, and pick location button -->
            <div class="form-group">
                <label for="location">Select parking location:</label>
                <div id="map" style="height: 300px;"></div>
                <button type="button" class="btn btn-primary" id="pickLocation">Pick Location</button>
            </div>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
        <div><p>Already Registered <a href="login.php">Login Here</a></p></div>
      </div>
    </div>
     <!-- Include the Google Maps JavaScript code -->
     <script>
        document.addEventListener("DOMContentLoaded", function () {
            let map;
            let marker;

            // Function to initialize the map
            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 0, lng: 0 }, // Initial map center
                    zoom: 15 // Initial zoom level
                });

                // Listen for a click on the map to place a marker
                map.addListener('click', function (e) {
                    placeMarker(e.latLng);
                });
            }

            // Function to place a marker on the map
            function placeMarker(location) {
                if (marker) {
                    marker.setPosition(location);
                } else {
                    marker = new google.maps.Marker({
                        position: location,
                        map: map
                    });
                }

                // Update the hidden input fields with latitude and longitude
                document.getElementById('latitude').value = location.lat();
                document.getElementById('longitude').value = location.lng();
            }

            // Initialize the map
            initMap();

            // Button click event to open the map picker
            document.getElementById('pickLocation').addEventListener('click', function () {
                
            });
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZIzjk_x0z2B9wng8WwuvpLW-GvDZc470&libraries=places"></script>
</body>
</html>