<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "thebooyks_lib");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to get users with the highest fine amounts, separated into paid and unpaid
$sql = "SELECT u.UserID, u.FirstName, u.LastName, 
               SUM(CASE WHEN f.PayStatus = 'Paid' THEN f.Amount ELSE 0 END) AS TotalPaidFines,
               SUM(CASE WHEN f.PayStatus = 'Unpaid' THEN f.Amount ELSE 0 END) AS TotalUnpaidFines,
               GROUP_CONCAT(DISTINCT b.Title SEPARATOR ', ') AS Books
        FROM Users u
        JOIN Fines f ON u.UserID = f.UserID
        JOIN Borrowings br ON f.BorrowingID = br.BorrowingID
        JOIN Books b ON br.BookID = b.BookID
        GROUP BY u.UserID
        ORDER BY TotalUnpaidFines DESC, TotalPaidFines DESC
        LIMIT 10";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Users with the Highest Fine Amounts</title>
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
    <div class="container">
        <h2>Users with the Highest Fine Amounts</h2>
        <table>
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Total Paid Fines</th>
                <th>Total Unpaid Fines</th>
                <th>Books</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['UserID']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . $row['TotalPaidFines'] . "</td>
                            <td>" . $row['TotalUnpaidFines'] . "</td>
                            <td>" . htmlspecialchars($row['Books']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No data available</td></tr>";
            }
            ?>
        </table>
        <div class="button-container">
            <button onclick="window.location.href='index.php'">Back to Main Page</button>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
