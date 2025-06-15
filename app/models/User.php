<?php

class User
{
    private $conn;
    private $table;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->table = 'users';
    }

    /**
     * Create a new user
     *
     * @param array $userData
     * @return bool|string Returns user ID on success, false on failure
     */
    public function create($userData)
    {
        // Basic sanitization (trim and strip tags)
        $sanitizedData = $this->sanitizeInputs($userData);

        // Validate sanitized data
        $errors = $this->validate($sanitizedData);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check if username already exists
        if ($this->getUserByUsername($sanitizedData['username'])) {
            return ['success' => false, 'errors' => ['username' => 'Username already exists']];
        }

        // Check if email already exists
        if ($this->getUserByEmail($sanitizedData['email'])) {
            return ['success' => false, 'errors' => ['email' => 'Email already exists']];
        }

        // Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

        // Prepare query
        $query = "INSERT INTO " . $this->table . " (username, email, password) 
                  VALUES (:username, :email, :password)";

        try {
            $statement = $this->conn->prepare($query);

            // Bind parameters
            $statement->bindParam(':username', $sanitizedData['username']);
            $statement->bindParam(':email', $sanitizedData['email']);
            $statement->bindParam(':password', $hashedPassword);

            // Execute the query
            $statement->execute();

            // Return the new user ID
            return ['success' => true, 'user_id' => $this->conn->lastInsertId()];
        } catch (PDOException $e) {
            // Log the error
            error_log('Error creating user: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['database' => 'Failed to create user account']];
        }
    }

    /**
     * Validate user data
     *
     * @param array $userData
     * @return array Returns array of errors or empty array if valid
     */
    private function validate($userData)
    {
        $errors = [];

        // Validate username
        if (empty($userData['username'])) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($userData['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        } elseif (strlen($userData['username']) > 50) {
            $errors['username'] = 'Username cannot exceed 50 characters';
        }

        // Validate email
        if (empty($userData['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        // Validate password
        if (empty($userData['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($userData['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        // Validate password confirmation
        if (isset($userData['confirm_password']) && $userData['password'] !== $userData['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        return $errors;
    }

    /**
     * Sanitize user inputs for database storage
     * (trim and strip_tags only - htmlspecialchars is for output only)
     *
     * @param array $data
     * @return array
     */
    private function sanitizeInputs($data)
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            // Skip password fields from sanitization
            if ($key === 'password' || $key === 'confirm_password') {
                $sanitized[$key] = $value;
                continue;
            }

            // Trim whitespace
            $value = trim($value);

            // Strip tags
            $value = strip_tags($value);

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return array|bool Returns user data or false if not found
     */
    public function getUserById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by username
     *
     * @param string $username
     * @return array|bool Returns user data or false if not found
     */
    public function getUserByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':username', $username);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return array|bool Returns user data or false if not found
     */
    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':email', $email);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Authenticate user
     *
     * @param string $emailOrUsername
     * @param string $password
     * @return array|bool Returns user data or false if authentication failed
     */
    public function authenticate($emailOrUsername, $password)
    {
        // Sanitize input - only trim and strip_tags for storage
        $emailOrUsername = trim(strip_tags($emailOrUsername));

        // Get user by username or email
        $user = $this->getUserByUsername($emailOrUsername);

        if (!$user) {
            $user = $this->getUserByEmail($emailOrUsername);
        }

        if (!$user) {
            return false;
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Remove password from returned data for security
            unset($user['password']);
            return $user;
        }

        return false;
    }

    /**
     * Update user information
     *
     * @param int $id
     * @param array $userData
     * @return bool Returns true on success, false on failure
     */
    public function update($id, $userData)
    {
        // Basic sanitization
        $sanitizedData = $this->sanitizeInputs($userData);

        // Validate the data if needed
        // Note: You could add validation here if required

        // Start building the query
        $query = "UPDATE " . $this->table . " SET ";
        $updateFields = [];
        $params = [':id' => $id];

        // Build update fields dynamically
        if (isset($sanitizedData['username'])) {
            $updateFields[] = "username = :username";
            $params[':username'] = $sanitizedData['username'];
        }

        if (isset($sanitizedData['email'])) {
            $updateFields[] = "email = :email";
            $params[':email'] = $sanitizedData['email'];
        }

        if (isset($userData['password'])) {
            $updateFields[] = "password = :password";
            $params[':password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }

        // Append update fields to query
        $query .= implode(', ', $updateFields);
        $query .= " WHERE id = :id";

        // Execute query
        try {
            $statement = $this->conn->prepare($query);
            return $statement->execute($params);
        } catch (PDOException $e) {
            error_log('Error updating user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool Returns true on success, false on failure
     */
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        try {
            $statement = $this->conn->prepare($query);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log('Error deleting user: ' . $e->getMessage());
            return false;
        }
    }
}
