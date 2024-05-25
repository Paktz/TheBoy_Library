<!DOCTYPE html>
<html>
<head>
    <title>Library Management System</title>
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
        .section {
            margin: 20px 0;
        }
        .section h3 {
            color: #555;
        }
        .button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>TheByook Library Management System</h2>

        <div class="section">
            <h3>Forms</h3>
            <a href="register.php" class="button">Register</a>
            <a href="add_book.php" class="button">Add Book</a>
            <a href="borrow_book.php" class="button">Borrow Book</a>
            <a href="return_book.php" class="button">Return Book</a>
            <a href="reserve_book.php" class="button">Reserve Book</a>
            <a href="review_book.php" class="button">Review Book</a>
            <a href="pay_fine.php" class="button">Pay Fine</a>
        </div>

        <div class="section">
            <h3>Analysis Reports</h3>
            <a href="AP_active_user.php" class="button">Most Active Borrowers</a>
            <a href="AP_user_fines.php" class="button">Users Fines Analysis</a>
            <a href="AP_borrow_duration.php" class="button">Average Borrow Duration by Users</a>
            <a href="AP_book_review.php" class="button">Book Review Analysis</a>
        </div>
    </div>
</body>
</html>
