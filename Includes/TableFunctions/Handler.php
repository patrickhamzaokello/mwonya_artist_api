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
            'message' => 'Verification Token saved successfully.'
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
            'message' => 'PasswordReset Token saved successfully.'
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

   
}
