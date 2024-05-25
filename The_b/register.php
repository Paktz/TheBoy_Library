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
    </style>
</head>
<body>
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
                <option value="3" <?php if ($roleID == 3) echo 'selected'; ?>>Patron</option>
                <option value="4" <?php if ($roleID == 4) echo 'selected'; ?>>Guest</option>
            </select>

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
