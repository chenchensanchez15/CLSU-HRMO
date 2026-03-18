-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2026 at 01:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hrmo`
--

-- --------------------------------------------------------

--
-- Table structure for table `job_vacancies`
--

DROP TABLE IF EXISTS `job_vacancies`;
CREATE TABLE `job_vacancies` (
  `id_vacancy` int(11) NOT NULL,
  `publication_id` int(11) NOT NULL,
  `plantilla_item_id` int(11) NOT NULL,
  `date_posted` date NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_vacancies`
--

INSERT INTO `job_vacancies` (`id_vacancy`, `publication_id`, `plantilla_item_id`, `date_posted`, `created_at`) VALUES
(1, 7, 1083, '2026-02-11', '2026-02-14 22:20:37'),
(2, 7, 1084, '2026-02-13', '2026-02-14 22:20:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `job_vacancies`
--
ALTER TABLE `job_vacancies`
  ADD PRIMARY KEY (`id_vacancy`),
  ADD UNIQUE KEY `unique_vacancy` (`publication_id`,`plantilla_item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `job_vacancies`
--
ALTER TABLE `job_vacancies`
  MODIFY `id_vacancy` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- CREATE CONNECTION TO HRMIS-TEMPLATE DATABASE
-- --------------------------------------------------------

-- Create a view that connects to HRMIS-template database
-- This assumes HRMIS-template database exists on the same server

CREATE OR REPLACE VIEW `vw_plantilla_items` AS
SELECT 
    pi.id AS plantilla_item_id,
    pi.item_number AS plantilla_item_no,
    pi.position_title,
    pi.department_id,
    pi.office_id,
    pi.salary_grade,
    pi.monthly_salary,
    pi.status,
    d.department_name,
    o.office_name
FROM hrmis_template.plantilla_items pi
LEFT JOIN hrmis_template.departments d ON pi.department_id = d.id
LEFT JOIN hrmis_template.offices o ON pi.office_id = o.id;

-- Create a function to get plantilla item details
DELIMITER //
CREATE FUNCTION GetPlantillaItemDetails(plantilla_id INT)
RETURNS TEXT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE item_details TEXT DEFAULT '';
    
    SELECT CONCAT(
        'Item No: ', pi.item_number, 
        ' | Position: ', pi.position_title,
        ' | Department: ', COALESCE(d.department_name, 'N/A'),
        ' | Office: ', COALESCE(o.office_name, 'N/A'),
        ' | Salary Grade: ', pi.salary_grade,
        ' | Monthly Salary: ', pi.monthly_salary
    )
    INTO item_details
    FROM hrmis_template.plantilla_items pi
    LEFT JOIN hrmis_template.departments d ON pi.department_id = d.id
    LEFT JOIN hrmis_template.offices o ON pi.office_id = o.id
    WHERE pi.id = plantilla_id;
    
    RETURN COALESCE(item_details, 'Plantilla item not found');
END//
DELIMITER ;