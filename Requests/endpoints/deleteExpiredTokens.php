<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'includedFiles.php';

if (!isset($db) || empty($db)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection not established.',
    ]);
    exit;
}

// Parse input JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data) || (!isset($data['current_date']))) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Provide a valid date',
    ]);
    exit;
}

try {
    // Handler setup
    $handler = new Handler($db);

    // Get user based on provided input
    $result = null;
    $result = $handler->DeleteExpiredTokens($data['current_date']);

    // Respond based on result
    if ($result) {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'data' => $result,
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Error Found.',
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage(),
    ]);
}
?>
