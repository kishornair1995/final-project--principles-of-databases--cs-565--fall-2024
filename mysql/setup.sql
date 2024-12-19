-- Create Database
CREATE DATABASE IF NOT EXISTS passwords;

-- Create User
CREATE USER IF NOT EXISTS 'passwords_user'@'localhost' IDENTIFIED BY 'k(D2Whiue9d8yD';
GRANT ALL PRIVILEGES ON passwords.* TO 'passwords_user'@'localhost';

USE passwords;

-- Accounts Table
CREATE TABLE Accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    app_name VARCHAR(255) NOT NULL,
    url VARCHAR(255),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE
);

-- Passwords Table
CREATE TABLE Passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT,
    user_id INT,
    password VARBINARY(255),
    FOREIGN KEY (account_id) REFERENCES Accounts(id),
    FOREIGN KEY (user_id) REFERENCES Users(id),
    UNIQUE KEY(account_id, user_id)
);

-- Populate with Example Data
INSERT INTO Users (first_name, last_name, username, email) VALUES
('John', 'Doe', 'jdoe', 'jdoe@example.com'),
('Jane', 'Smith', 'jsmith', 'jsmith@example.com');

INSERT INTO Accounts (app_name, url, comment) VALUES
('Facebook', 'https://facebook.com', 'Personal social media'),
('LinkedIn', 'https://linkedin.com', 'Professional network');

INSERT INTO Passwords (account_id, user_id, password) VALUES
(1, 1, AES_ENCRYPT('mypassword123', 'secret_key')),
(2, 2, AES_ENCRYPT('anotherpassword', 'secret_key'));
