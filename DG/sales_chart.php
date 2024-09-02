<?php
include 'connection.php';
date_default_timezone_set('Asia/Manila');

// Get the sorting type and date range from the request
$sortType = $_GET['sortType'] ?? 'day';
$startDate = $_GET['startDate'] ?? date('Y-m-01');
$endDate = $_GET['endDate'] ?? date('Y-m-d');

// Initialize sales data arrays
$salesData = [];

// Determine the query based on the sort type
switch ($sortType) {
    case 'day':
        $dateRangeQuery = $conn->prepare("SELECT DATE(date_created) as date, SUM(Total_amount) as total FROM sales WHERE DATE(date_created) BETWEEN ? AND ? GROUP BY DATE(date_created)");
        break;
    case 'week':
        $dateRangeQuery = $conn->prepare("SELECT YEAR(date_created) as year, MONTH(date_created) as month, WEEK(date_created, 1) as week_of_year, WEEK(date_created, 1) - WEEK(DATE_SUB(date_created, INTERVAL DAYOFMONTH(date_created)-1 DAY), 1) + 1 as week_of_month, SUM(Total_amount) as total FROM sales WHERE DATE(date_created) BETWEEN ? AND ? GROUP BY YEAR(date_created), MONTH(date_created), WEEK(date_created, 1)");
        break;
    case 'year':
        $dateRangeQuery = $conn->prepare("SELECT YEAR(date_created) as year, MONTH(date_created) as month, SUM(Total_amount) as total FROM sales WHERE DATE(date_created) BETWEEN ? AND ? GROUP BY YEAR(date_created), MONTH(date_created)");
        break;
    default:
        $dateRangeQuery = $conn->prepare("SELECT DATE(date_created) as date, SUM(Total_amount) as total FROM sales WHERE DATE(date_created) BETWEEN ? AND ? GROUP BY DATE(date_created)");
        break;
}

$dateRangeQuery->execute([$startDate, $endDate]);
$salesData = $dateRangeQuery->fetchAll(PDO::FETCH_ASSOC);

// Prepare response data
$response = [];
foreach ($salesData as $data) {
    if ($sortType === 'day') {
        $formattedDate = date('M d, Y', strtotime($data['date'])); // Convert to "Jan 01, 2024" format
        $response['labels'][] = $formattedDate;
        $response['sales'][] = $data['total'];
    } elseif ($sortType === 'week') {
        $monthName = date('M', mktime(0, 0, 0, $data['month'], 10)); // Convert month number to three-letter month name
        $response['labels'][] = "Week {$data['week_of_month']} of {$monthName}";
        $response['sales'][] = $data['total'];
    } elseif ($sortType === 'year') {
        $monthName = date('M', mktime(0, 0, 0, $data['month'], 10)); // Convert month number to three-letter month name
        $response['labels'][] = $monthName . ' ' . $data['year'];
        $response['sales'][] = $data['total'];
    }
}

echo json_encode($response);
?>
