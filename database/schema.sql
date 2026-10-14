CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','student') NOT NULL DEFAULT 'student'
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    student_no VARCHAR(9) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    course_id INT NOT NULL,
    year_level TINYINT NOT NULL,
    address VARCHAR(200) NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

INSERT INTO courses (code, name) VALUES
('BSIT', 'Bachelor of Science in Information Technology'),
('BSCS', 'Bachelor of Science in Computer Science'),
('BSED', 'Bachelor of Secondary Education'),
('BSBA', 'Bachelor of Science in Business Administration');

-- passwords are hashed (admin123 and user123)
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$12$LRo2Esfd9MzrsBbkPRMWzesgLZyHBN/bBD/vDySIdbsBUPbIkKUze', 'admin'),
('juan',  '$2y$12$6UccfWGz17TXyVNkECTLseIwc9Imt0NFEHfQs1OQMZ3zbkOI2ocKG', 'student'),
('maria', '$2y$12$6UccfWGz17TXyVNkECTLseIwc9Imt0NFEHfQs1OQMZ3zbkOI2ocKG', 'student');

INSERT INTO students (user_id, student_no, full_name, email, course_id, year_level, address) VALUES
(2, '2026-0001', 'Juan Dela Cruz', 'juan@example.com', 1, 3, 'Tuguegarao City, Cagayan'),
(3, '2026-0002', 'Maria Santos', 'maria@example.com', 2, 2, 'Enrile, Cagayan');
