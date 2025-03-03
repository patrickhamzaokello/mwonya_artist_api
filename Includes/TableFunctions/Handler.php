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
    
        // Set the default rolling period to 12 months if not specified
        $months = isset($data['months']) ? (int)$data['months'] : 12;
    
        // Validate the rolling period to ensure it's a positive number
        if ($months <= 0) {
            throw new Exception("Rolling period must be a positive number.");
        }
    
        // Load JSON data
        $jsonData = file_get_contents('/var/www/html/artistapi.mwonya.com/development/Includes/generated_json_data/artist_trends.json');
        $data = json_decode($jsonData, true);
    
        // Filter data by artist ID
        $filteredData = array_filter($data, function($record) use ($artistId) {
            return $record['id'] == $artistId;
        });
    
        // Create a lookup table for the specified rolling period (earliest to most recent)
        $currentMonth = new DateTime('now');
        $monthLookup = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthKey = $currentMonth->format('Y-m');
            $monthLookup[$monthKey] = [
                'name' => $currentMonth->format('M Y'),
                'total' => 0
            ];
            $currentMonth->modify('-1 month');
        }
    
        // Reverse the lookup table to ensure chronological order
        $monthLookup = array_reverse($monthLookup, true);
    
        // Merge with available data
        foreach ($filteredData as $record) {
            $monthKey = $record['month'];
            if (isset($monthLookup[$monthKey])) {
                $monthLookup[$monthKey]['total'] = round($record['total_plays']);
            }
        }
    
        // Return the processed data as an array
        return array_values($monthLookup);
    }



    public function Creator_artistExists($creator_ID) {
    
        if (!$creator_ID) {
            throw new Exception("creator_id must be provided.");
        }
    
        // Prepare the query based on the provided parameter
        $query = "SELECT 1 FROM Creator_Artist WHERE creator_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
    
        $stmt->bind_param("s", $creator_ID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $exists = $result->num_rows > 0;
        $stmt->close();
    
        return $exists;
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

    public function updateCreatorPassword($data) {
        $password = $data['password'];
        $user_id = $data['user_id'];
        $user_email = $data['user_email'];
    
        try {
            // Prepare the update query
            $update_query = "UPDATE MwonyaCreators SET password = ?, updated_at = NOW() WHERE id = ? and email = ?";
            $update_stmt = $this->conn->prepare($update_query);
    
            if (!$update_stmt) {
                throw new Exception("Failed to prepare update statement: " . $this->conn->error);
            }
    
            // Bind parameters to the query
            $update_stmt->bind_param("sss", $password, $user_id, $user_email);
    
            // Execute the query
            $update_stmt->execute();
    
            // Check if any rows were affected
            if ($update_stmt->affected_rows > 0) {
                $update_stmt->close();
                return [
                    'status' => 'success',
                    'message' => 'Password updated successfully.'
                ];
            } else {
                $update_stmt->close();
                return [
                    'status' => 'error',
                    'message' => 'Invalid user credentials, password reset failed to complete.'
                ];
            }
        } catch (Exception $e) {
            // Log the error and return a descriptive message
            error_log("Database error in updateCreatorPassword: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
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


    public function listCreatorArtistProfiles($data) {

        $user_id = isset($data['creatorID']) ? $data['creatorID'] : "";
        $userRole = isset($data['creatorRole']) ? $data['creatorRole'] : "";
        try {
            // Validate input
            if (empty($user_id) || empty($userRole)) {
                throw new Exception("Invalid input.");
            }
    
            // Begin transaction
            $this->conn->begin_transaction();
    
              // Check if the creator exists
              $creatorCheckQuery = "SELECT `id` FROM `MwonyaCreators` WHERE `role`=? and `id` = ?";
              $creatorCheckStmt = $this->conn->prepare($creatorCheckQuery);
      
              if (!$creatorCheckStmt) {
                  throw new Exception("Failed to prepare artist check statement: " . $this->conn->error);
              }
      
              $creatorCheckStmt->bind_param("ss", $userRole, $user_id);
              $creatorCheckStmt->execute();
              $creatorCheckStmt->store_result();
      
              if ($creatorCheckStmt->num_rows === 0) {
                  throw new Exception("Creator  does not exist.");
              }
    
    
              if ($userRole === 'admin') {
                $query = "
               SELECT a.id, a.name, g.name AS genre_name, a.bio AS biography, a.verified, pf.file_path AS profile_image_url, cf.file_path AS cover_image_url, a.facebookurl AS facebook_url, a.twitterurl AS twitter_url, a.instagramurl AS instagram_url, a.youtubeurl AS youtube_url FROM artists a LEFT JOIN genres g ON a.genre = g.id LEFT JOIN Uploads pf ON a.profile_image_id = pf.upload_id LEFT JOIN Uploads cf ON a.cover_image_id = cf.upload_id ORDER BY a.datecreated DESC";
            } 
            else {
                $query = "
               SELECT a.id, a.name, g.name AS genre_name, a.bio AS biography, a.verified, pf.file_path AS profile_image_url, cf.file_path AS cover_image_url, a.facebookurl AS facebook_url, a.twitterurl AS twitter_url, a.instagramurl AS instagram_url, a.youtubeurl AS youtube_url FROM artists a LEFT JOIN genres g ON a.genre = g.id LEFT JOIN Uploads pf ON a.profile_image_id = pf.upload_id LEFT JOIN Uploads cf ON a.cover_image_id = cf.upload_id JOIN Creator_Artist ca on a.id = ca.artist_id WHERE ca.creator_id = ? ORDER BY a.datecreated DESC"; 
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
                    'biography' => $row['biography'] ?? "",
                    'verified' => $row['verified'] ? true : null,
                    'genre' => $row['genre_name'] ?? "",
                    'profileImage' => $row['profile_image_url'] ?? "",
                    'coverImage' => $row['cover_image_url'] ?? "",
                    'socialLinks' =>  [
                        'facebook_url' => $row['facebook_url']?? "",
                        'twitter_url' => $row['twitter_url']?? "",
                        'instagram_url' => $row['instagram_url']?? "",
                        'youtube_url' => $row['youtube_url ']?? ""
                    ],
                   
                ];
            }
    
            $stmt->close();
            return $artists; // Return an array of artist objects
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $this->conn->rollback();
            error_log("Error updating artist profile image: " . $e->getMessage());
    
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }




    public function getArtistQuery($search_query)
{
    if (!$search_query) {
        throw new Exception("Query must be provided.");
    }

    try {
        $query = "SELECT a.id, a.name, u.file_path AS imageUrl 
                  FROM `artists` a 
                  JOIN Uploads u ON u.upload_id = a.profile_image_id 
                  WHERE a.available = 1 AND a.name LIKE ?";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        // Add wildcards to the search query for the LIKE clause
        $search_param = "%" . $search_query . "%";
        $stmt->bind_param("s", $search_param);

        $stmt->execute();
        $result = $stmt->get_result();

        $artists = [];
        while ($row = $result->fetch_assoc()) {
            $artists[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'imageUrl' => $row['imageUrl'] ?? null,
            ];
        }

        $stmt->close();
        return $artists; 
    } catch (Exception $e) {
        error_log("Database error in getArtistQuery: " . $e->getMessage());
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
                up_profile.file_path AS profile_image_url, 
                up_cover.file_path AS cover_image_url
            FROM 
                artists a
            LEFT JOIN 
                genres g 
            ON 
                a.genre = g.id
            LEFT JOIN
                Uploads up_profile
            ON
                a.profile_image_id = up_profile.upload_id
            LEFT JOIN
                Uploads up_cover
            ON
                a.cover_image_id = up_cover.upload_id
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
                up_profile.file_path AS profile_image_url, 
                up_cover.file_path AS cover_image_url
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
            LEFT JOIN
                Uploads up_profile
            ON
                a.profile_image_id = up_profile.upload_id
            LEFT JOIN
                Uploads up_cover
            ON
                a.cover_image_id = up_cover.upload_id
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

public function getArtistLastRelease($artist_id) {
    // Get the last release album
    $query = "SELECT a.`id`, a.`title`, a.`artist`, ar.name AS artistName, a.artworkPath, a.`genre`, a.`releaseDate`, a.`exclusive` 
              FROM `albums` a 
              JOIN `artists` ar ON a.`artist` = ar.`id` 
              WHERE a.`artist` = ? AND a.`available` = 1 AND a.`releaseDate` IS NOT NULL 
              ORDER BY a.`releaseDate` DESC 
              LIMIT 1";
              
    $stmt = $this->conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $this->conn->error);
    }

    $stmt->bind_param("s", $artist_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $album = $result->fetch_assoc();

    if (!$album) {
        return null; // No album found for the artist
    }

    $album_id = $album['id'];

    // Track Performance for Songs in the Latest Album
    $track_query = "SELECT 
                        s.`title` AS songTitle,
                        SUM(f.`plays`) AS totalPlays,
                        COUNT(DISTINCT f.`userid`) AS uniqueListeners,
                        AVG(f.`listened_duration`) AS avgListenDuration
                    FROM `songs` s
                    LEFT JOIN `frequency` f ON s.`id` = f.`songid`
                    WHERE s.`album` = ?
                    GROUP BY s.`id`";
                    
    $track_stmt = $this->conn->prepare($track_query);

    if (!$track_stmt) {
        throw new Exception("Failed to prepare track statement: " . $this->conn->error);
    }

    $track_stmt->bind_param("s", $album_id);
    $track_stmt->execute();

    $track_result = $track_stmt->get_result();
    $tracks = [];

    while ($track = $track_result->fetch_assoc()) {
        $tracks[] = $track;
    }

    // Total Album Performance
    $album_query = "SELECT 
                        SUM(f.`plays`) AS albumTotalPlays,
                        COUNT(DISTINCT f.`userid`) AS albumUniqueListeners,
                        AVG(f.`listened_duration`) AS avgListenDurationPerTrack
                    FROM `songs` s
                    LEFT JOIN `frequency` f ON s.`id` = f.`songid`
                    WHERE s.`album` = ?";
                    
    $album_stmt = $this->conn->prepare($album_query);

    if (!$album_stmt) {
        throw new Exception("Failed to prepare album performance statement: " . $this->conn->error);
    }

    $album_stmt->bind_param("s", $album_id);
    $album_stmt->execute();

    $album_performance = $album_stmt->get_result()->fetch_assoc();

    // Combine data into a single array for return
    $album['tracks'] = $tracks;
    $album['performance'] = $album_performance;

    return $album;
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



public function updateArtistProfileImage($artistId,  $imageType,$mediaId, $creator_id) {
    try {
        // Validate input
        if (empty($artistId) || empty($mediaId) || !in_array($imageType, ['profile_image', 'cover_image'])) {
            throw new Exception("Invalid input: artist Id, media Id, or image Type is missing or invalid.");
        }

        // Begin transaction
        $this->conn->begin_transaction();

          // Check if the artist exists
          $artistCheckQuery = "SELECT `id` FROM `artists` WHERE `id` = ?";
          $artistCheckStmt = $this->conn->prepare($artistCheckQuery);
  
          if (!$artistCheckStmt) {
              throw new Exception("Failed to prepare artist check statement: " . $this->conn->error);
          }
  
          $artistCheckStmt->bind_param("s", $artistId);
          $artistCheckStmt->execute();
          $artistCheckStmt->store_result();
  
          if ($artistCheckStmt->num_rows === 0) {
              throw new Exception("Artist with ID '$artistId' does not exist.");
          }


          // check if creator is the one who uploaded the media
        $mediaCheckQuery = "SELECT `upload_id` FROM `Uploads` WHERE `upload_id` = ? and `user_id` = ?";
        $mediaCheckStmt = $this->conn->prepare($mediaCheckQuery);

        if (!$artistCheckStmt) {
            throw new Exception("Failed to prepare artist check statement: " . $this->conn->error);
        }

        $mediaCheckStmt->bind_param("ii", $mediaId, $creator_id);
        $mediaCheckStmt->execute();
        $mediaCheckStmt->store_result();

        if ($mediaCheckStmt->num_rows === 0) {
            throw new Exception("This User is not the owner of this media");
        }

        // Determine the column to update based on image type
        $columnToUpdate = $imageType === 'profile_image' ? 'profile_image_id' : 'cover_image_id';

        // Update the artist record
        $updateQuery = "UPDATE `artists` SET `$columnToUpdate` = ?, `lastupdate` = NOW() WHERE `id` = ?";
        $updateStmt = $this->conn->prepare($updateQuery);

        if (!$updateStmt) {
            throw new Exception("Failed to prepare update statement: " . $this->conn->error);
        }

        $updateStmt->bind_param("ss", $mediaId, $artistId);

        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update artist image: " . $updateStmt->error);
        }

          // Update the uploads table to set the status to 'completed'
          $updateUploadsQuery = "UPDATE `Uploads` SET `upload_status` = 'completed', `is_active`=1 WHERE `upload_id` = ?";
          $updateUploadsStmt = $this->conn->prepare($updateUploadsQuery);
  
          if (!$updateUploadsStmt) {
              throw new Exception("Failed to prepare uploads update statement: " . $this->conn->error);
          }
  
          $updateUploadsStmt->bind_param("i", $mediaId);
  
          if (!$updateUploadsStmt->execute()) {
              throw new Exception("Failed to update uploads status: " . $updateUploadsStmt->error);
          }


        // Commit the transaction
        $this->conn->commit();

        // Close the statement
        $updateStmt->close();

        return [
            'success' => true,
            'message' => ucfirst($imageType) . " image updated successfully."
        ];
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $this->conn->rollback();
        error_log("Error updating artist profile image: " . $e->getMessage());

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}


public function createNewArtistProfile($artistDetails) {
    try {
        // Begin a transaction
        $this->conn->begin_transaction();

        // Check if the artist with the provided reference_id already exists
        $artistCheckQuery = "SELECT `id` FROM `artists` WHERE `id` = ? or (`name` = ? and `email` = ?)";
        $artistCheckStmt = $this->conn->prepare($artistCheckQuery);

        if (!$artistCheckStmt) {
            throw new Exception("Failed to prepare track check statement: " . $this->conn->error);
        }

        $artistCheckStmt->bind_param("sss", $artistDetails['artistID'],$artistDetails['artistName'],$artistDetails['artistEmail']);
        $artistCheckStmt->execute();
        $artistCheckStmt->store_result();

        if ($artistCheckStmt->num_rows > 0) {
            $message = "An artist already exists with the same reference, name and email.";
            return [
                'success' => false,
                'message' => $message,
                'artist_id' => $artistDetails['artistID']
            ];

        } else {
            // Track does not exist, insert a new record
            $insertQuery = "
                INSERT INTO `artists` 
                    (`id`, `name`, `email`, `phone`, `genre`, `facebookurl`, `twitterurl`, `instagramurl`,  `youtubeurl`, `meta_data`, `RecordLable`, `isIndependent`, `datecreated`) 
                VALUES 
                    (?,?,?,?,?,?,?,?,?,?,?,?,NOW())";

            $artistDetailsJson = json_encode($artistDetails);
            $insertStmt = $this->conn->prepare($insertQuery);

            if (!$insertStmt) {
                throw new Exception("Failed to prepare insert statement: " . $this->conn->error);
            }

            $insertStmt->bind_param(
                "ssssisssssss",
                $artistDetails['artistID'],
                $artistDetails['artistName'],
                $artistDetails['artistEmail'],
                $artistDetails['artistPhoneNumber'],
                $artistDetails['artistGenre'],
                $artistDetails['socialLinks']['facebook'],
                $artistDetails['socialLinks']['twitter'],
                $artistDetails['socialLinks']['instagram'],
                $artistDetails['socialLinks']['youtube'],
                $artistDetailsJson,
                $artistDetails['artistlabelName'],
                $artistDetails['artistIsIndependent'],

            );

            if (!$insertStmt->execute()) {
                throw new Exception("Failed to insert track: " . $insertStmt->error);
            }

            $message = "Artist Created successfully.";


            // Link artist profile to creator
          // Update the uploads table to set the status to 'completed'
          $artist_linking_query = "INSERT INTO `Creator_Artist`(`creator_id`, `artist_id`, `access_type`, `date_created`) VALUES  (? , ?  ,'owner' , NOW())";
          $linkArtistCreator = $this->conn->prepare($artist_linking_query);
  
          if (!$linkArtistCreator) {
              throw new Exception("Failed to prepare uploads update statement: " . $this->conn->error);
          }
  
          $linkArtistCreator->bind_param("is", $artistDetails['current_userId'],$artistDetails['artistID']);
  
          if (!$linkArtistCreator->execute()) {
              throw new Exception("Failed to link new artist to creator: " . $linkArtistCreator->error);
          }

        }

        
        // Commit the transaction
        $this->conn->commit();

        return [
            'success' => true,
            'message' => $message,
            'artist_id' => $artistDetails['artistID']
        ];

    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $this->conn->rollback();
        error_log("Database error in insert artist: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

public function insertOrUpdateTrack($trackDetails) {
    try {
        // Begin a transaction
        $this->conn->begin_transaction();

        // Check if the track with the provided reference_id already exists
        $trackCheckQuery = "SELECT `id` FROM `songs` WHERE `reference` = ?";
        $trackCheckStmt = $this->conn->prepare($trackCheckQuery);

        if (!$trackCheckStmt) {
            throw new Exception("Failed to prepare track check statement: " . $this->conn->error);
        }

        $trackCheckStmt->bind_param("s", $trackDetails['reference_id']);
        $trackCheckStmt->execute();
        $trackCheckStmt->store_result();

        if ($trackCheckStmt->num_rows > 0) {
            // Track exists, update the existing record
            $updateQuery = "
                UPDATE `songs`
                SET 
                    `title` = ?, 
                    `artist` = ?, 
                    `album` = ?, 
                    `genre` = ?, 
                    `upload_id` = ?, 
                    `duration` = ?, 
                    `tag` = ?, 
                    `meta_data` = ?, 
                    `releaseDate` = ?, 
                    `updated_at` = NOW()
                WHERE `reference` = ?";

            $updateStmt = $this->conn->prepare($updateQuery);

            if (!$updateStmt) {
                throw new Exception("Failed to prepare update statement: " . $this->conn->error);
            }

            $updateStmt->bind_param(
                "ssssssssss",
                $trackDetails['title'],
                $trackDetails['artist'],
                $trackDetails['album'],
                $trackDetails['genre'],
                $trackDetails['upload_id'],
                $trackDetails['duration'],
                $trackDetails['tag'],
                $trackDetails['metadata'],
                $trackDetails['releasedate'],
                $trackDetails['reference_id']
            );

            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update track: " . $updateStmt->error);
            }

            $message = "Track updated successfully.";
        } else {
            // Track does not exist, insert a new record
            $insertQuery = "
                INSERT INTO `songs` 
                    (`reference`, `title`, `artist`, `album`, `genre`, `upload_id`, `duration`, `tag`, `meta_data`, `releaseDate`, `dateAdded`) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $insertStmt = $this->conn->prepare($insertQuery);

            if (!$insertStmt) {
                throw new Exception("Failed to prepare insert statement: " . $this->conn->error);
            }

            $insertStmt->bind_param(
                "sssssissss",
                $trackDetails['reference_id'],
                $trackDetails['title'],
                $trackDetails['artist'],
                $trackDetails['album'],
                $trackDetails['genre'],
                $trackDetails['upload_id'],
                $trackDetails['duration'],
                $trackDetails['tag'],
                $trackDetails['metadata'],
                $trackDetails['releasedate']
            );

            if (!$insertStmt->execute()) {
                throw new Exception("Failed to insert track: " . $insertStmt->error);
            }

            $message = "Track inserted successfully.";
        }


        // Update the uploads table to set the status to 'completed'
        $updateUploadsQuery = "UPDATE `Uploads` SET `upload_status` = 'completed', `is_active`=1 WHERE `upload_id` = ?";
        $updateUploadsStmt = $this->conn->prepare($updateUploadsQuery);

        if (!$updateUploadsStmt) {
            throw new Exception("Failed to prepare uploads update statement: " . $this->conn->error);
        }

        $updateUploadsStmt->bind_param("i", $trackDetails['upload_id']);

        if (!$updateUploadsStmt->execute()) {
            throw new Exception("Failed to update uploads status: " . $updateUploadsStmt->error);
        }


        // Commit the transaction
        $this->conn->commit();

        return [
            'success' => true,
            'message' => $message,
            'reference_id' => $trackDetails['reference_id']
        ];

    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $this->conn->rollback();
        error_log("Database error in insert or update track: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}


public function saveMediaUpload($uploadDetails){
    try {
        // Begin a transaction
        $this->conn->begin_transaction();

        // Insert a new record
        $insertQuery = "
            INSERT INTO Uploads 
                (`user_id`, `upload_type`, `file_path`, `file_name`, `file_size`, `file_format`, `metadata`, `file_hash`, `uploaded_at`, `is_active`, `upload_status`) 
            VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'pending')";
        $insertStmt = $this->conn->prepare($insertQuery);


        $insertStmt->bind_param(
            "ssssisssi",
            $uploadDetails['user_id'],
            $uploadDetails['upload_type'],
            $uploadDetails['file_path'],
            $uploadDetails['file_name'],
            $uploadDetails['file_size'],
            $uploadDetails['file_format'],
            $uploadDetails['metadata'],
            $uploadDetails['file_hash'],
            $uploadDetails['is_active']
        );

        if (!$insertStmt->execute()) {
            throw new Exception("Failed to execute upload insert query: " . $insertStmt->error);

        }

        $lastInsertId = $this->conn->insert_id;

        $this->conn->commit();

        return [
            'success' => true,
            'message' => 'Upload added successfully',
            'upload_id' => $lastInsertId
        ];
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $this->conn->rollback();
        error_log("Database error in upload insert: " . $e->getMessage());
        return [
            'success' => false,
            'message' =>  $e->getMessage()
        ];
    }
}



public function createNewRelease($releaseDetails){
    try {
        // Begin a transaction
        $this->conn->begin_transaction();

        // Insert into albums table
        $albumQuery = "
            INSERT INTO albums 
                (`id`, `title`, `artist`, `genre`, `exclusive`, `tag`, `tags`, `description`, `datecreated`, `releaseDate`, `AES_code`) 
            VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";  // Available set to 0 by default
        
        $albumStmt = $this->conn->prepare($albumQuery);
        
        if (!$albumStmt) {
            throw new Exception("Failed to prepare album statement: " . $this->conn->error);
        }


        $albumStmt->bind_param(
            "ssssisssss",
            $releaseDetails['releaseID'],
            $releaseDetails['release_title'],
            $releaseDetails['artist'],
            $releaseDetails['genre'],
            $releaseDetails['exclusive'],
            $releaseDetails['releaseType'],
            $releaseDetails['tags'],
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
                al.artist as artist_id,
                al.genre as genre_id,
                a.name AS artist,
                al.tag as releasetype,
                al.tags as attached_tags,
                al.artworkPath AS imageUrl,
                g.name AS genre_name,
                al.exclusive,
                al.available,
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
                s.title,
                s.duration,
                u.file_path AS trackFilePath
            FROM 
                songs AS s
            INNER JOIN 
                Uploads AS u ON s.upload_id = u.upload_id
            WHERE 
                s.album = ?";
        
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
            'artist' => $album['artist'] ?? 'Unknown',
            'artist_id' => $album['artist_id'],
            'genre_id' => $album['genre_id'],
            'imageUrl' => $album['imageUrl'],
            'releasetype' => $album['releasetype'],
            'genre_name' => $album['genre_name'] ?? 'Unknown',
            'description' => $album['description'] ?? 'No description available.',
            'releaseDate' => $album['releaseDate'],
            'tags' => $album['attached_tags'] ?? null,
            'exclusive' => $album['exclusive'] ? true : false,
            'available' => $album['available'] ? true : false,
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
