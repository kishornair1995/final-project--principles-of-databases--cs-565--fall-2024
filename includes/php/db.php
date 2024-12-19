<?php
require_once 'config.php';

// Database connection
function connectDB() {
    global $host, $dbname, $user, $db_password;
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Insert account
function insertAccount($appName, $url, $comment, $firstName, $lastName, $username, $email, $account_password) {
    $pdo = connectDB();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO Accounts (app_name, url, comment, first_name, last_name, username, email, password, created_at) 
            VALUES (:appName, :url, :comment, :firstName, :lastName, :username, :email, AES_ENCRYPT(:password, 'secret_key'), NOW())
        ");
        $stmt->execute([
            ':appName' => $appName,
            ':url' => $url ?: null,
            ':comment' => $comment ?: null,
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':username' => $username,
            ':email' => $email,
            ':password' => $account_password
        ]);
        echo "Account inserted successfully!<br>";
    } catch (PDOException $e) {
        die("Insert failed: " . $e->getMessage());
    }
}

// Search accounts
function searchAccounts($search) {
    $pdo = connectDB();
    try {
        $stmt = $pdo->prepare("
            SELECT 
                id, 
                app_name, 
                url, 
                comment, 
                first_name, 
                last_name, 
                username, 
                email, 
                CAST(AES_DECRYPT(password, 'secret_key') AS CHAR) AS decrypted_password, 
                created_at 
            FROM Accounts 
            WHERE app_name LIKE :search OR username LIKE :search OR email LIKE :search
        ");
        $stmt->execute([':search' => "%$search%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            displayTable($results);
        } else {
            echo "<div>No records found.</div>";
        }
    } catch (PDOException $e) {
        die("Search failed: " . $e->getMessage());
    }
}

// Fetch all accounts
function fetchAllAccounts() {
    $pdo = connectDB();
    try {
        $stmt = $pdo->query("
            SELECT 
                id, 
                app_name, 
                url, 
                comment, 
                first_name, 
                last_name, 
                username, 
                email, 
                CAST(AES_DECRYPT(password, 'secret_key') AS CHAR) AS decrypted_password, 
                created_at 
            FROM Accounts
        ");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            displayTable($results);
        } else {
            echo "<div>No records found.</div>";
        }
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

// Helper function to display table
function displayTable($results) {
    echo "<table border='1' cellpadding='5' cellspacing='0' style='width: 100%; text-align: left;'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>App Name</th>
                    <th>URL</th>
                    <th>Comment</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>";
    
    foreach ($results as $row) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['app_name']) . "</td>
                <td>" . htmlspecialchars($row['url']) . "</td>
                <td>" . htmlspecialchars($row['comment'] ?? 'NULL') . "</td>
                <td>" . htmlspecialchars($row['first_name']) . "</td>
                <td>" . htmlspecialchars($row['last_name']) . "</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>" . htmlspecialchars($row['decrypted_password']) . "</td>
                <td>" . htmlspecialchars($row['created_at']) . "</td>
              </tr>";
    }

    echo "  </tbody>
          </table>";
}

// Delete account
function deleteAccount($attribute, $pattern) {
    $pdo = connectDB();
    try {
        $stmt = $pdo->prepare("DELETE FROM Accounts WHERE $attribute = :pattern");
        $stmt->execute([':pattern' => $pattern]);
        echo "Account deleted successfully!<br>";
    } catch (PDOException $e) {
        die("Delete failed: " . $e->getMessage());
    }
}

// Update account
function updateAccount($attributeToUpdate, $newValue, $queryAttribute, $pattern) {
    $pdo = connectDB();
    try {
    
        if ($attributeToUpdate === 'password') {
            $stmt = $pdo->prepare("
                UPDATE Accounts 
                SET $attributeToUpdate = AES_ENCRYPT(:newValue, 'secret_key') 
                WHERE $queryAttribute = :pattern
            ");
        } else {
            $stmt = $pdo->prepare("
                UPDATE Accounts 
                SET $attributeToUpdate = :newValue 
                WHERE $queryAttribute = :pattern
            ");
        }

        // Execute the query
        $stmt->execute([
            ':newValue' => $newValue,
            ':pattern' => $pattern
        ]);

        echo "Account updated successfully!<br>";
    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}

?>
