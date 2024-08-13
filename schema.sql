CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE columns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    column_id INT,
    title VARCHAR(255) NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (column_id) REFERENCES columns(id)
);