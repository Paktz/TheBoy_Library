<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "thebooyks_lib");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to get book review analysis, combining reviews for books with the same title
$sql = "SELECT b.Title, AVG(r.Rating) AS AverageRating, COUNT(r.ReviewID) AS ReviewCount,
               GROUP_CONCAT(DISTINCT CONCAT(u.FirstName, ' ', u.LastName, ': ', r.ReviewText) SEPARATOR '; ') AS Reviews
        FROM Books b
        JOIN Reviews r ON b.BookID = r.BookID
        JOIN Users u ON r.UserID = u.UserID
        GROUP BY b.Title
        ORDER BY AverageRating DESC, ReviewCount DESC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Review Analysis</title>
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
        <h2>Book Review Analysis</h2>
        <table>
            <tr>
                <th>Title</th>
                <th>Average Rating</th>
                <th>Review Count</th>
                <th>Reviews</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['Title']) . "</td>
                            <td>" . number_format($row['AverageRating'], 2) . "</td>
                            <td>" . $row['ReviewCount'] . "</td>
                            <td>" . htmlspecialchars($row['Reviews']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No data available</td></tr>";
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
