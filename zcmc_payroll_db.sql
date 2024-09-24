-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2024 at 10:28 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zcmc_payroll_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deduction_group_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `amount` double NOT NULL,
  `percentage` double DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `emmployment_type` varchar(255) NOT NULL,
  `is_mandatory` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `deductions`
--

INSERT INTO `deductions` (`id`, `deduction_group_id`, `name`, `code`, `amount`, `percentage`, `date_from`, `date_to`, `emmployment_type`, `is_mandatory`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Gsis', 'gsis', 1000, NULL, NULL, NULL, '', 0, 1, NULL, NULL),
(3, 2, 'Pag-Ibig Premium', 'piPrem', 2000, NULL, NULL, NULL, '', 0, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deduction_groups`
--

CREATE TABLE `deduction_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `deduction_groups`
--

INSERT INTO `deduction_groups` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'gsis', 'gsisCode', NULL, NULL),
(2, 'Pag-Ibig', 'pagibigCode', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deduction_logs`
--

CREATE TABLE `deduction_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deduction_id` bigint(20) UNSIGNED NOT NULL,
  `action_by` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_computed_salaries`
--

CREATE TABLE `employee_computed_salaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `time_record_id` bigint(20) UNSIGNED NOT NULL,
  `computed_salary` text NOT NULL COMMENT 'Without night differential computation and deductions',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions`
--

CREATE TABLE `employee_deductions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_list_id` bigint(20) UNSIGNED NOT NULL,
  `deduction_id` bigint(20) UNSIGNED NOT NULL,
  `amount` double DEFAULT NULL,
  `percentage` double DEFAULT NULL,
  `frequency` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `date_from` varchar(255) DEFAULT NULL,
  `date_to` varchar(255) DEFAULT NULL,
  `stopped_at` varchar(255) DEFAULT NULL,
  `completed_at` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `with_terms` tinyint(1) NOT NULL,
  `total_term` int(11) DEFAULT NULL,
  `total_paid` int(11) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deduction_logs`
--

CREATE TABLE `employee_deduction_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_deduction_id` bigint(20) UNSIGNED NOT NULL,
  `action_by` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deduction_trails`
--

CREATE TABLE `employee_deduction_trails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_deduction_id` bigint(20) UNSIGNED NOT NULL,
  `total_term` int(11) NOT NULL,
  `total_term_paid` int(11) NOT NULL,
  `amount_paid` double NOT NULL,
  `date_paid` date NOT NULL,
  `balance` double NOT NULL,
  `is_last_payment` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_lists`
--

CREATE TABLE `employee_lists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_profile_id` bigint(20) UNSIGNED NOT NULL,
  `employee_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `ext_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) NOT NULL,
  `assigned_area` text DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `is_newly_hired` tinyint(1) NOT NULL,
  `is_excluded` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_receivables`
--

CREATE TABLE `employee_receivables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_list_id` bigint(20) UNSIGNED NOT NULL,
  `receivable_id` bigint(20) UNSIGNED NOT NULL,
  `amount` double DEFAULT NULL,
  `percentage` double DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `date_from` varchar(255) DEFAULT NULL,
  `date_to` varchar(255) DEFAULT NULL,
  `stopped_at` varchar(255) DEFAULT NULL,
  `completed_at` varchar(255) DEFAULT NULL,
  `total_paid` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_receivable_logs`
--

CREATE TABLE `employee_receivable_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_receivable_id` bigint(20) UNSIGNED NOT NULL,
  `action_by` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_salaries`
--

CREATE TABLE `employee_salaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_list_id` bigint(20) UNSIGNED NOT NULL,
  `employment_type` varchar(255) NOT NULL,
  `basic_salary` text NOT NULL,
  `salary_grade` int(11) NOT NULL,
  `salary_step` int(11) NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_taxes`
--

CREATE TABLE `employee_taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_list_id` bigint(20) UNSIGNED NOT NULL,
  `with_holding_tax` text NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employment_types`
--

CREATE TABLE `employment_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `excluded_employees`
--

CREATE TABLE `excluded_employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_list_id` bigint(20) UNSIGNED NOT NULL,
  `payroll_headers_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `month` varchar(255) NOT NULL,
  `is_removed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'true if it is removed from list. for the genpayrol month and year',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_payrolls`
--

CREATE TABLE `general_payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_headers_id` bigint(20) UNSIGNED NOT NULL,
  `employee_list_id` bigint(20) UNSIGNED NOT NULL,
  `time_records` text NOT NULL,
  `employee_receivables` text NOT NULL,
  `employee_deductions` text NOT NULL,
  `employee_taxes` text NOT NULL,
  `net_pay` text NOT NULL,
  `gross_pay` text NOT NULL,
  `net_salary_first_half` text NOT NULL,
  `net_salary_second_half` text NOT NULL,
  `net_total_salary` text NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_payroll_trails`
--

CREATE TABLE `general_payroll_trails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `general_payrolls_id` bigint(20) UNSIGNED NOT NULL,
  `payroll_headers_id` bigint(20) UNSIGNED NOT NULL,
  `employee_list_id` bigint(20) UNSIGNED NOT NULL,
  `time_records` text NOT NULL,
  `employee_receivables` text NOT NULL,
  `employee_deductions` text NOT NULL,
  `employee_taxes` text NOT NULL,
  `net_pay` text NOT NULL,
  `gross_pay` text NOT NULL,
  `net_salary_first_half` text NOT NULL,
  `net_salary_second_half` text NOT NULL,
  `net_total_salary` text NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `imports`
--

CREATE TABLE `imports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deduction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receivables_id` bigint(20) UNSIGNED DEFAULT NULL,
  `module` varchar(255) NOT NULL,
  `module_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `employment_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_trails`
--

CREATE TABLE `login_trails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `action_by` bigint(20) UNSIGNED NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `methods` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_08_19_000000_create_failed_jobs_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2024_07_17_081408_create_login_trails_table', 1),
(4, '2024_07_17_081624_create_transaction_logs_table', 1),
(5, '2024_07_17_081711_create_employee_lists_table', 1),
(6, '2024_07_17_081734_create_time_records_table', 1),
(7, '2024_07_17_081755_create_employee_salaries_table', 1),
(8, '2024_07_17_081815_create_employee_computed_salaries_table', 1),
(9, '2024_07_17_081911_create_deduction_groups_table', 1),
(10, '2024_07_17_082017_create_deductions_table', 1),
(11, '2024_07_17_082040_create_deduction_logs_table', 1),
(12, '2024_07_17_082121_create_employee_deductions_table', 1),
(13, '2024_07_17_082137_create_employee_deduction_logs_table', 1),
(14, '2024_07_17_082140_create_employee_deduction_trails_table', 1),
(15, '2024_07_17_082241_create_receivables_table', 1),
(16, '2024_07_17_082259_create_receivable_logs_table', 1),
(17, '2024_07_17_082260_create_imports_table', 1),
(18, '2024_07_17_082349_create_employee_receivables_table', 1),
(19, '2024_07_17_082433_create_employee_receivable_logs_table', 1),
(20, '2024_07_17_082600_create_employee_taxes_table', 1),
(21, '2024_07_18_070517_create_payroll_headers_table', 1),
(22, '2024_07_19_070518_create_excluded_employees_table', 1),
(23, '2024_08_07_081348_create_stoppage_logs_table', 1),
(24, '2024_08_15_095353_create_general_payrolls_table', 1),
(25, '2024_08_15_095513_create_general_payroll_trails_table', 1),
(26, '2024_08_21_095941_create_employment_types_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payroll_headers`
--

CREATE TABLE `payroll_headers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `employment_type` varchar(255) NOT NULL,
  `fromPeriod` varchar(255) DEFAULT NULL,
  `toPeriod` varchar(255) DEFAULT NULL,
  `days_of_duty` varchar(255) DEFAULT NULL,
  `created_by` text NOT NULL COMMENT 'Saved Logged Employee data - Json Format',
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `employee_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, '2022090252', 'Dennis N. Falcasantos  ', '50b3895b3102b9534197f5aeb1b8b1cdc69edeabd094ca230adef5c44db32b06', 'eyJpdiI6IksxbmszODhObzNwc0w4ZGdFYmJUY3c9PSIsInZhbHVlIjoiUk1TY1owcjAxSzJDM2Y4Z2x1S2lFZElPdi9KRTgxc1pqN1dxSW9PY3NGK3Jqc3dsamZ0Z0g4bms4blRmZSt0Q0JlUE5zK0JIcEFWdE1RR2ZNK1lESDdtTFMyeCtodUR1TU02ZkUwUVdkQ2o5cHZ0aTh0Q2NWZzViZ3BrMDJNRjZUbGpCM1FTa2dQOWgwNWU3MUlMREM0aW1DbUVkaE92bElCc3VXbXpwTkthNWdHR2MzeVhzRExBVXJXNEl5KzJoYmpPaldmUERKVzZGK1BLY3pSbFAxTFRrQWRqcTgwQ1NZK3ZtOXY4czgraWxVYzhBMnFZQmZBdHMyb3BzcEpXdFYzK0c2c3ZXWTFGR004US9hRE9ycGlJOW5pckZDd04zaVFvOUlIcTdoNlYzbU9GamFLcnd3L2hFYjc2VmwyeHZncUFoWGJWWlVBM1pxazF0dlEzUk9oQmVGc2ZOZnRWekpQSU1pbzZ0c0VpbTI1Q25rVGxtdk9UYmJ5ZEE5Rkljc28wYmsyS21Sb3BHUTNlZTZCMmUvYWlPMHRBaDlsSkhWVXZ5eFU2bnN2cE5tT1NZTDVnd21icjFWQjhaM0NCOEp3Y0REVVVLQ3JLZmJUcHVYNXBNcW1TKzR4cXZCZ3dYb0tGRGVqaDhBYUhDUmVtdFNPWjhEYWNCd1dkc3Z6RUJ3Z25zMkhSR21WcG1kRXlKTlZaSTRZR01walhyQ3dmWkE2Q1ZyNmF6Y1NuZzF1WkdpbVFVNTFGRWYvZXNWLzE4L0pOc3Zhck9rV0dEdGhXQzlja3RENzhxQUx5bFNhQjUzcGtzZUltQVc3bnJidVhEMW5hTVFRZEcybW9vSTNzNmQ0R2t5TkswYTE1Yys0Rk55NVNrcWsrbG5qR2prNm1mbEk4UmlaSjNzbDh4bTFYOXNsUENEejdCZFJsSmx2ZXpsNUhQMWNrQUlHU0xOcWhYN0R6WHBBNS80cWdIV28vbi9RYkduc3RFUHEzK1h0MUpybFJFdEtzNkU1cmcxTUc2OVY3d3NYYlA3a0VDUEdOc3BacWNnWUpYRmkrdk9sNWY2OFQxOHo2aVhFQjZJRThTVjFRQlVuRVBRSG1xR0oybVR5UGttWTg3WXRJY2hPNVB6SVdxeTIvYkl0LzFMZWZuc016TnlIb3NMazRFLzgwYjRZbnNxSW5TeGsveVpzU0habnBodGpFSVk5TXFiMklka3R6dVRJeEEySGp2Z3hnTGxVcUh0ZEhreTB3UVFhRFBNNEJtbnk0eWk1NHhZa2RCRDVZTGFTN2xxV0R0K2tXQ21rOHFLM1c3WVlaQlFVRFBzV2hadXZmcUl2S3J6V0p2N2FicnJWbm5Ic1J0aGlxMjhYWDBCNDFLTTRyeUxzRGNJM3h1bmNiS20wd3c3bkQ2WDJ3ZkhxaU5SRWtWN3dLdUJTNEhwU2ZzQ3RBMTZxaFlMRmFwN2ZyNU95ZWZ1NmxZNWtPT3d0U0RMdDBUcWF5WFdHQlVaaEN6bkdDQm9ZdGxmcm5pT0dhd3pMSXJ2c1hPVHdaL0F3dnlXeGRWNWNScTVMTmRYM09tL1hSR0lWWUsweUtCb2xuTXpXTXJtK1U4SkluajUzNFdRSS92TUxoaWVLbjZGLy96ci9NNWZFY01yRE5uV004QW53QjRjVTc0WTNITVZ4Q01pYk5vaURMU2NmTWxMMW1KdUNlK3VYZTJKbWRvd0dkaHFFeXlEUHd2ZjE3VVhLZVc0bndrMUN4VzY4VENpTndUNGh3Ylp1aDNzeDZaSVVSZmFTOEk5VWFOaDg1TDlIVlh6b1pHK3NvMU96dFZIZmJkcU9ZQXlIODFOTGFkRTlNN1ZpOW1FR3Z0dUR2ZVJKclU2YTlwbTJXMUNmeXdZR3ZWR1ZuMDBBdHFlUnNjS1F1dDgrSE5mK3ZMZm1ONkhwbXRNbU9zMno2R0VWVEQvT2J5U2RPY1lFTzF3Nmh1NkhzL0IvbUxqMFlzaWpxRlJjZGVZL0lwbXg0eGZZS1d0Sk80Y05Ma0hEeVNUemVlWTNPU1BQRm9WV29BM2NmTFdncjFiajB0d2Z1THRpc1Urc1ZqQ1ViQjhPZGlVKzdIUUswYXdTSDBxcitZZFRQTVpNYkVGV0JmTTcyVlNmZVJQQ1FMSWZiSlZvNmExa2kxNzlZckRkMWN4RFVyOGRaTVVsZTNzVW9RUFJDYUpXV0ZNbXYzTEFuazl4cEk3UTdkd3B5RzhWQnBMZ0hFdEZtRzBqdHNVUnk5WlVUVW1HeEVjREJadU95UG9SdFRhTEJKNzFhdHNPaFhYOG12T2NDbUllQjZITWRJeUoxNS9pZDM4L3dJdFVFWkNNb3V3dWlZWFhxQ0JmY3A2T2FMYi9ZM3VYSXlPY2RpeW1pMGE4VTdNVnNHcUZCVExyeWVqUXdIRnBqKzU4dkhsYjdsa2RVREUxWkpQSHVXVzBzRGlCdXhtVkt5aTJoUktPcy8rUzlyZ1QyRzkrUDBqRGNKKzNaVlZCM2hYN3FoaXlqd1NKRmthYWlyT3hkSFh0cEQ0ZThWeWI3QUdjQU81aERhWTlBM3NNaGlkdWtnSEh1R1RmUHgxdC82RzgwVWtsWFZhVk5oOUVnYjdYRDlFYURwWHdXNXduRUthKzJZS0l3bWM0SUN4ZVZFWHhwMmo2UlN2M05OZmtXWWxPc1ArcE5ISldZRXkxMGtvTFFCZFNFcnUrV0NDcDZkWFgycFVxTUFQRkFWc3FZTEdSZk94NlZvZ3AyclJRbllsaGZldVdhL1JoU3BzYVBQeVRxdmpzY0pYRmxlWW9vV2Z5TzVkamZIOXZjNTlOZThWQzRLRHFCdUVhK1hzQ0ZhYm5UVW9IcVpMeEdlTFZMNklvWFZvTWo5Vmw4NHV4Z21PNUJ5VEE4N29Mb28zRXJzUGQzTnAyZW9udGY4UkhGb3JGZ1VNdDVtNVM3RHpsZmd1R1dKTm9vWFZGL3RacjJXOHkxOHM3Y1NSYzkvR3dGK2JUVHF5TklNZ1RPZTA5bU5qOWt4Q0tQaklOZG1zRXpUMExrcXByUDVqOFVFQk15MldPK0RMUkpUWDNuT0FYbDdsVW9zRml2YXA1cFVnTGFvWnhRRDZQUTNXNTNjeDdiZjc5TllQTWNVRUFBYW1XV3h5Y0w1MFk3QWZtQnpSZ3pGU0p5NmVsUHhnTjRRQ3pkY0w1SkJzdEJ5OHFQQmo5SDl1ZTAraUxvUHllcXlpTHlRY0c1Sk9ZUWh1UjFmSm9EbStKNHNvc2ErKzViRDROMGEwdEpqWDU3SFJnR0xBUG91UnIvZ0tsNVcvcStySTZ6QjUzUUxUR2k0QzVlVHcvZVZoMExGbmh2K0xPN3czK2tnNlFMS1VlTTZ6YXZxbWpMdVJxV1h2dU9iUkpTbFAzamM5SWJlYlVkSVpXQ0JRMFlHdkQxUVdkdThvL2Q4dGc1eWFwTHFxU25yWWxNdmsxV0t0TW5pSEk3OHdQOHUvNlRsN0NMRmErbC8yMTVvUFFXZEE2ZUkwOE5GRkZKbUV0RlFsYWhJUkJaWURuTHZnanZEbHM3bS9tSllzQ1NPQThWRTFyNmlBYzMzVkpMMWZubDN3bFN3bFVGc3dvRWpMTUJ5N2luSXV3bzVzek40aFhIN2wyOVloTVRMUEFLWVM3cEV4N1p3Q05kd2RQUzBqbzI5c1h4ZG5aMkZyMkJFMHg4eFQ3SGlLYW96b1B0a2pzZnlTeWVyY2dJVnJtLzFZMTZYSS9naXc2MEZ6UVRCQnNnMURjZ1VueE44T1ZDcFBoK1hwQjEwUWs1NGJyWkcyaFlTdmM5VEVUYSs2eWhFNy8wYVRGdXVEeTV5eDJBQ2Vqd0k5YXVjS2tUUzM3RmdFckFjRVBGeDhhdDlveU8vMVdvaEljeFNudlptdm9zVGRTV0JrN0FiNHZOdmo5aTlyRGFMc29NRGx3b0RxQ05JckFMV05oVnUvMHZkTk9IYmthbEFVcFp4SERoZ1RpUU9XRDlVOXFnbmdLcWNra3NLVFB4enV2dWNkc2dqOGF6MlZ3OG85eS95R1h6TWd6dWR6dlFXUml2RXlIR3ExODhYM2N1VnZpRlBwOVM3SUhVeGlyU1FKak1oZ1loSkhsMlk2UnEzQnlOSmhVK1hLaWxVQ1M1Q2dXZE0vNHZBb25BT0JaYVpkV0oxWlp0WC9UNi93aDFzUy9oZlRLSnVzSU01L2VLY2RiUm4yZWlLK1BiYlYxbHkwQnhyMktua3B0OGVDUFJpQTVid3ZUNnplMlQ0cGk1MHZjcTVXMjUvbnYwd2ozWk1PcTBlRjllMkNDNWp5QTl5Y0Zpa0I2dzFYZys2MUo1RVVaT2ZNNTE5amc4Q0Z2MHlIMkZidDBQc1RZSzhIa1I5UUVrR2RkM2lHdVRDem9CUmFCQTZ4TFRPRVVONHBTNExYRGxybHNyUjAralZDS2JzaDJBcEdsYml5R3BaUmRuUy9kSnFBcUw2RUFhd2FlVG4yeC9SYWsxQ3puUnltb21UUGJ6MEdwYWZGaGVjSG5qcWVzYWh5RTNQM08yRmRORmE1MHFKaWRQUFJ5VktJQ3p3ek5FeHZMSldmMi9FeHROTzZBQmJRd0p5NnRzTkNLVjNpdExNSS9OWGJXUXI1SDA4a202eENZL0w3THhzK3pmZHN1WHJFNisvcVhacGhsYjBaclQ2dlh1ckJkamc3VEdhemxCdmFDT2ZCSWZpYnJldDJlUHRMc2JNWXlvMllteEs2VHhEVUtPNzZaRnd4YVJvSGMyUEpUeDUzYkFkbXJlaXM0OUpYRm1QMTQ1enhoS2NFYS9DbVRzbkU5WGd2N1drdnhXemY2blVGQ1BxcXE5elRSWDFGcWw2ZEJuMDlXY2F0NlpOVFhWSXV3MzVNdkRyZDZGMWo2eG1HZ1JOSmRWdzQ0MVNERk5ZR1pJSW5jQXZab1pOeHpqR2c1bGZaelNWaG9lMm1neW1NY0Q0R2dOdjUyWkVTeEpGSjh6c09TZUkyWXJGSlZyVkMrV1ZXZEhLeWpBQVo0b2JvNDFQZC84azlnL09YOFpyREhka2gxYmNmcXZ3cFh4cy9pS0J0U3RwMjNhcDRRdktVc0Q4T0h4cExWSjEwa1Yva0tEaVkycTRJSWdwOURpYWxocFUyYXZZWjJyY25KT0JjTzJTUE9VWkZCdTFqcXl0azlhL1VuSUJQZWt3WXY2NTVnTWI5ay94eGZhamJLdWxHaS9vMWF3U1JsZnpJSGJ5amR1cDgyYkFmcnRRTmt4Y25pU2xqaHAxOXpQazl6Qmh6UjdIMTd1VGRzWXB2R2gySVFNd1kvcHdFYjl5MStFT3ZCWkE4N0U3R1pzczBmTlFSN0NoWTZPWlFycXYwWVVsN1FnVnpwdDdnYkhiQ0JkOUNQcGhGZUhOeEd6ZWlQYUFJbXhGclhpeGZjdFpaUTUxSFZOR1pXZjVQdmx3dkw3dDN4SnZGa0pQanJEK01jeWkwd2VOVEY1bUZ3V0hZcFdMVmIxRGZlRHZCMCtON1IxaXBicm1JdFNsM1k4UWoxbFBiMmFyY2FBb1MvUExqem5UeUlkL2JTd0wwajVHSXdlb3hLZks0alhMbXJjWFgvdGFIcU54cDY5c0dERGcrbldMbDNxNUl3S2hKWk4rNmY2TFNRallSR2FyR3RlVEdrRytXS2VSOGlGZnBGMmhXWk0xK2pnUUROWkhzSU5XMVNEclRLeDZvRDZtbjhXMUpubWlGV0w3VGt5cTJmeVArdjdBYk1NcytjanM1dlBLU3o5TkNHa0oreFBSRGZrZW9sdjg2VFc5Tm1neHNSWGVYM3BSZnJhcHowZUNRd1l3bW5OMEZ5QTI4U1dNMkhOTUhHc0Rsd3lYSTVkU244SlBBWndlcEQwMHZEZ1RrdDZFS1F5SVNqMkZaZVBGcTM5QUFENC9PTVRxdnc5SDdyTTNEckVXNWx2bjlJUUtsa2tSanc1TWVkUWx2UFhNUVBSNVFJaVI3dkU5ZncxTjJQSzhCaWV3a2ZEMmd3YnNqay9ScG5tUlBuSzAwOWE3QndzcVZPWXpDUlUxZnR5dDdpMTBrUnBUWVkxUzRPL09aL01UdFViQ1BKV29VVXc3dWJaU0h5bmF4anlTWUpHY29GRGhIMDFOelFUMDBZV0ZvWldTTklkaEFlcitEdG1Xb3RBNjBWSXFyeUVsajJyd1FMcHBCaUlrUmVQS090Slk5Q25PYlhGTm83ZmdVbUdSVDZBODBlTE1DYUt6TTNOR05nTjQxQ0hXTzlvQ1BNbDA5U2ZSZTgxM1RJQ3AxV20zamI4WkNlMWxWTW50MFcvaTZVRk5mcm9YVGJpQzBFbThFQlpSVUV6QVZNQk5hUTZsTEhoSHIzVVc5SWtCcThRU3Joc0xLTEFrL0h3YVZkRXdMQkhRbVhBemoxSmZITXAvdzNIVm9NVm1ZSUlpTCtOakQrQTFhYW9uK0pXMWpGWmJKSTh0L1BOc1NPdWw1MG5uVjYyU1E1dGd4Sno1Y01yQTdRbVJyV1diM2duYktPYVk2N2puVkp1KzNFUzdhR2EwbHZ5bk84Z1FaME5NK0hxM1hOQ0ozVENvdmFFbFRoZ2ZuRkxlSUhGeG8xaXNjdGk0QlVldDl2RnUxSlpFQkdybWZZQm9DcDU5YUU2VXRkUVE3bk13Ung4VlR3Lyt0Z29GUzBRZXRaZHZjM1BkODRGNDdWSFBIY1dmM2JGYTZTeFNRQk12ZjA0eE83eW00aDZBeGVMR2VQZzE5cWhUbHYzcFg1ck1tSGNIVDNCL3RFQUpFMkNtRGxEZmNKNEVwa3oxRnN4OUtIeXBUZlp4WUpIVWxsZEQ5V3lpQmxkYm9aUU84YnBVdEg2Mk1UbTJlWDVnOG1yWlROeUlNSmFvNVNsQjllQU44RlVkTG43YXMzMWl4bHpDVHVuUlFIMi80N0p3WGk4SGpIcVdzRGYzZ2JwRWErWk5NeVUwMEVlSXo3RmttYXVWYmpSQ0o1RytERGMxOUh2TXpZb0lMOGpuWUVkSlQra2IvTFl4c2U1TExBZFRFWnZ6MXZpcnhNTHpzK0c3NllMVHh1WTdOWkNlWlV1SGNGS0ZtdFg5NnZhVm9GZ1BHTjZDVm1lOHdPNEM0emlNdFROTE00ZkhCOHJGUDlkdUFpcnRpeFhnT0tYWWpsbUx4SGR4QjRIZWFpVGNNT2NJV0tFV3I0SEtOQU8yVHdQOEV2Ti9OdHNWcCtvZEZzOWZYYWh1WjVjdW9kMEluYUpGMWNzaUJQYngxSkJLZnJNT3VRT3dvYjVySjNlaHBITTA2Qjl5c3gwZk94aUc3aHQ1THJJZElkTHhGU3BOM2JNeFhuTVlBNUJpWThJNUswSWtLQ2MvTWdLS2lmSFNjcUw3Sjd5eEdiM251UzNiWGJ2ZHpuVktGc1NmMTMxaW1OVGFzelhmRUhlSWk4SFlzNGJFRlhBMjNiZ1dRa084S0xzMFYwTjF0bk1JNlB6bW9VcU9YbG9BR2IyNVR6Q0V6T0dOVFBGbXp4T0F6enUxSitDQ2EwemRMR1diWDU0S3Uzb2RwaUV4U0xwMldoT3hpVUM3SUdZakgxcWRCRm5iTTRiTFZnZlo2cXlNQjJiZ1hndDcvcHcycXc4RW5JQ3hSTFhJemFRMEk0TTIxNnpxb3d2alRYYW5QNVBjQWpXbEs2YU9lWXNGN2g2eDhUSEtpUlVXUFZqc1EyMnhTbTlzNTcvbnlpOXg5ajUyYmc2cDBNbUUxK25hQzE4blduaVNOc0NTNGdzMHhnNG4zNGcwamd1SlJ6cHR1VUtJTUhUQVJMN3ZrVmFVbTIrUEljZnBYcUoreWQ5eTUzVDE2aG5wczJLTlBOVXA0dERVdHpXV0Y3MWtwSnkwMnRJZktkN0trdUlSQ1BzN2dyL0dNVHdLZVd5OWNXSVVuMFd5LzVnTG1mc3dDT0xMekZ0eEREbG10a0ZZUW91QUFNOGNuM3BCWDNoM3Z2cG1DZ3BEMXJBZ2ZhVWp2dWZud3BYR0VHUU5PajBZb1pSaEEzUkgydUs5SGIyL2ZrUEx3NUZxRXhDUXl0bWVjeERicEd4MEd5Y0R3SldqZ2hCNzNXUUZGMUV6WXNBTFE4ZC9ITXNlN2wzbmZmYmIwcVJjYVlwaTcxODBRcitrWlc2MVFLem15a2RVeTlQNHBrb0p2THYwVWVoTGpwUEtWVHYvM09CRWtDaGFITFpZSkpxYmJQNlEwYmd5LzlsMGNKSndNUktKZ3VHTHd2eEEwcWZ0a3VGY21DY3h2cVhtdGQwd0VsN0FtMFpBYmVqcnhBWkpONUlNeHBwTGllMC9ORWF2ZDdDelYrREJIZlNRZG84ZWRYWEJjbUJoMjAvRFJHRVFwd3k2ZncvK292dFhXYkdZYzYwWStSSUk1dkd1YkhadWtwUHNIWUN0SlVjTzhTRzVhdlNTaXBlNkJ5OSt4b29JL2dQbEFHZXZMM1YwMUsyMzZzRmJUM2Ivam5DQVFLTUxHQnpFcm5aZGhnQmpjL21PZ1VzazluTzdLUTRDVGgweExKaUpUNWFtRXBna3ZSanhxYWlzdjUyMHZrRkc3eW9raDlWVXdGaFlPWW9BaXl1U29kWURVaDdZcjR5aThJNzdSL0E3Y2twMUVpc3hqc1NJTmxrZjJ6R21iOEE5d1V3NTVuVTlGSld3Y3hISklQbERWUVhVQjIzSGZyQVRlaStYRFJacVp4U251VVZiNUoyY3lXOW9SQWpUcDFpNHdsbGNLdG0xOFZiaU9aMkhuSm5vK01lVTZuTTZ0LzJTTnhjMkxVakZNMTVJQlFUbjNSVkNYU1VKOG1qRWtkSVUzZVUyaFZiQkRPWW5WSlFKeVZsVGNXdzc5TGgxOHUyaVQrdWhvaEVKcWFlV21Hb0ljaS84Vm5BMkdibzFCNEdKendGRG44MUg3RXlGQ1ZjRG1vSmMvYXc0QW0xV1dsRy91dkVRaWhOTll3SnFzdHQ3S0dXdzArNHpJa3Y5VmRzNnJ3SlE4VzJnUGg5b0UrSUMwam91VlBieDg0SzQ4aFQ1MTE1akJGMUVscy9qY1V4ejZSbnZ0NkxYVmtiek1RNWNnc2gyNTdYZmNQMEpxa1NqNlZVTlJKRXBLanpUSTk2MFJPQmNQejZCOUp2eUdmWG9HdHhoeU1McDNQdkEyVVRJdGo2SHpUcmZnaXdOTDVrQ1BDRUZ4WVR6Yk0xeUttNkNVWEgzcGd0cEs0WnpXVFA2L0lmZC9mZkFsWFlkUy9LMGRyMlllWktpQnRQVis0QTNvZ0ljQWw4SDE2S3N6elYxOEhPdEhnT2szUU1tSDN5TGdaUWxJeTNGT1luUmtmbU9SdkdxWUtRRkdsOVFuWmp4Qkd1N2ZYbkJmaUhRdk03SlYzcDhQZHV4VmNCZDN5Y2NTaDczbS9hb2l4b2ZOT1BCN0lLeUVOU29POHBpbHpXWEpTUjR4WDlQeHpLOHNSZ091ZHIwR1lweWM4dDQzSEY0MTRPY0pPajNscFRyRUF6aHVEcTFzeFZsUXhCOEwxOXpzenZEWHU2VkZGcHoyeXNMQUlySkdVNExjSmZ3SExpN3I1M0pyV3lhcHJ2NXhsaENsMnphaFVLMkFmcVNPM05HUHN3V0xqTkVuNEpFT0ttZjNJM0dHdHFJWXltT1ZTUU1WcWE0b2tUby9TRFpXSmp1ejFIQXZlNC9OdmRuVytkUjQ5RnBHZDB4dnlzejdlaHEvT2lsaUFuMlZ5SUEvNTBrRXN3UHpBMkMvemhpbmdQL0NKNlBvNHB3TWxsaVRuSUtIZmUrTWRubDFtQytINURsdjRVby9odXdDQUxTMGR1dmFzOHI5ektOa0F0TFNSQ1FMTlFMYzBpS1l6dnpFalVBV240QzhzcWR0aHV3SEJMM1RvMXhHcUJodDlFY0RsKy9VMWt3clYrR3ZJWGg3OXlvVmlKczNKM09HNGhZeTFHV0R0QWtHcERObDM2UG9RSHVncFBrdnZ5MGVla3N5Zm1YUkxBY0F2V3RTR2tMZ2NLckd1NldzL0dNSUpPa2dOY28xdHVMZm5yeVdnQ1RHRVVuQ0ZsN3RvSTh5SGRqN2tFc2RCZ1JFY1JxR2sydVEySzNHZm1hMk1FUkNzRmFzOTkyZkhEbmlFd1lNd2tGWlZxTVhsNlRRU0hodG80a0tRdTRmemFKdkhnT2pyY3plTHMxbHRRbE9MQjNCaGlDSGljZGNFN2NoeXJOM3lwNzZrd1dETmIyZk5PNjQyWjZvblA0MTZIaVNQR3dmUjlWbVlsdFNtcjM2VGxKUWZiYkdMaGlhZVl4bk8renpPRWtUd3d5VWV0MlhpZ1hmQlVKN3gzR2hFNTB4QXFPZGlIOUs3V3JmKzdBUlFpWlBVL0VnbFZ1dXpwZUhOLzY2a0JNRUJwZWxCQ2c2NGdSUDVmTlBnZUZZYXlYV1RxclhYclJEdXIwaWJlcm9tZS9YUVZ4NEVGdUtPTy96eXQ0c0NUSytvTlcxTHZGL256Vm80Wi91T1dlN0lXbXNlQ040alJ1SE1jaXVUZHJuUnFXN1NKWFJzbis4NWlsMEo1NWRXSDY0UVVtMGhUNEV4Qy9meEJDT2VWTkVQOE5GMklicHVaQmd4Mmx1aU9zSUFpeWU4U1NSSjJKZ0dYZnc5SGt6TytwbXBBbnlzVkRhcWxzSW1lc0p3V3hOYkJpc3BmdVA4dmY3Y0xnazg0T2VJemN6aXZrMjBPUHFDRTNXOTl1SERpblhDdjFESlpTN3JxM2pmRTVKM2M1cllTcWRNdHI0MS9DYzkvbWl5Zmoza3U2dzRMcGFhMWRLdjNqSHhzN0l6dkdVTXNBcUg1QzNQMU1EOVpQRC83Z1ZBSDVEaUNvcUpuam5raTRyM2tKZUdUREV2V2xpRlE3KzNleGZsaElmWExqcHVhWWJtZ3dZTEJHbzFjaHQ5WW5EcXg5T243NGhRTWI1cVdHb3RzU3hGdHFxdk1lSVI1bmZNK0tBQkVDMEhqSkMxOS9WWjlQK0o2Q3FKbmhkN2gyc3hENVFiNmdpMjZSSU1xOUZWMi8zZnlnd1dic2hwelpDLy9VemZFOUg2cHBuRW8xT09UYjRwRVFWOWRVQmFxOGVST1dncUNObCtDL1E2MW5zWVY4bnVDd3ltSWhjMG9idDVtS0hmbkNQei96YWVhT2tmZ01hQ2hEaWNLcWNlcUNBWndISGFSYlVuNzJwV2xvWmlHODR6aDgwbzI0L2cxd1RKWWdDTnE4bnZMb0FrbXdybkZrLzkxTm5IanZ2R29RZDloNmVlZ2QwV2s4U0pkNzJtUDBRWEhaTDUyWFUrSE85VS84Z3VTb0ZtOUplNnFzQ2ZTcUhWa3FVYjNMejA5VVVUNmNtY1krTHpreFR5NFVad0FqbTdnMjhmWGhrd1g3N0tSSXFUaTJrZ3J2TUJFQ09QTmZGMWF3QksxbXI5U0dpUFpESmRzNHdSeE1SVTk5elRFWnJMZDNoYzJJNm5HMGc1d1FuZHdGd2Y1WGk1YmRhSXdkQUwvdk1xZVFseVprSHVVVDBRWGRKZUhla3VEemdnQy8ydlc4bm9iUHArOUFkTSs3K3FDdVFWUEVlaWFKOHA5VU5FRkpTT3VMNnZ3Zm4vdTZJREh3cVVmbTRNSjZTK2o0VGl0eXJPVkY2TW9obVp2ZDN5c2xmUmdDaWUwV3F0Mm9Rdi8wdGJpVFhFTlpjcFFJTk1LcXJsK1J4Z3ZPL2FuZmdmZi9vdng5cDl2eHhpU3JMVUg3M1J3bXAvT0xEOWhjRCtIWXo1Ui8yZHNMamg3QmZ1TEt5T2Q2QmhhU2pEdkNIKzE4akpualV4akxabW0zUnpzRkw0bkx2S2hPbmczVjVIQ3N5QVJ2WndOWTBmVmZwY09RMDVaT0VTMzdRc05zM2RFVlJBL0gwNzJlY0lJRWxzcmFaTWZmZm83NnRxaTRGbjBlQjNhMmZBNjhMZStSOUdzcHp5ZlpmWTVQb2I4NTBrMzRMaVcydDNpTGg0dEZqN3M3MmsxeE1zUTRVeWJOQ1FmWmluT1loSlNmQ0x3SU1VeXRhb2FuOUtJZXRZaHRuTzd2MEdKY21LVVJ6VkRSVnUzUWMyQ0F1bFhlVXBkUlN2TkFXcDJEU1JDSW5UWTRVQi95TEZaV09iNDBjS2ltcjh1UUFWMGx3VlBvb1JOdGp2cE5tMXBFdzZhV0hvMkdsTUx5L0tMMWpTYjBsMUtYaTRDbUQrZkVySUhsUHlUUjJPSUZrTHRJZkwxalFnOFh4Tm9zdkNhdGtsSDdaY3M2VWl1ZVpBTVVMUEJYMXZVQWRnNi9uQ1dUK2lNcjNLZUIweWZteWllUXNCTmJUU0hQMFBmYVc0WGs0SFBzRzNPcjd4ZjBOd0ZrdjIvQmQ1OHZnbWM1b3JaalFucitUNlJleE9FRkwrd01DMkl1S2ZxRDh1TUU0SmdVdllENHp1Z2RPdDJJSmRsWDFGMUEraUFVUUJCTTZCMitVQm15TERNMWg5WktWTXBxVkduL0xrVG1GalQza1EyU2w2N2lJWmxCN1hqbjNsdTVMTUdaRXc4UGdLY2Rrb2xLSk5uRTZwd2tvVHBQeWNaSlRSeEJuSTF1bjFkckc3SGpIaXh5eUdnakQzSlZ4WXRnRkQ2eDRjYkZ1VFFVS0tGY0UvNHBIVmdkQW5hV1ErY3VSZ001YkxoL0x2aTlSQ3ZBQ2RUcUdBR2g0WCtiVm5WSlV2VmZVUHBJSnB2RTd4VFJvUWZDTUZhRVZKblF3cE9sb1hUQTQyMzY5WUI3MlJMMFpnRzg3VWxhZCtnZS9DSVhPcUs1S0ZPRzhBMEdRUnY4dEg5czdFbWQ2elpoaGlKREJXb3Z4L2x4OGhkSWk3M2wxdVlFaWVzdjNobEsvbUlEdWE5U21FNlhhbDNLMDFHanE4WXJTQ0pLTk96TU5TTEsxVVNSelpJUUtRR3pVU2J2bUk5THIvTXNPUmhpWUhSN2tnZGc5UWtvT1EwV3J3empwL01mQnJNWkRreTBaQmxXK2pXVG9CT1VTRUhVbnhqTGhTZzAvelNmbXpVTklsZGNRRHJtdHVEWUtBank1L3pwWjFjK3RZTXBHVXVVY3pYeTlJR2ZEeHFJMFk0dWRwL003c0RmRk1jZlJIZjhCZnE5UlZNS2pWbGVrTGtESXBRQjZIVXB5a1FXSmdBT3pYS3FRVTFXOGRFbXVQWGdyVlRmZHQvMmR2REtUZjRzbVRhU3VXMU93d1FRRk14V3ltc0Fob2RVWmp2Q1NydWtBdmJ2cnhlMGVGZGR6ZDNuZzlydXdpSDdSRXZUUk5ZUm1pWkk4UTV1NkQ0OHJ1U1ZhMTVocVFIaVFVYTJNeVIwOTFuWHhGaGV5a2svcGNxNmlTeXpDdUcvSHRTbTg1a3RPL0xRRjNwWjZoYzFvWEhJOU4xMnV1S2dEd1VrcGFlU3NOY1hBdEdveVRYbHVxRFJNWmE5Y1IxSGNZK2RsTURvOUNLWmlkTTFKRVBMOFBiL2cyMVp6SWhlQTQ3bG5WbG9ES0NzbGd5M0pZdFQ4OXVWUjBqTVliYzR2VE93Sk03V3hvYW5nb0lMRjFZTGxPUjgrNEtoMUxhbms0VVNESGttaGxYNXZIRC93WXBKbHdDekFWK1l3eEtybDE2MXAvT3NaajhqN2daSDB0c1NXSzBTNHNOMmk0T0RQeTJaREdseWgrYU1PR2E4YWd5K3RTaGVnci9vTTZjY3plbXo5cHVId2Zhc2YyZm1tQktjdmhzRUtQbWluL0draFJJSHJXMFlhRHVLTlpYRXNiQnZnWU56L0crTlRJZWJUa2g5M3dDaXhCRmtsNE1xdGRNWTlVdlcxd2cvMzlzZzNDUE1LSTNwMVZnbFRYVDJjakt4Mk9WUTdoL3NvTTM4U1lkQTM3YVNUZm9LUlVuWEp4TVZjdWxnOVg1MEhQV3lkMnVXdEw5dVE4RjZDYkdhaTYxKzVRSmJhOWlvY0ViUjZuUU82TzlmdkxlYXIyTDJFek4rRCtMdlVwblJSSElsVjE1UEpQbURHOUFJZmhEUXllRmwwUkFDRSs1WmsxRUprOTFGTkd1TVZUV2ZQK3ZOSU9MWERDNDVpWWtTbFFKTWZsNjdDcXJNUmJVR0pVSGp6L1pBVlVXbkgzc3dKaFMxd1pMcDh6L2xYaTJEZFZXQ1ZLbFZUdEZuZXJVZG9Va2pvWFVQamwxSGRXTXVEcVovWlZONXRVbTVlTGFSeFNteFArWDVmSFNzTXJGZm04VUlsU0FEV0plb1RGWVYzRTB3cUlIRzhqYkVjNENYdHQ0WUROcVdUNEtJY2x1b2puak1VdlZJNXFWTEJYYjNFVVN3WjBtOEpIalorTW9ETmdEZ2xjalFmTnBSTFFnTlFncCsxL0FvbFhUdittaWp1aHhXY0lwbFdwdmZ6NEJybmpES2RGYytPenNMQnJUOXdBNUlZK2t4RW4vb05MMCtxazVKL05ycU5LZHN1cWlwVGlzd3NhT2tYaDRodnFabmk4eWRmQXROWitQYmpGcVBPcmNOd0kzRzdaVWhwRHArOE9GU0YyTUIrTXVqQlR4WU04YXgyWGNkZytOV09mcUo5YWJZRW5lUmt0TzZVaE5ZQ2twaG9zbUVaRnN0NXB4T085a0ExYzRYdmNjc05uMWV6UDJ1cFAxaHExV3JLc0lrV2VyRUNtaGxHRWxvdStGcXBYNTRLeFZaZnJrZzhieFBJRkE3MlViVzJ4aDJSaXBPbGdZTFRxQTVYR3RvQ3Y4bUFVZ0x4SUhUTGpjSXdzd0JVRGVlak4zUDRrSUFMbEJYdkJlWGNsZFBrWjVTQ2haOVluMm1yZzZlSlR1dlVnRkp4RXRlUDhNcWpybEFEVWVjZjEwZUkvNVpNZ0VBL0doMkE0aFY5RVlueHlzNCtIWDllL0s1YmxzWktpaTBLbzUzYWc9PSIsIm1hYyI6IjliYmU1M2ExOWFkMmM1NmFmZWJlZDU0NmRlNDUxNjZiY2Q0YzA4ZWQ1MWE2MmMxNzM2OGVhYTExZDNkOTE0MzUiLCJ0YWciOiIifQ==', '2024-09-03 07:47:50', '2024-09-03 03:22:17', '2024-09-03 07:47:50');

-- --------------------------------------------------------

--
-- Table structure for table `receivables`
--

CREATE TABLE `receivables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `amount` double DEFAULT NULL,
  `percentage` double DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `emmployment_type` varchar(255) NOT NULL,
  `is_mandatory` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receivable_logs`
--

CREATE TABLE `receivable_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `receivable_id` bigint(20) UNSIGNED NOT NULL,
  `action_by` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stoppage_logs`
--

CREATE TABLE `stoppage_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_deduction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_receivable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `date_to` varchar(255) DEFAULT NULL,
  `date_from` varchar(255) DEFAULT NULL,
  `stopped_at` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_records`
--

CREATE TABLE `time_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_list_id` bigint(20) UNSIGNED NOT NULL,
  `total_working_hours` double NOT NULL,
  `total_working_minutes` double NOT NULL,
  `total_leave_with_pay` double NOT NULL,
  `total_leave_without_pay` double NOT NULL,
  `total_without_pay_days` double NOT NULL,
  `total_present_days` double NOT NULL,
  `total_night_duty_hours` double NOT NULL,
  `total_absences` double NOT NULL,
  `undertime_minutes` double NOT NULL,
  `absent_rate` double NOT NULL,
  `undertime_rate` double NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `fromPeriod` varchar(255) DEFAULT NULL COMMENT 'period from , ex.1-15',
  `toPeriod` varchar(255) DEFAULT NULL COMMENT 'period to , ex.16-31',
  `minutes` double NOT NULL,
  `daily` double NOT NULL,
  `hourly` double NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_logs`
--

CREATE TABLE `transaction_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module` text DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `status` text NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `serverResponse` varchar(255) DEFAULT NULL COMMENT 'Ex. th->getMessage() or any response from server ',
  `affected_entity` text DEFAULT NULL COMMENT 'Ex. modified data IDs,uploaded documents. in JSON FORMAT',
  `employee_profile_id` bigint(20) UNSIGNED NOT NULL,
  `employee_number` text DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `transaction_logs`
--

INSERT INTO `transaction_logs` (`id`, `module`, `action`, `status`, `ip_address`, `remarks`, `serverResponse`, `affected_entity`, `employee_profile_id`, `employee_number`, `name`, `created_at`, `updated_at`) VALUES
(1, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 01:42:56', '2024-09-03 01:42:56'),
(2, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 01:45:21', '2024-09-03 01:45:21'),
(3, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 01:51:57', '2024-09-03 01:51:57'),
(4, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 01:53:28', '2024-09-03 01:53:28'),
(5, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 02:27:13', '2024-09-03 02:27:13'),
(6, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 02:31:19', '2024-09-03 02:31:19'),
(7, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 02:40:12', '2024-09-03 02:40:12'),
(8, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 02:40:16', '2024-09-03 02:40:16'),
(9, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 02:53:29', '2024-09-03 02:53:29'),
(10, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 02:54:43', '2024-09-03 02:54:43'),
(11, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 3:  (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 02:55:44', '2024-09-03 02:55:44'),
(12, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Server error: `POST http://127.0.0.1:8000/api/sign-in` resulted in a `500 Internal Server Error` response:\n<!DOCTYPE html>\r\n<html lang=\"en\" class=\"auto\">\r\n<!--\r\nParseError: syntax error, unexpected token &quot;&lt;&lt;&quot; in (truncated...)\n', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 03:10:06', '2024-09-03 03:10:06'),
(13, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Server error: `POST http://127.0.0.1:8000/api/sign-in` resulted in a `500 Internal Server Error` response:\n<!DOCTYPE html>\n<html lang=\"en\" class=\"auto\">\n<!--\nParseError: syntax error, unexpected token &quot;&lt;&lt;&quot; in (truncated...)\n', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 03:13:39', '2024-09-03 03:13:39'),
(14, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Client error: `POST http://127.0.0.1:8000/api/sign-in` resulted in a `409 Conflict` response:\n{\"message\":\"You are currently logged on to other device. An OTP has been sent to your registered email. If you want to s (truncated...)\n', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 03:17:20', '2024-09-03 03:17:20'),
(15, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Client error: `POST http://127.0.0.1:8000/api/sign-in` resulted in a `403 Forbidden` response:\n{\"message\":\"Employee id or password incorrect.\"}\n', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 03:17:54', '2024-09-03 03:17:54'),
(16, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Undefined array key \"data\"', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 03:18:09', '2024-09-03 03:18:09'),
(17, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Client error: `POST http://127.0.0.1:8000/api/sign-in` resulted in a `409 Conflict` response:\n{\"message\":\"You are currently logged on to other device. An OTP has been sent to your registered email. If you want to s (truncated...)\n', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 03:19:13', '2024-09-03 03:19:13'),
(18, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Client error: `POST http://127.0.0.1:8000/api/sign-in` resulted in a `409 Conflict` response:\n{\"message\":\"You are currently logged on to other device. An OTP has been sent to your registered email. If you want to s (truncated...)\n', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 03:20:12', '2024-09-03 03:20:12'),
(19, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Client error: `POST http://127.0.0.1:8000/api/sign-in` resulted in a `403 Forbidden` response:\n{\"message\":\"Employee id or password incorrect.\"}\n', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 03:20:23', '2024-09-03 03:20:23'),
(20, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 03:22:17', '2024-09-03 03:22:17'),
(21, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Client error: `POST http://127.0.0.1:8000/api/sign-in` resulted in a `409 Conflict` response:\n{\"message\":\"You are currently logged on to other device. An OTP has been sent to your registered email. If you want to s (truncated...)\n', NULL, 0, '1918091351', 'Unknown Credentials', '2024-09-03 06:02:04', '2024-09-03 06:02:04'),
(22, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 06:02:55', '2024-09-03 06:02:55'),
(23, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'cURL error 7: Failed to connect to 127.0.0.1 port 8000 after 2036 ms: Connection refused (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for http://127.0.0.1:8000/api/sign-in', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 06:30:42', '2024-09-03 06:30:42'),
(24, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Server error: `POST http://127.0.0.1:8000/api/getUserInformations` resulted in a `500 Internal Server Error` response:\n<!DOCTYPE html>\r\n<html lang=\"en\" class=\"auto\">\r\n<!--\r\nIlluminate\\Contracts\\Container\\BindingResolutionException: Target  (truncated...)\n', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 06:31:03', '2024-09-03 06:31:03'),
(25, 'UMIS/Authentication', 'Signin Failed', '401', '192.168.36.36', 'Signin attempt failed.', 'Server error: `POST http://127.0.0.1:8000/api/getUserInformations` resulted in a `500 Internal Server Error` response:\n<!DOCTYPE html>\r\n<html lang=\"en\" class=\"auto\">\r\n<!--\r\nIlluminate\\Contracts\\Container\\BindingResolutionException: Target  (truncated...)\n', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 06:31:56', '2024-09-03 06:31:56'),
(26, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 06:33:58', '2024-09-03 06:33:58'),
(27, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 06:34:43', '2024-09-03 06:34:43'),
(28, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 07:03:43', '2024-09-03 07:03:43'),
(29, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 07:13:35', '2024-09-03 07:13:35'),
(30, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 07:14:52', '2024-09-03 07:14:52'),
(31, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 07:17:28', '2024-09-03 07:17:28'),
(32, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 07:18:08', '2024-09-03 07:18:08'),
(33, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 07:22:26', '2024-09-03 07:22:26'),
(34, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 07:27:19', '2024-09-03 07:27:19'),
(35, 'UMIS/Authentication', 'Signin Success', '202', '192.168.36.36', 'Signin attempt successful.', 'Login Success', NULL, 0, '2022090252', 'Unknown Credentials', '2024-09-03 07:47:49', '2024-09-03 07:47:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deductions_deduction_group_id_foreign` (`deduction_group_id`);

--
-- Indexes for table `deduction_groups`
--
ALTER TABLE `deduction_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deduction_logs`
--
ALTER TABLE `deduction_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deduction_logs_deduction_id_foreign` (`deduction_id`);

--
-- Indexes for table `employee_computed_salaries`
--
ALTER TABLE `employee_computed_salaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_computed_salaries_time_record_id_foreign` (`time_record_id`);

--
-- Indexes for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_deductions_employee_list_id_foreign` (`employee_list_id`),
  ADD KEY `employee_deductions_deduction_id_foreign` (`deduction_id`);

--
-- Indexes for table `employee_deduction_logs`
--
ALTER TABLE `employee_deduction_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_deduction_logs_employee_deduction_id_foreign` (`employee_deduction_id`);

--
-- Indexes for table `employee_deduction_trails`
--
ALTER TABLE `employee_deduction_trails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_deduction_trails_employee_deduction_id_foreign` (`employee_deduction_id`);

--
-- Indexes for table `employee_lists`
--
ALTER TABLE `employee_lists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_receivables`
--
ALTER TABLE `employee_receivables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_receivables_employee_list_id_foreign` (`employee_list_id`),
  ADD KEY `employee_receivables_receivable_id_foreign` (`receivable_id`);

--
-- Indexes for table `employee_receivable_logs`
--
ALTER TABLE `employee_receivable_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_receivable_logs_employee_receivable_id_foreign` (`employee_receivable_id`);

--
-- Indexes for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_salaries_employee_list_id_foreign` (`employee_list_id`);

--
-- Indexes for table `employee_taxes`
--
ALTER TABLE `employee_taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_taxes_employee_list_id_foreign` (`employee_list_id`);

--
-- Indexes for table `employment_types`
--
ALTER TABLE `employment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `excluded_employees`
--
ALTER TABLE `excluded_employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excluded_employees_employee_list_id_foreign` (`employee_list_id`),
  ADD KEY `excluded_employees_payroll_headers_id_foreign` (`payroll_headers_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_payrolls`
--
ALTER TABLE `general_payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `general_payrolls_payroll_headers_id_foreign` (`payroll_headers_id`),
  ADD KEY `general_payrolls_employee_list_id_foreign` (`employee_list_id`);

--
-- Indexes for table `general_payroll_trails`
--
ALTER TABLE `general_payroll_trails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `general_payroll_trails_general_payrolls_id_foreign` (`general_payrolls_id`),
  ADD KEY `general_payroll_trails_payroll_headers_id_foreign` (`payroll_headers_id`),
  ADD KEY `general_payroll_trails_employee_list_id_foreign` (`employee_list_id`);

--
-- Indexes for table `imports`
--
ALTER TABLE `imports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imports_deduction_id_foreign` (`deduction_id`),
  ADD KEY `imports_receivables_id_foreign` (`receivables_id`);

--
-- Indexes for table `login_trails`
--
ALTER TABLE `login_trails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll_headers`
--
ALTER TABLE `payroll_headers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_employee_id_unique` (`employee_id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`);

--
-- Indexes for table `receivables`
--
ALTER TABLE `receivables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receivable_logs`
--
ALTER TABLE `receivable_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receivable_logs_receivable_id_foreign` (`receivable_id`);

--
-- Indexes for table `stoppage_logs`
--
ALTER TABLE `stoppage_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stoppage_logs_employee_deduction_id_foreign` (`employee_deduction_id`),
  ADD KEY `stoppage_logs_employee_receivable_id_foreign` (`employee_receivable_id`);

--
-- Indexes for table `time_records`
--
ALTER TABLE `time_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time_records_employee_list_id_foreign` (`employee_list_id`);

--
-- Indexes for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `deduction_groups`
--
ALTER TABLE `deduction_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `deduction_logs`
--
ALTER TABLE `deduction_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_computed_salaries`
--
ALTER TABLE `employee_computed_salaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_deduction_logs`
--
ALTER TABLE `employee_deduction_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_deduction_trails`
--
ALTER TABLE `employee_deduction_trails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_lists`
--
ALTER TABLE `employee_lists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_receivables`
--
ALTER TABLE `employee_receivables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_receivable_logs`
--
ALTER TABLE `employee_receivable_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_taxes`
--
ALTER TABLE `employee_taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employment_types`
--
ALTER TABLE `employment_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `excluded_employees`
--
ALTER TABLE `excluded_employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_payrolls`
--
ALTER TABLE `general_payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_payroll_trails`
--
ALTER TABLE `general_payroll_trails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imports`
--
ALTER TABLE `imports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_trails`
--
ALTER TABLE `login_trails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `payroll_headers`
--
ALTER TABLE `payroll_headers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `receivables`
--
ALTER TABLE `receivables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receivable_logs`
--
ALTER TABLE `receivable_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stoppage_logs`
--
ALTER TABLE `stoppage_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_records`
--
ALTER TABLE `time_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `deductions`
--
ALTER TABLE `deductions`
  ADD CONSTRAINT `deductions_deduction_group_id_foreign` FOREIGN KEY (`deduction_group_id`) REFERENCES `deduction_groups` (`id`);

--
-- Constraints for table `deduction_logs`
--
ALTER TABLE `deduction_logs`
  ADD CONSTRAINT `deduction_logs_deduction_id_foreign` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`id`);

--
-- Constraints for table `employee_computed_salaries`
--
ALTER TABLE `employee_computed_salaries`
  ADD CONSTRAINT `employee_computed_salaries_time_record_id_foreign` FOREIGN KEY (`time_record_id`) REFERENCES `time_records` (`id`);

--
-- Constraints for table `employee_deductions`
--
ALTER TABLE `employee_deductions`
  ADD CONSTRAINT `employee_deductions_deduction_id_foreign` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`id`),
  ADD CONSTRAINT `employee_deductions_employee_list_id_foreign` FOREIGN KEY (`employee_list_id`) REFERENCES `employee_lists` (`id`);

--
-- Constraints for table `employee_deduction_logs`
--
ALTER TABLE `employee_deduction_logs`
  ADD CONSTRAINT `employee_deduction_logs_employee_deduction_id_foreign` FOREIGN KEY (`employee_deduction_id`) REFERENCES `employee_deductions` (`id`);

--
-- Constraints for table `employee_deduction_trails`
--
ALTER TABLE `employee_deduction_trails`
  ADD CONSTRAINT `employee_deduction_trails_employee_deduction_id_foreign` FOREIGN KEY (`employee_deduction_id`) REFERENCES `employee_deductions` (`id`);

--
-- Constraints for table `employee_receivables`
--
ALTER TABLE `employee_receivables`
  ADD CONSTRAINT `employee_receivables_employee_list_id_foreign` FOREIGN KEY (`employee_list_id`) REFERENCES `employee_lists` (`id`),
  ADD CONSTRAINT `employee_receivables_receivable_id_foreign` FOREIGN KEY (`receivable_id`) REFERENCES `receivables` (`id`);

--
-- Constraints for table `employee_receivable_logs`
--
ALTER TABLE `employee_receivable_logs`
  ADD CONSTRAINT `employee_receivable_logs_employee_receivable_id_foreign` FOREIGN KEY (`employee_receivable_id`) REFERENCES `employee_receivables` (`id`);

--
-- Constraints for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD CONSTRAINT `employee_salaries_employee_list_id_foreign` FOREIGN KEY (`employee_list_id`) REFERENCES `employee_lists` (`id`);

--
-- Constraints for table `employee_taxes`
--
ALTER TABLE `employee_taxes`
  ADD CONSTRAINT `employee_taxes_employee_list_id_foreign` FOREIGN KEY (`employee_list_id`) REFERENCES `employee_lists` (`id`);

--
-- Constraints for table `excluded_employees`
--
ALTER TABLE `excluded_employees`
  ADD CONSTRAINT `excluded_employees_employee_list_id_foreign` FOREIGN KEY (`employee_list_id`) REFERENCES `employee_lists` (`id`),
  ADD CONSTRAINT `excluded_employees_payroll_headers_id_foreign` FOREIGN KEY (`payroll_headers_id`) REFERENCES `payroll_headers` (`id`);

--
-- Constraints for table `general_payrolls`
--
ALTER TABLE `general_payrolls`
  ADD CONSTRAINT `general_payrolls_employee_list_id_foreign` FOREIGN KEY (`employee_list_id`) REFERENCES `employee_lists` (`id`),
  ADD CONSTRAINT `general_payrolls_payroll_headers_id_foreign` FOREIGN KEY (`payroll_headers_id`) REFERENCES `payroll_headers` (`id`);

--
-- Constraints for table `general_payroll_trails`
--
ALTER TABLE `general_payroll_trails`
  ADD CONSTRAINT `general_payroll_trails_employee_list_id_foreign` FOREIGN KEY (`employee_list_id`) REFERENCES `employee_lists` (`id`),
  ADD CONSTRAINT `general_payroll_trails_general_payrolls_id_foreign` FOREIGN KEY (`general_payrolls_id`) REFERENCES `general_payrolls` (`id`),
  ADD CONSTRAINT `general_payroll_trails_payroll_headers_id_foreign` FOREIGN KEY (`payroll_headers_id`) REFERENCES `payroll_headers` (`id`);

--
-- Constraints for table `imports`
--
ALTER TABLE `imports`
  ADD CONSTRAINT `imports_deduction_id_foreign` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `imports_receivables_id_foreign` FOREIGN KEY (`receivables_id`) REFERENCES `receivables` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `receivable_logs`
--
ALTER TABLE `receivable_logs`
  ADD CONSTRAINT `receivable_logs_receivable_id_foreign` FOREIGN KEY (`receivable_id`) REFERENCES `receivables` (`id`);

--
-- Constraints for table `stoppage_logs`
--
ALTER TABLE `stoppage_logs`
  ADD CONSTRAINT `stoppage_logs_employee_deduction_id_foreign` FOREIGN KEY (`employee_deduction_id`) REFERENCES `employee_deductions` (`id`),
  ADD CONSTRAINT `stoppage_logs_employee_receivable_id_foreign` FOREIGN KEY (`employee_receivable_id`) REFERENCES `employee_receivables` (`id`);

--
-- Constraints for table `time_records`
--
ALTER TABLE `time_records`
  ADD CONSTRAINT `time_records_employee_list_id_foreign` FOREIGN KEY (`employee_list_id`) REFERENCES `employee_lists` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
