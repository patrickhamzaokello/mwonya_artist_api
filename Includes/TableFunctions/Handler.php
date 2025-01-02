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


    public function getCreatorArtistProfiles($user_id)
{
    try {
        // SQL query to fetch required artist data with genre details
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
            WHERE 
                a.creator_id = ?
            ORDER BY a.name asc";
        
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }

        // Bind the user_id parameter as an integer
        $stmt->bind_param("i", $user_id);
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
                duration
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
                'duration' => $track['duration']
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



    
   
}
