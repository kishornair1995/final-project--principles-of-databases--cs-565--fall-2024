<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwords Management App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Passwords Management App</h1>
    </header>

<?php
// Include necessary PHP files
require_once "includes/php/config.php";
require_once "includes/php/db.php";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $option = $_POST["submitted"] ?? null;

    switch ($option) {
        case 'SEARCH':
            $search = $_POST["search"] ?? '';
            if ($search !== '') {
                searchAccounts($search);
            } else {
                echo '<div id="error">Search field is empty. Please try again.</div>';
            }
            break;

        case 'INSERT_ACCOUNT':
            $appName = $_POST["app_name"] ?? '';
            $url = $_POST["url"] ?? '';
            $comment = $_POST["comment"] ?? '';
            $firstName = $_POST["first_name"] ?? '';
            $lastName = $_POST["last_name"] ?? '';
            $username = $_POST["username"] ?? '';
            $email = $_POST["email"] ?? '';
            $password = $_POST["password"] ?? '';

            if ($appName && $firstName && $lastName && $username && $email && $password) {
                insertAccount($appName, $url, $comment, $firstName, $lastName, $username, $email, $password);
                echo "<div>Account inserted successfully!</div>";
            } else {
                echo '<div id="error">Please fill in all required fields.</div>';
            }
            break;

        case 'DELETE_ACCOUNT':
            $attribute = $_POST["current-attribute"] ?? '';
            $pattern = $_POST["pattern"] ?? '';
            if ($attribute && $pattern) {
                deleteAccount($attribute, $pattern);
                echo "<div>Account deleted successfully!</div>";
            } else {
                echo '<div id="error">Please provide valid details to delete.</div>';
            }
            break;

        case 'UPDATE_ACCOUNT':
            $attribute = $_POST["current-attribute-name"] ?? '';
            $newValue = $_POST["new-attribute"] ?? '';
            $queryAttribute = $_POST["query-attribute"] ?? '';
            $pattern = $_POST["pattern"] ?? '';
            if ($attribute && $newValue && $queryAttribute && $pattern) {
                updateAccount($attribute, $newValue, $queryAttribute, $pattern);
                echo "<div>Account updated successfully!</div>";
            } else {
                echo '<div id="error">Update form is incomplete.</div>';
            }
            break;

        default:
            echo '<div id="error">Invalid action submitted.</div>';
            break;
    }
}

// Include HTML Forms
require_once "includes/html/search-form.html";
require_once "includes/html/insert-form.html";
require_once "includes/html/update-form.html";
require_once "includes/html/delete-form.html";
?>

</body>
</html>
