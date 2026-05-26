CREATE DATABASE IF NOT EXISTS student_system;

USE student_system;
 
DROP TABLE IF EXISTS grades;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    student_id VARCHAR(50) NOT NULL
);

CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    units INT NOT NULL
);

CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    prelim INT NOT NULL,
    midterm INT NOT NULL,
    final_exam INT NOT NULL,
    final_grade INT NOT NULL,
    UNIQUE KEY unique_subject_grade (subject_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

INSERT INTO users (username, password, fullname, student_id)
VALUES
('admin', 'admin123', 'Hiro Hamada', 'CIT-3000');

INSERT INTO subjects (subject_code, subject_name, units)
VALUES
('IT101', 'Introduction to Programming', 3),
('IT102', 'Web Development', 3),
('MATH101', 'Calculus I', 4),
('ENG101', 'Technical Writing', 3),
('PHY101', 'Physics I', 4);

INSERT INTO grades (subject_id, prelim, midterm, final_exam, final_grade)
VALUES
(1, 88, 90, 92, 90),
(2, 85, 87, 89, 87),
(3, 90, 91, 93, 91),
(4, 86, 88, 90, 88),
(5, 92, 94, 95, 94);