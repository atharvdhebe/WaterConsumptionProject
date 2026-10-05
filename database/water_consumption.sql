CREATE DATABASE IF NOT EXISTS water_consumption;

USE water_consumption;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS consumption (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    family_members INT NOT NULL,
    bathrooms INT NOT NULL,
    bathing_freq INT NOT NULL,
    laundry_freq INT NOT NULL,
    dishwashing INT NOT NULL,
    garden VARCHAR(10) NOT NULL,
    vehicle_wash INT NOT NULL,
    consumption FLOAT NOT NULL,
    category VARCHAR(20),
    date_recorded DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS predictions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    predicted_consumption FLOAT,
    category VARCHAR(20),
    prediction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
