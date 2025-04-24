<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
$con=mysqli_connect("localhost","root","","myhmsdb");
if(isset($_POST['patsub'])){
	$email=$_POST['email'];
	$password=$_POST['password2'];

    // --- DEBUGGING START ---
    echo "<hr>DEBUGGING INFO (Stage 1 - Input):<br>";
    echo "Submitted Email: " . htmlspecialchars($email) . "<br>";
    echo "Submitted Password: " . htmlspecialchars($password) . "<br>";
    // --- DEBUGGING END ---

	// Query only by email first
	$query="select * from patreg where email='$email' LIMIT 1;";
	$result=mysqli_query($con,$query);
	// Check if a user with that email exists
	if(mysqli_num_rows($result)==1)
	{
        // --- DEBUGGING START ---
        echo "DEBUGGING INFO (Stage 2 - User Found):<br>";
        echo "User found in database.<br>";
        // die(); // Stop script here to show debug output
        // --- DEBUGGING END ---

		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);

        // --- DEBUGGING START ---
        echo "DEBUGGING INFO (Stage 3 - Hash Check):<br>";
        echo "Stored Hash: " . htmlspecialchars($row['password']) . "<br>";
        // die(); // Stop script here to show debug output
        // --- DEBUGGING END ---

        // Verify the submitted password against the stored hash
        $isPasswordCorrect = password_verify($password, $row['password']);

        // --- DEBUGGING START ---
        echo "DEBUGGING INFO (Stage 4 - Verification Result):<br>";
        echo "Password Verify Result: " . ($isPasswordCorrect ? 'TRUE' : 'FALSE') . "<br>";
        echo "<hr>";
        // die(); // Stop script here to show debug output
        // --- DEBUGGING END ---

        if ($isPasswordCorrect) {
            // Password is correct, set session variables
            echo "DEBUGGING INFO (Stage 5 - Success):<br>Password verified. Setting session and redirecting...";
            // --- Set Session Vars ---
            $_SESSION['pid'] = $row['pid'];
            $_SESSION['username'] = $row['fname']." ".$row['lname'];
            $_SESSION['fname'] = $row['fname'];
            $_SESSION['lname'] = $row['lname'];
            $_SESSION['gender'] = $row['gender'];
            $_SESSION['contact'] = $row['contact'];
            $_SESSION['email'] = $row['email'];
            // --- Redirect ---
            // Use JavaScript redirect AFTER echo if header() causes issues
            echo "<script>window.location.href='admin-panel.php';</script>";
            exit(); // Stop script after successful login redirect
        } else {
            // Password verification failed
             echo "DEBUGGING INFO (Stage 6 - Password Fail):<br>";
             echo "Password verification failed for email: " . htmlspecialchars($email);
             echo "<hr>";
             exit(); // Stop script here to show debug output
        }
	} else {
        // Prepare the query to fetch user by email
        $query = "SELECT pid, fname, lname, gender, contact, email, password FROM patreg WHERE email='$email' LIMIT 1";
        // --- DEBUGGING START (Before User Not Found Check) ---
        echo "<hr>DEBUGGING INFO (func.php - Stage 1.5: Before Query Exec):<br>";
        echo "Attempting to execute query: " . htmlspecialchars($query) . "<br>";
        // --- DEBUGGING END ---

        $result = mysqli_query($con, $query);

        // --- DEBUGGING START (After Query Exec, Before User Not Found Check) ---
        $num_rows = $result ? mysqli_num_rows($result) : 'N/A (Query Failed)';
        $db_error = mysqli_error($con);
        $result_bool_string = $result ? 'true' : 'false';
        echo "<hr>DEBUGGING INFO (func.php - Stage 1.8: After Query Exec):<br>";
        echo "Query Result (bool): " . $result_bool_string . "<br>";
        echo "Number of Rows Found: " . $num_rows . "<br>";
        echo "Database Error: [" . htmlspecialchars($db_error) . "]<br>";
        // --- DEBUGGING END ---

        // Check if user exists
        if($result && mysqli_num_rows($result) == 1) {
            // --- DEBUGGING START ---
            echo "DEBUGGING INFO (Stage 2 - User Found):<br>";
            echo "User found in database.<br>";
            // die(); // Stop script here to show debug output
            // --- DEBUGGING END ---

            $row=mysqli_fetch_array($result,MYSQLI_ASSOC);

            // --- DEBUGGING START ---
            echo "DEBUGGING INFO (Stage 3 - Hash Check):<br>";
            echo "Stored Hash: " . htmlspecialchars($row['password']) . "<br>";
            // die(); // Stop script here to show debug output
            // --- DEBUGGING END ---

            // Verify the submitted password against the stored hash
            $isPasswordCorrect = password_verify($password, $row['password']);

            // --- DEBUGGING START ---
            echo "DEBUGGING INFO (Stage 4 - Verification Result):<br>";
            echo "Password Verify Result: " . ($isPasswordCorrect ? 'TRUE' : 'FALSE') . "<br>";
            echo "<hr>";
            // die(); // Stop script here to show debug output
            // --- DEBUGGING END ---

            if ($isPasswordCorrect) {
                // Password is correct, set session variables
                echo "DEBUGGING INFO (Stage 5 - Success):<br>Password verified. Setting session and redirecting...";
                // --- Set Session Vars ---
                $_SESSION['pid'] = $row['pid'];
                $_SESSION['username'] = $row['fname']." ".$row['lname'];
                $_SESSION['fname'] = $row['fname'];
                $_SESSION['lname'] = $row['lname'];
                $_SESSION['gender'] = $row['gender'];
                $_SESSION['contact'] = $row['contact'];
                $_SESSION['email'] = $row['email'];
                // --- Redirect ---
                // Use JavaScript redirect AFTER echo if header() causes issues
                echo "<script>window.location.href='admin-panel.php';</script>";
                exit(); // Stop script after successful login redirect
            } else {
                // Password verification failed
                 echo "DEBUGGING INFO (Stage 6 - Password Fail):<br>";
                 echo "Password verification failed for email: " . htmlspecialchars($email);
                 echo "<hr>";
                 exit(); // Stop script here to show debug output
            }
        } else {
            // --- DEBUGGING START (User Not Found) ---
            echo "<hr>DEBUGGING INFO (Stage 7 - User Not Found):<br>";
            echo "User NOT found in database for email: " . htmlspecialchars($email) . "<br>";
            exit(); // Stop script here to show debug output
            // --- DEBUGGING END ---
        }
    }
}

if(isset($_POST['docsub'])){
    $email=$_POST['email'];
    $password=$_POST['password'];
    $query="select * from doctb where email='$email' and password='$password';";
    $result=mysqli_query($con,$query);
    if(mysqli_num_rows($result)==1)
    {
        while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
            $_SESSION['dname'] = $row['name'];
            $_SESSION['demail'] = $row['email'];
        }
        header("Location:doctor-panel.php");
    }
    else {
        // Use JavaScript alert for failed doctor login
        echo "<script>alert('Invalid Doctor Email or Password. Try Again!'); window.history.back();</script>";
        exit();
    }
}

if(isset($_POST['update_data']))
{
	$contact=$_POST['contact'];
	$status=$_POST['status'];
	$query="update appointmenttb set payment='$status' where contact='$contact';";
	$result=mysqli_query($con,$query);
	if($result)
		header("Location:updated.php");
}

// Function to display doctors in a dropdown
if (!function_exists('display_docs')) {
    function display_docs() {
        // --- DEBUG START display_docs --- 
        echo "<!-- DEBUG: Entered display_docs() in func.php -->\n";
        global $con;
        if (!$con) {
            echo "<!-- DEBUG: DB Connection FAILURE in display_docs -->\n";
            return; // Stop if no connection
        } else {
            echo "<!-- DEBUG: DB Connection SUCCESS in display_docs -->\n";
        }

        $query = "SELECT * FROM doctb";
        echo "<!-- DEBUG: display_docs query: " . htmlspecialchars($query) . " -->\n";
        $result = mysqli_query($con, $query);
        
        if (!$result) {
            $error = mysqli_error($con);
            echo "<!-- DEBUG: display_docs Query FAILED: " . htmlspecialchars($error) . " -->\n";
            return; // Stop if query failed
        } else {
             $num_rows = mysqli_num_rows($result);
             echo "<!-- DEBUG: display_docs Query SUCCEEDED. Rows found: $num_rows -->\n";
        }

        // --- Original Loop --- 
        while ($row = mysqli_fetch_array($result)) {
            $name = $row['username']; // Doctor's name
            $docFees = $row['docFees'];
            $spec = $row['spec']; // Doctor's specialization
            // Add data-spec attribute for filtering
            echo '<option value="' . htmlspecialchars($name) . '" data-docfees="' . htmlspecialchars($docFees) . '" data-spec="' . htmlspecialchars($spec) . '">' . htmlspecialchars($name) . '</option>';
        }
        // --- DEBUG END display_docs ---
    }
}

// Function to display patients in a dropdown (example - adjust as needed)
if (!function_exists('display_pats')) {
    function display_pats() {
        global $con;
        $query = "SELECT * FROM patreg";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_array($result)) {
            $fname = $row['fname'];
            $lname = $row['lname'];
            $pid = $row['pid'];
            echo '<option value="' . $pid . '">' . htmlspecialchars($fname) . ' ' . htmlspecialchars($lname) . '</option>';
        }
    }
}

// Function to display appointments (example - adjust as needed)
if (!function_exists('display_specs')) {
    function display_specs() {
        global $con;
        $query = "SELECT DISTINCT(spec) FROM doctb"; // Assuming spec column exists
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_array($result)) {
            $spec = $row['spec'];
            echo '<option value="' . htmlspecialchars($spec) . '">' . htmlspecialchars($spec) . '</option>';
        }
    }
}

// Function to get patient details
if (!function_exists('get_patient_details')) {
    function get_patient_details() {
        global $con;
        $query = "SELECT * FROM patreg";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_array($result)) {
            $pid = $row['pid'];
            $fname = $row['fname'];
            $lname = $row['lname'];
            $gender = $row['gender'];
            $email = $row['email'];
            $contact = $row['contact'];
            echo "<tr>
                  <td>$pid</td>
                  <td>$fname</td>
                  <td>$lname</td>
                  <td>$gender</td>
                  <td>$email</td>
                  <td>$contact</td>
                </tr>";
        }
    }
}

// Function to get doctor details
if (!function_exists('get_doctor_details')) {
    function get_doctor_details(){
        global $con;
        $query = "select * from doctb";
        $result = mysqli_query($con,$query);
        while($row = mysqli_fetch_array($result)){
            $username = $row['username'];
            $email = $row['email'];
            $docFees = $row['docFees'];
            echo "<tr>
                    <td>$username</td>
                    <td>$email</td>
                    <td>$docFees</td>
                </tr>";
        }

    }
}


// Function to get appointment details
if (!function_exists('get_appointment_details')) {
    function get_appointment_details(){
        global $con;
        $query = "select * from appointmenttb;";
        $result = mysqli_query($con,$query);
        while($row = mysqli_fetch_array($result)){

            #$fname = $row['fname'];
            #$lname = $row['lname'];
            #$email = $row['email'];
            #$contact = $row['contact'];
            #$doctor = $row['doctor'];
            #$payment = $row['payment'];
            echo "<tr>
                  <td>{$row['ID']}</td>
                  <td>{$row['pid']}</td>
                  <td>{$row['fname']}</td>
                  <td>{$row['lname']}</td>
                  <td>{$row['gender']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['contact']}</td>
                  <td>{$row['doctor']}</td>
                  <td>{$row['docFees']}</td>
                  <td>{$row['appdate']}</td>
                  <td>{$row['apptime']}</td>
                  <td>{$row['userStatus']}</td>
                  <td>{$row['doctorStatus']}</td>
                  <td><a href='admin-panel1.php?ID={$row['ID']}&cancel=update' onclick='return(confirm(\"Are you sure you want to cancel this appointment?\"));' title='Cancel Appointment'>
                        <button class='btn btn-danger'>Cancel</button></a></td>
                  <td><a href='admin-panel1.php?ID={$row['ID']}&prescribe=update' onclick='return(confirm(\"Are you sure you want to prescribe this particular patient?\"));'
                  title='Prescribe Patient'><button class='btn btn-success'>Prescribe</button></a></td>
                </tr>";
        }
    }
}


if (!function_exists('get_prescription_details')) {
    function get_prescription_details(){
        global $con;
        $pid = isset($_SESSION['pid']) ? $_SESSION['pid'] : '';
        $query = "select * from prestb where pid='$pid'";
        $result = mysqli_query($con,$query);
        while($row = mysqli_fetch_array($result)){
            $doctor = $row['doctor'];
            $ID = $row['ID'];
            $fname = $row['fname'];
            $lname = $row['lname'];
            $appdate = $row['appdate'];
            $apptime = $row['apptime'];
            $disease = $row['disease'];
            $allergy = $row['allergy'];
            $prescription = $row['prescription'];
            echo "<tr>
                  <td>$doctor</td>
                  <td>$ID</td>
                  <td>$fname</td>
                  <td>$lname</td>
                  <td>$appdate</td>
                  <td>$apptime</td>
                  <td>$disease</td>
                  <td>$allergy</td>
                  <td>$prescription</td>
              </tr>";
        }
    }
}
?>