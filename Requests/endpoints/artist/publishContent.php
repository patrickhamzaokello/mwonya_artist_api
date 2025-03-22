<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require '../../../Includes/config/Database.php';
require "../../../Includes/TableClasses/User.php";
require "../../../Includes/TableClasses/Artist.php";
require "../../../Includes/TableClasses/Album.php";
require "../../../Includes/TableClasses/Genre.php";
require "../../../Includes/TableClasses/Caption.php";
require "../../../Includes/TableClasses/Song.php";
require "../../../Includes/TableClasses/Playlist.php";
require "../../../Includes/TableClasses/SharedPlaylist.php";
require "../../../Includes/TableClasses/Shared.php";
require "../../../Includes/TableClasses/LikedSong.php";
require "../../../Includes/TableClasses/ArtistPick.php";
require "../../../Includes/TableClasses/ArtistEvents.php";
require "../../../Includes/TableClasses/PlaylistSlider.php";
require "../../../Includes/TableClasses/SearchSlider.php";
require "../../../Includes/TableClasses/Events.php";
require "../../../Includes/TableClasses/Constants.php";
require "../../../Includes/TableClasses/Account.php";
require "../../../Includes/TableClasses/TrackTotalPlay.php";
require "../../../Includes/TableClasses/WeeklyTopTracks.php";
require  "../../../Includes/TableClasses/PlaylistCoverGenerator.php";
include_once '../../../Includes/TableFunctions/Handler.php';


$database = new Database();
$db = $database->getConnection();

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

if (!isset($data) || (!isset($data['content_id']) || !isset($data['content_state']))) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Provide a valid content_id.',
    ]);
    exit;
}

try {
    // Handler setup
    $handler = new Handler($db);

    //convert content_state to integer
    $data['content_state'] = intval($data['content_state']);

    // Get user based on provided input
    $result = null;
    $result = $handler->publishContent($data['content_id'], $data['content_state']);


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
            'message' => 'Content not found.',
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
