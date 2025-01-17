<?php

class Handler
{

    private $ImageBasepath = "https://mwonyaa.com/";

    // track update info

    public function __construct($con)
    {
        $this->conn = $con;
        $this->version = 18; // VersionCode
    }

    
    public function getMwonyaCreatorByID($user_id){
        try {
            // Use a prepared statement to prevent SQL injection
            $query = "SELECT * FROM MwonyaCreators  WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->db->error);
            }

            $stmt->bind_param("i", $user_id); // Bind user_id as an integer
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $stmt->close();
            return $user; // Return user as associative array
        } catch (Exception $e) {
            error_log("Database error in getMwonyaCreatorByID: " . $e->getMessage());
            return null;
        }
    }

    public function getMwonyaCreatorByEmail($email){
        try {
            // Use a prepared statement to prevent SQL injection
            $query = "SELECT * FROM MwonyaCreators  WHERE email = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->db->error);
            }

            $stmt->bind_param("s", $email); // Bind email as a string
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $stmt->close();
            return $user; // Return user as associative array
        } catch (Exception $e) {
            error_log("Database error in getMwonyaCreatorByEmail: " . $e->getMessage());
            return null;
        }
    }

    public function getMwonyaVerificationTokenByToken($token){
        try {
            // Use a prepared statement to prevent SQL injection
            $query = "SELECT * FROM MwonyaVerificationToken  WHERE token = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            $stmt->bind_param("s", $token); 
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $stmt->close();
            return $user; // Return user as associative array
        } catch (Exception $e) {
            error_log("Database error in MwonyaVerificationToken: " . $e->getMessage());
            return null;
        }
    }

    public function getMwonyaVerificationTokenByEmail($email){
        try {
            // Use a prepared statement to prevent SQL injection
            $query = "SELECT * FROM MwonyaVerificationToken  WHERE email = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            $stmt->bind_param("s", $email); 
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $stmt->close();
            return $user; // Return user as associative array
        } catch (Exception $e) {
            error_log("Database error in MwonyaVerificationToken: " . $e->getMessage());
            return null;
        }
    }

    public function getMwonyaPasswordResetTokenByToken($token){
        try {
            // Use a prepared statement to prevent SQL injection
            $query = "SELECT * FROM MwonyaPasswordResetToken  WHERE token = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            $stmt->bind_param("s", $token); 
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $stmt->close();
            return $user; // Return user as associative array
        } catch (Exception $e) {
            error_log("Database error in MwonyaPasswordResetToken: " . $e->getMessage());
            return null;
        }
    }

    public function getMwonyaPasswordResetTokenByEmail($email){
        try {
            // Use a prepared statement to prevent SQL injection
            $query = "SELECT * FROM MwonyaPasswordResetToken  WHERE email = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            $stmt->bind_param("s", $email); 
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $stmt->close();
            return $user; // Return user as associative array
        } catch (Exception $e) {
            error_log("Database error in MwonyaPasswordResetToken: " . $e->getMessage());
            return null;
        }
    }

    public function getArtistDashboardMetrics($data){
        $artistId = $data['artistID'];
        $selected_metrics = isset($data['keyMetrics']) ? $data['keyMetrics'] : [];

        $sql = "SELECT s.artist_id, s.total_plays, s.unique_listeners, s.average_daily_plays, s.engagement_score,s.peak_listening_hours, s.song_variety, s.repeat_play_rate, s.repeat_listener_ratio, s.new_listeners_growth, s.current_play_count, h.metric_type, h.old_value, h.new_value, h.changed_at FROM artist_statistics_summary s LEFT JOIN artist_statistics_history h ON s.artist_id = h.artist_id AND h.changed_at = (SELECT MAX(changed_at) FROM artist_statistics_history WHERE artist_id = s.artist_id AND metric_type = h.metric_type) WHERE s.artist_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        $stmt->bind_param("s", $artistId);
        $stmt->execute();

        $rows = $stmt->get_result();
        $response = [];

        if ($rows->num_rows === 0) {
            echo json_encode(["message" => "No data found for the specified artist."], JSON_PRETTY_PRINT);
            return;
        }
        
        // Fetch data and build the response
        $response = [];
        $metricsMap = [
            'total_plays' => 'Total Plays (Last Updated)',
            'unique_listeners' => 'Unique Listeners (Last Updated)',
            'average_daily_plays' => 'Average Daily Plays (Last Updated)',
            'engagement_score' => 'Engagement Score (Last Updated)',
            'current_play_count' => 'Play Count (Last Updated)',
            'peak_listening_hours'=> 'Peak Hours (Last Updated)',
            'new_listeners_growth'=> 'Listeners Growth (Last Updated)',
            'repeat_play_rate'=> 'Repeat Play Rate (Last Updated)',
            'repeat_listener_ratio'=> 'Repeat Listener Ratio (Last Updated)',
            'song_variety'=> 'Track Diversity (Last Updated)',

        ];
        
        while ($row = $rows->fetch_assoc()) {
            $metric = $row['metric_type'];
            
            if(empty($selected_metrics) || in_array($metric, $selected_metrics)){
                $value = $row[$metric] ?? 0; // Default to 0 if value is null
                $changeAmount = $row['new_value'] - $row['old_value'];
                if ($row['old_value'] != 0) {
                    $changePercentage = round(($changeAmount / $row['old_value']) * 100, 2);
                } else {
                    // If old_value is zero, set change percentage to 0 or handle it differently if needed
                    $changePercentage = 0;
                }
                $trend = $changeAmount > 0 ? 'up' : ($changeAmount < 0 ? 'down' : 'neutral');
                $lastUpdated = date('M j, Y', strtotime($row['changed_at']));
            
                $response[] = [
                    'title' => $metricsMap[$metric],
                    'value' => number_format($value),
                    'change' => [
                        'amount' => ($changeAmount >= 0 ? '+' : '') . number_format($changeAmount),
                        'percentage' => ($changePercentage >= 0 ? '+' : '') . $changePercentage . '%'
                    ],
                    'lastUpdated' => $lastUpdated,
                    'trend' => $trend
                ];
            }
        }


        $stmt->close();
        return $response;

    }


    public function getArtistTotalPlayTrend($data) {
        $artistId = $data['artistID'];
        $year = isset($data['year']) ? $data['year'] : date('Y');

        $jsonData = file_get_contents('/var/www/html/artistapi.mwonya.com/development/Includes/generated_json_data/artist_trends.json');
        $data = json_decode($jsonData, true);
        $filteredData = [];

    
        // Filter data by artist ID and year
        foreach ($data as $record) {
            if ($record['id'] == $artistId && substr($record['month'], 0, 4) == $year) {
                $filteredData[] = $record;
            }
        }
    
        // Sort the filtered data by month
        usort($filteredData, function($a, $b) {
            return strcmp($a['month'], $b['month']);
        });
    
        // Prepare result with month and total plays
        $result = [];
        foreach ($filteredData as $record) {
            $date = DateTime::createFromFormat('Y-m', $record['month']);
            $formattedMonth = $date->format('M');
            $result[] = [
                'name' => $formattedMonth,
                'total' => round($record['total_plays_pct_change'])
            ];
        }
    
        return $result;
    
    }
    public function createVerificationToken($data){
        #get the  email, token, expires
        $email = $data['email'];
        $token = $data['token'];
        $expires = $data['expires'];

        try {
              // Prepare the insert query
            $insert_query = "INSERT INTO MwonyaVerificationToken (email, token, expires) 
            VALUES (?, ?, ?)";

            $insert_stmt = $this->conn->prepare($insert_query);
            if (!$insert_stmt) {
            throw new Exception("Failed to prepare insert statement: " . $this->conn->error);
            }

            // Bind parameters to the insert query
            $insert_stmt->bind_param("sss", $email, $token, $expires);

            // Execute the query
            $insert_stmt->execute();
            $insert_stmt->close();

            // Return success response
            return [
            'status' => 'success',
            'message' => [
                'email' => $email,
                'token' => $token,
                'expires' => $expires
                ]
            ];

        } catch (Exception $e){
            error_log("Database error in VerificationToken: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function createPasswordResetToken($data){
        #get the  email, token, expires
        $email = $data['email'];
        $token = $data['token'];
        $expires = $data['expires'];
        try {
              // Prepare the insert query
            $insert_query = "INSERT INTO MwonyaPasswordResetToken (email, token, expires) 
            VALUES (?, ?, ?)";

            $insert_stmt = $this->conn->prepare($insert_query);
            if (!$insert_stmt) {
            throw new Exception("Failed to prepare insert statement: " . $this->conn->error);
            }

            // Bind parameters to the insert query
            $insert_stmt->bind_param("sss", $email, $token, $expires);

            // Execute the query
            $insert_stmt->execute();
            $insert_stmt->close();

            // Return success response
            return [
            'status' => 'success',
            'message' => [
                'email' => $email,
                'token' => $token,
                'expires' => $expires
                ]
            ];

        } catch (Exception $e){
            error_log("Database error in PasswordReset: " . $e->getMessage());
            return $e->getMessage();
        }
    }


    public function registerMwonyaCreator($data){
        $email = $data['email'];
        $username = $data['username'];
        $phone_number = $data['phone_number'];
        $password = $data['password'];
        $creator_role = $data['creator_role'];

        try {
              // Prepare the insert query
            $insert_query = "INSERT INTO MwonyaCreators (username, email, phone, password, role) 
            VALUES (?, ?, ?, ?, ?)";

            $insert_stmt = $this->conn->prepare($insert_query);
            if (!$insert_stmt) {
            throw new Exception("Failed to prepare insert statement: " . $this->conn->error);
            }

            // Bind parameters to the insert query
            $insert_stmt->bind_param("sssss", $username, $email, $phone_number, $password, $creator_role);

            // Execute the query
            $insert_stmt->execute();
            $insert_stmt->close();

            // Return success response
            return [
            'status' => 'success',
            'message' => 'Creator registered successfully.'
            ];

        } catch (Exception $e){
            error_log("Database error in getMwonyaCreatorByEmail: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function deleteTokenbyID($token_id){
        // Prepare the delete query
        $query = "DELETE FROM MwonyaVerificationToken WHERE id =  ?";
        
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        
        // Bind the current date parameter
        $stmt->bind_param("s", $token_id);

        // Execute the query
        if ($stmt->execute()) {
            $deleted_rows = $stmt->affected_rows; // Get the number of deleted rows
            $stmt->close();
            return [
                'status' => 'success',
                'message' => "$deleted_rows tokens deleted successfully."
            ];
        } else {
            $stmt->close();
            return [
                'status' => 'error',
                'message' => "Failed to delete token with id: $token_id."
            ];
        }
    }

    public function updateEmailVerified($data) {
        $email = $data['email'];
        $user_id = $data['user_id'];
        $emailVerified = $data['emailVerifiedDate'];
    
        try {
            // Prepare the update query
            $update_query = "UPDATE MwonyaCreators SET emailVerified = ?, email = ?, updated_at = NOW() WHERE id = ?";
            $update_stmt = $this->conn->prepare($update_query);
    
            if (!$update_stmt) {
                throw new Exception("Failed to prepare update statement: " . $this->conn->error);
            }
    
            // Bind parameters to the query
            $update_stmt->bind_param("sss", $emailVerified, $email, $user_id);
    
            // Execute the query
            $update_stmt->execute();
    
            // Check if any rows were affected
            if ($update_stmt->affected_rows > 0) {
                $update_stmt->close();
                return [
                    'status' => 'success',
                    'message' => 'Email verified successfully.'
                ];
            } else {
                $update_stmt->close();
                return [
                    'status' => 'error',
                    'message' => 'No record found to update or no changes were made.'
                ];
            }
        } catch (Exception $e) {
            // Log the error and return a descriptive message
            error_log("Database error in updateEmailVerified: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function deletePasswordResetTokenbyID($id){
        // Prepare the delete query
        $query = "DELETE FROM MwonyaPasswordResetToken WHERE id =  ?";
        
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        
        // Bind the current date parameter
        $stmt->bind_param("s", $id);

        // Execute the query
        if ($stmt->execute()) {
            $deleted_rows = $stmt->affected_rows; // Get the number of deleted rows
            $stmt->close();
            return [
                'status' => 'success',
                'message' => "$deleted_rows tokens deleted successfully."
            ];
        } else {
            $stmt->close();
            return [
                'status' => 'error',
                'message' => "Failed to delete token with id: $id."
            ];
        }
    }


    public function DeleteExpiredPasswordTokens($current_date){
        // Prepare the delete query
        $query = "DELETE FROM MwonyaPasswordResetToken WHERE date(expires) < ?";
        
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        
        // Bind the current date parameter
        $stmt->bind_param("s", $current_date);

        // Execute the query
        if ($stmt->execute()) {
            $deleted_rows = $stmt->affected_rows; // Get the number of deleted rows
            $stmt->close();
            return [
                'status' => 'success',
                'message' => "$deleted_rows expired tokens deleted successfully."
            ];
        } else {
            $stmt->close();
            return [
                'status' => 'error',
                'message' => 'Failed to delete expired tokens.'
            ];
        }
    }


    public function DeleteExpiredTokens($current_date){
        // Prepare the delete query
        $query = "DELETE FROM MwonyaVerificationToken WHERE date(expires) < ?";
        
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        
        // Bind the current date parameter
        $stmt->bind_param("s", $current_date);

        // Execute the query
        if ($stmt->execute()) {
            $deleted_rows = $stmt->affected_rows; // Get the number of deleted rows
            $stmt->close();
            return [
                'status' => 'success',
                'message' => "$deleted_rows expired tokens deleted successfully."
            ];
        } else {
            $stmt->close();
            return [
                'status' => 'error',
                'message' => 'Failed to delete expired tokens.'
            ];
        }
    }


    public function getTopArtistTracks($data) {
        $artistID = isset($data['artistID']) ? $data['artistID'] : "";
        try {

            $query = "SELECT s.id AS song_id, s.title AS song_title, al.title AS album_name, al.artworkPath AS album_cover, g.name AS genre_name, a.name AS artist_name, SUM(f.plays) AS total_plays FROM songs s JOIN artists a ON s.artist = a.id JOIN albums al ON s.album = al.id JOIN genres g ON s.genre = g.id JOIN frequency f ON s.id = f.songid WHERE a.id = ? AND f.dateUpdated >= NOW() - INTERVAL 30 DAY GROUP BY s.id, s.title, al.title, al.artworkPath, g.name, a.name ORDER BY total_plays DESC LIMIT 5";

  
            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            // Bind the user_id parameter if the user is not an admin
            $stmt->bind_param("s", $artistID);
            $stmt->execute();

            $result = $stmt->get_result();

            // Fetch all results and map them to the desired structure
            $artists = [];
            while ($row = $result->fetch_assoc()) {
                $artists[] = [
                    'song_id' => $row['song_id'],
                    'song_title' => $row['song_title'],
                    'album_name' => $row['album_name'],
                    'album_cover' => $row['album_cover'],
                    'genre_name' => $row['genre_name'],
                    'artist_name' => $row['artist_name'],
                    'total_plays' => $row['total_plays'],
                ];
            }

            $stmt->close();
        

            return $artists;
            } catch (Exception $e) {
                error_log("Database error in getCreatorArtistProfiles: " . $e->getMessage());
                return null;
            }
    }


    public function getCreatorArtistProfiles($data)
{

    $user_id = isset($data['user_id']) ? $data['user_id'] : "";
    $userRole = isset($data['userRole']) ? $data['userRole'] : "";

    try {
        // SQL query to fetch required artist data with genre details
        if ($userRole === 'admin') {
            $query = "
            SELECT 
                a.id, 
                a.name, 
                g.id AS genre_id, 
                g.name AS genre_name, 
                a.bio AS biography, 
                a.verified, 
                a.profilephoto AS profile_image_url, 
                a.coverimage AS cover_image_url
            FROM 
                artists a
            LEFT JOIN 
                genres g 
            ON 
                a.genre = g.id
            ORDER BY 
                a.name ASC";
        } 
        else {
            $query = "
            SELECT 
                a.id, 
                a.name, 
                g.id AS genre_id, 
                g.name AS genre_name, 
                a.bio AS biography, 
                a.verified, 
                a.profilephoto AS profile_image_url, 
                a.coverimage AS cover_image_url
            FROM 
                Creator_Artist ca
            INNER JOIN 
                artists a 
            ON 
                ca.artist_id = a.id
            LEFT JOIN 
                genres g 
            ON 
                a.genre = g.id
            WHERE 
                ca.creator_id = ?
            ORDER BY 
                a.name ASC";
        }
    
        
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

      // Bind the user_id parameter if the user is not an admin
        if ($userRole !== 'admin') {
            $stmt->bind_param("i", $user_id);
        }
        $stmt->execute();

        $result = $stmt->get_result();

        // Fetch all results and map them to the desired structure
        $artists = [];
        while ($row = $result->fetch_assoc()) {
            $artists[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'biography' => $row['biography'] ?? null,
                'verified' => $row['verified'] ? true : null,
                'genre' => $row['genre_id'] ? [
                    'id' => $row['genre_id'],
                    'name' => $row['genre_name']
                ] : null,
                'profileImage' => $row['profile_image_url'] ? [
                    'fileUrl' => $row['profile_image_url']
                ] : null,
                'coverImage' => $row['cover_image_url'] ? [
                    'fileUrl' => $row['cover_image_url']
                ] : null
            ];
        }

        $stmt->close();
        return $artists; // Return an array of artist objects
    } catch (Exception $e) {
        error_log("Database error in getCreatorArtistProfiles: " . $e->getMessage());
        return null;
    }
}

public function getArtistLiveData($data) {
    $artistID = isset($data['artistID']) ? $data['artistID'] : "";
    $isVerified = isset($data['isVerified']) ? $data['isVerified'] : "";

    // Define revenue multiplier based on verification status
    $revenueMultiplier = ($isVerified == 1 ? 0.008 : 0.006);

    // Initialize response array
    $response = [];

    // Get total plays for the artist
    $query = "SELECT SUM(tp.total_plays) as total_listeners FROM track_plays tp WHERE tp.songid IN (SELECT id FROM songs WHERE artist = ?)";
    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $this->conn->error);
    }
    $stmt->bind_param("s", $artistID);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_plays = (int) $result->fetch_assoc()['total_listeners'];
    $response['total_plays'] = $total_plays;
    $stmt->close();

    // Calculate total stream revenue
    $revenue = round($total_plays * $revenueMultiplier);
    $response['revenue'] = $revenue;


    // Get total releases for the artist
    $query = "SELECT COUNT(*) as total_releases FROM albums WHERE artist = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $artistID);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_releases = $result->fetch_assoc()['total_releases'];
    $response['total_releases'] = $total_releases;
    $stmt->close();

    // Get total tracks for the artist
    $query = "SELECT COUNT(*) as total_tracks FROM songs WHERE artist = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $artistID);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_tracks = $result->fetch_assoc()['total_tracks'];
    $response['total_tracks'] = $total_tracks;
    $stmt->close();

    // Get total collection from artist circle subscription
    $query = "SELECT SUM(pps.amount) AS total_circle_amount FROM pesapal_payment_status pps JOIN pesapal_transactions pt ON pps.merchant_reference = pt.merchant_reference WHERE pt.subscription_type_id = ? AND pps.status_code = 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $artistID);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_circle_amount = round($result->fetch_assoc()['total_circle_amount']);
    $response['total_circle_amount'] = $total_circle_amount;
    $stmt->close();

     $response = [
        [
            'value' => $total_plays,
            'label' => 'Total Streams',
            'icon' => 'Music',
            'color' => 'bg-purple-500'
        ],
        [
            'value' => $revenue,
            'label' => 'Stream Revenue',
            'icon' => 'DollarSign',
            'prefix' => 'Ugx',
            'color' => 'bg-blue-500'
        ],
        [
            'value' => $total_circle_amount,
            'label' => 'Artist Circle',
            'icon' => 'Users',
            'prefix' => 'Ugx',
            'color' => 'bg-pink-500'
        ],
        [
            'value' => $total_releases,
            'label' => 'Releases',
            'icon' => 'Disc',
            'color' => 'bg-orange-500'
        ],
        [
            'value' => $total_tracks,
            'label' => 'Tracks',
            'icon' => 'Music2',
            'color' => 'bg-green-500'
        ]
    ];

    return $response;
}

public function getArtistDiscovery($artist_id)
{
    try {
        // SQL query to fetch album and related information with artist and genre details
        $query = "
            SELECT 
                al.id, 
                al.title, 
                al.releaseDate, 
                YEAR(al.releaseDate) AS release_year, 
                al.tag,
                al.AES_code, 
                al.exclusive, 
                g.name AS genre,
                al.artworkPath AS artwork_path,
                COUNT(s.id) AS total_songs
            FROM 
                albums al
            LEFT JOIN 
                artists a ON al.artist = a.id
            LEFT JOIN 
                genres g ON a.genre = g.id
            LEFT JOIN 
                songs s ON s.album = al.id
            WHERE 
                al.artist = ?
            GROUP BY 
                al.id
            ORDER BY 
                al.releaseDate DESC";
        
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        // Bind the artist_id parameter as an integer
        $stmt->bind_param("s", $artist_id);
        $stmt->execute();

        $result = $stmt->get_result();

        // Fetch all results and map them to the desired structure
        $albums = [];
        while ($row = $result->fetch_assoc()) {
            $albums[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'releaseDate' => $row['releaseDate'],
                'releaseYear' => $row['release_year'],
                'tag' => $row['tag'],
                'AES_code' => $row['AES_code'],
                'exclusive' => $row['exclusive'] ? true : false,
                'genre' => $row['genre'] ?? null,
                'artwork' => $row['artwork_path'] ? [
                    'fileUrl' => $row['artwork_path']
                ] : null,
                'totalSongs' => $row['total_songs']
            ];
        }

        $stmt->close();
        return $albums; // Return an array of album objects
    } catch (Exception $e) {
        error_log("Database error in getArtistDiscovery: " . $e->getMessage());
        return null;
    }
}

public function createNewRelease($releaseDetails){
    try {
        // Begin a transaction
        $this->conn->begin_transaction();

        // Insert into albums table
        $albumQuery = "
            INSERT INTO albums 
                (`id`, `title`, `artist`, `genre`, `exclusive`, `tag`, `description`, `datecreated`, `releaseDate`, `AES_code`) 
            VALUES 
                (?, ?, ?, ?,?, ?, ?, NOW(), ?, ?)";  // Available set to 0 by default
        
        $albumStmt = $this->conn->prepare($albumQuery);
        
        if (!$albumStmt) {
            throw new Exception("Failed to prepare album statement: " . $this->conn->error);
        }


        $albumStmt->bind_param(
            "ssssissss",
            $releaseDetails['releaseID'],
            $releaseDetails['release_title'],
            $releaseDetails['artist'],
            $releaseDetails['genre'],
            $releaseDetails['exclusive'],
            $releaseDetails['releaseType'],
            $releaseDetails['description'],
            $releaseDetails['releaseDate'],
            $releaseDetails['aesCode'],  // AES_code specifies whether it's a single, album, or EP
        );

        if (!$albumStmt->execute()) {
            throw new Exception("Failed to execute album query: " . $albumStmt->error);
        }

        // Commit the transaction
        $this->conn->commit();

        return [
            'success' => true,
            'message' => 'New Release added successfully',
            'releaseID' =>  $releaseDetails['releaseID']
        ];

    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $this->conn->rollback();
        error_log("Database error in new release: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Database error in new release: " . $e->getMessage()
        ];
    }
}


public function addSingleTrackWithAlbum($trackDetails)
{
    try {
        // Begin a transaction
        $this->conn->begin_transaction();

        // Insert into albums table
        $albumQuery = "
            INSERT INTO albums 
                (`id`, `title`, `artist`, `genre`, `exclusive`, `tag`, `description`, `datecreated`, `releaseDate`, `AES_code`) 
            VALUES 
                (?, ?, ?, ?,?, ?, ?, NOW(), ?, ?)";  // Available set to 0 by default
        
        $albumStmt = $this->conn->prepare($albumQuery);
        
        if (!$albumStmt) {
            throw new Exception("Failed to prepare album statement: " . $this->conn->error);
        }

        $albumStmt->bind_param(
            "ssssissss",
            $trackDetails['album_id'],
            $trackDetails['album_title'],
            $trackDetails['artist'],
            $trackDetails['genre'],
            $trackDetails['exclusive'],
            $trackDetails['tag'],
            $trackDetails['description'],
            $trackDetails['releaseDate'],
            $trackDetails['AES_code'],  // AES_code specifies whether it's a single, album, or EP
        );

        if (!$albumStmt->execute()) {
            throw new Exception("Failed to execute album query: " . $albumStmt->error);
        }

        // Insert into songs table
        $songQuery = "
            INSERT INTO songs 
                (`title`, `reference`, `artist`, `album`, `genre`, `duration`, `tag`, `producer`, `songwriter`, 
                 `labels`, `description`, `dateAdded`, `releaseDate`) 
            VALUES 
                (?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?, NOW(), ?)";  // Available set to 0 by default
        
        $songStmt = $this->conn->prepare($songQuery);

        if (!$songStmt) {
            throw new Exception("Failed to prepare song statement: " . $this->conn->error);
        }

        $songStmt->bind_param(
            "ssssssssssss",
            $trackDetails['title'],
            $trackDetails['track_reference'],
            $trackDetails['artist'],
            $trackDetails['album_id'],
            $trackDetails['genre'],
            $trackDetails['duration'],
            $trackDetails['tag'],
            $trackDetails['producer'],
            $trackDetails['songwriter'],
            $trackDetails['labels'],
            $trackDetails['description'],
            $trackDetails['releaseDate'],
        );

        if (!$songStmt->execute()) {
            throw new Exception("Failed to execute song query: " . $songStmt->error);
        }

        // Commit the transaction
        $this->conn->commit();

        return [
            'success' => true,
            'message' => 'Single track and content added successfully',
            'track_id' => $trackDetails['track_reference'],
            'album_id' =>  $trackDetails['album_id']
        ];

    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $this->conn->rollback();
        error_log("Database error in addSingleTrackWithcontent: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Database error in addSingle TrackWithcontent:  and content: " . $e->getMessage()
        ];
    }
}



public function getContentDetailsByID($content_id)
{
    try {
        // SQL query to fetch album details
        $albumQuery = "
            SELECT 
                al.id AS content_id,
                al.title,
                a.name AS artist,
                al.artworkPath AS imageUrl,
                g.name AS category,
                al.description,
                al.releaseDate
            FROM 
                albums al
            LEFT JOIN 
                artists a ON al.artist = a.id
            LEFT JOIN 
                genres g ON a.genre = g.id
            WHERE 
                al.id = ?";

        $stmt = $this->conn->prepare($albumQuery);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        // Bind the content_id parameter as an integer
        $stmt->bind_param("s", $content_id);
        $stmt->execute();

        $albumResult = $stmt->get_result();
        
        $album = $albumResult->fetch_assoc();

        if (!$album) {
            throw new Exception("Album not found for content_id: $content_id");
        }

        // SQL query to fetch tracks for the album
        $tracksQuery = "
            SELECT 
                title,
                duration,
                path as trackFilePath
            FROM 
                songs
            WHERE 
                album = ?";
        
        $tracksStmt = $this->conn->prepare($tracksQuery);

        if (!$tracksStmt) {
            throw new Exception("Failed to prepare statement for tracks: " . $this->conn->error);
        }

        // Bind the content_id parameter to fetch tracks
        $tracksStmt->bind_param("s", $content_id);
        $tracksStmt->execute();

        $tracksResult = $tracksStmt->get_result();
        $tracks = [];

        while ($track = $tracksResult->fetch_assoc()) {
            $tracks[] = [
                'title' => $track['title'],
                'duration' => $track['duration'],
                'trackFilePath' => $track['trackFilePath']
            ];
        }

        $tracksStmt->close();



        // Add duration calculation
        $totalDurationQuery = "
            SELECT 
                SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) AS total_duration
            FROM 
                songs
            WHERE 
                album = ?";
        
        $durationStmt = $this->conn->prepare($totalDurationQuery);

        if (!$durationStmt) {
            throw new Exception("Failed to prepare statement for duration: " . $this->conn->error);
        }

        $durationStmt->bind_param("s", $content_id);
        $durationStmt->execute();

        $durationResult = $durationStmt->get_result();
        $totalDuration = $durationResult->fetch_assoc()['total_duration'];
        $durationStmt->close();

        $stmt->close();


        // Format the response
        return [
            'content_id' => $album['content_id'],
            'title' => $album['title'],
            'artist' => $album['artist'] ?? 'Various Artists',
            'imageUrl' => $album['imageUrl'],
            'category' => $album['category'] ?? 'Unknown',
            'description' => $album['description'] ?? 'No description available.',
            'releaseDate' => $album['releaseDate'],
            'duration' => $totalDuration ?? '0h 0m',
            'tracks' => $tracks
        ];
    } catch (Exception $e) {
        error_log("Database error in getContentDetailsByID: " . $e->getMessage());
        return null;
    }
}

public function updateTrack_CoverImageMediaUpload($referenceId ,$fileType,$awsUrl, $upload_status){
    // $fileType = // 'track' or 'coverArt'
    
    try {
        // Prepare the update query based on fileType
        if ($fileType === 'track') {
            $update_query = "UPDATE songs SET path = ?, upload_status = ?, updated_at = NOW() WHERE reference = ?";
        } elseif ($fileType === 'coverArt') {
            $update_query = "UPDATE albums SET artworkPath = ?, upload_status = ?,  updated_at = NOW() WHERE id = ?";
        } else {
            throw new Exception("Invalid file type provided.");
        }

        // Prepare the statement
        $update_stmt = $this->conn->prepare($update_query);

        if (!$update_stmt) {
            throw new Exception("Failed to prepare update statement: " . $this->conn->error);
        }

        // Bind parameters to the query
        $update_stmt->bind_param("sss", $awsUrl, $upload_status, $referenceId);

        // Execute the query
        $update_stmt->execute();

        // Check if any rows were affected
        if ($update_stmt->affected_rows > 0) {
            $update_stmt->close();
            return [
                'status' => 'success',
                'message' => $fileType. "Record updated successfully."
            ];
        } else {
            $update_stmt->close();
            return [
                'status' => 'error',
                'message' => $fileType. "No record found to update or no changes were made."
            ];
        }
    } catch (Exception $e) {
        // Log the error and return a descriptive message
        error_log("Database error in updateTrackOrCoverArt: " . $e->getMessage());
        return [
            'status' => $fileType. 'error',
            'message' => $e->getMessage()
        ];
    }
}

public function getAllGenre(){
    try {
        $query = "SELECT id, name, tag FROM genres ORDER BY name asc";
        
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        $artists = [];
        while ($row = $result->fetch_assoc()) {
            $artists[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'tag' => $row['tag']
            ];
        }

        $stmt->close();
        return $artists; 
    } catch (Exception $e) {
        error_log("Database error in getAllGenre: " . $e->getMessage());
        return null;
    }
}



    
   
}
