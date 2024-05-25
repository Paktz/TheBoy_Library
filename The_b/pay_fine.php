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

// Get list of unpaid fines with user names
$unpaidFinesQuery = "SELECT f.FineID, f.UserID, u.FirstName, u.LastName, f.Amount, f.DateIssued 
                     FROM fines f
                     JOIN users u ON f.UserID = u.UserID
                     WHERE f.PayStatus = 'Unpaid'";
$unpaidFinesResult = mysqli_query($conn, $unpaidFinesQuery);

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
        .navbar {
            overflow: hidden;
            background-color: #333;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar-right {
            float: right;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
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
        .unpaid-fines {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .button-container button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .button-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <div class="navbar-right">
            <a href="add_book.php">Add Book</a>
            <a href="borrow_book.php">Borrow Book</a>
            <a href="return_book.php">Return Book</a>
            <a href="pay_fine.php">Pay Fine</a>
            <a href="review_book.php">Review Book</a>
        </div>
    </div>
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
        <div class="unpaid-fines">
            <h3>Currently Unpaid Fines</h3>
            <table>
                <tr>
                    <th>Fine ID</th>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Amount</th>
                    <th>Date Issued</th>
                </tr>
                <?php
                if ($unpaidFinesResult && mysqli_num_rows($unpaidFinesResult) > 0) {
                    while ($fine = mysqli_fetch_assoc($unpaidFinesResult)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($fine['FineID']) . "</td>
                                <td>" . htmlspecialchars($fine['UserID']) . "</td>
                                <td>" . htmlspecialchars($fine['FirstName']) . " " . htmlspecialchars($fine['LastName']) . "</td>
                                <td>" . htmlspecialchars($fine['Amount']) . "</td>
                                <td>" . htmlspecialchars($fine['DateIssued']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No unpaid fines</td></tr>";
                }
                ?>
            </table>
        </div>
        <div class="button-container">
            <button onclick="window.location.href='index.php'">Return to Home Page</button>
        </div>
    </div>
</body>
</html>
