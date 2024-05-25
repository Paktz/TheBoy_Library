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
$firstName = $lastName = $email = $roleID = "";
$successMessage = $errorMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $roleID = $_POST['role'];

    // Insert new user into Users table
    $sql = "INSERT INTO Users (FirstName, LastName, Email, RoleID, LibraryCreditPoints)
            VALUES ('$firstName', '$lastName', '$email', $roleID, 0)";

    if (mysqli_query($conn, $sql)) {
        // Set success message and clear form data
        $successMessage = "New user registered successfully";
        $firstName = $lastName = $email = $roleID = "";
    } else {
        $errorMessage = "Error: " . mysqli_error($conn);
    }
}

// Fetch current users' data
$usersQuery = "SELECT u.UserID, u.FirstName, u.LastName, u.Email, r.RoleName, u.LibraryCreditPoints
               FROM Users u
               JOIN Roles r ON u.RoleID = r.RoleID";
$usersResult = mysqli_query($conn, $usersQuery);

// Close the connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
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
        input, select {
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
        .current-users {
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
            <a href="register.php">Register User</a>
        </div>
    </div>
    <div class="container">
        <h2>User Registration Form</h2>
        <?php if ($successMessage): ?>
            <p class="message"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form action="register.php" method="post">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" required>

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="1" <?php if ($roleID == 1) echo 'selected'; ?>>Administrator</option>
                <option value="2" <?php if ($roleID == 2) echo 'selected'; ?>>Librarian</option>
                <option value="3" <?php if ($roleID == 3) echo 'selected'; ?>>Member</option>
                <option value="4" <?php if ($roleID == 4) echo 'selected'; ?>>Guest</option>
            </select>

            <input type="submit" value="Register">
        </form>
        <div class="current-users">
            <h3>Current Users</h3>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Library Credit Points</th>
                </tr>
                <?php
                if ($usersResult && mysqli_num_rows($usersResult) > 0) {
                    while ($user = mysqli_fetch_assoc($usersResult)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($user['UserID']) . "</td>
                                <td>" . htmlspecialchars($user['FirstName']) . "</td>
                                <td>" . htmlspecialchars($user['LastName']) . "</td>
                                <td>" . htmlspecialchars($user['Email']) . "</td>
                                <td>" . htmlspecialchars($user['RoleName']) . "</td>
                                <td>" . htmlspecialchars($user['LibraryCreditPoints']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No users found</td></tr>";
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
