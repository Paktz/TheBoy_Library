<?php
// Start the session
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "thebooyks_lib");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables
$fineID = "";
$successMessage = $errorMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $fineID = $_POST['fineID'];

    // Check if the fine ID exists
    $checkFineID = "SELECT PayStatus FROM fines WHERE FineID = $fineID";
    $result = mysqli_query($conn, $checkFineID);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['PayStatus'] == 'Paid') {
            // Fine is already paid
            $errorMessage = "The fine with ID $fineID is already paid.";
        } else {
            // Update the fine record to mark it as paid
            $sql = "UPDATE fines SET PayStatus = 'Paid' WHERE FineID = $fineID";
            if (mysqli_query($conn, $sql)) {
                // Set success message and clear form data
                $successMessage = "Fine paid successfully";
                $fineID = "";
            } else {
                $errorMessage = "Error: " . mysqli_error($conn);
            }
        }
    } else {
        $errorMessage = "No fine found with ID $fineID.";
    }
}

// Close the connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pay Fine</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            margin: 10px 0 5px;
        }
        input {
            margin: 5px 0 15px;
            padding: 8px;
            width: 100%;
            max-width: 300px;
        }
        input[type="submit"] {
            width: 150px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
        }
        .error {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Pay Fine</h2>
        <?php if ($successMessage): ?>
            <p class="message"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form action="pay_fine.php" method="post">
            <label for="fineID">Fine ID:</label>
            <input type="number" id="fineID" name="fineID" value="<?php echo htmlspecialchars($fineID); ?>" required>

            <input type="submit" value="Pay Fine">
        </form>
    </div>
</body>
</html>
