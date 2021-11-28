# graff.is
Logging graffiti

## what does this run on?
This is a PHP page, currently running off a PHP/Apache2/MySQL server.

Database name: `base_db`

To create the "graffiti" table:

```SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `graffiti` (
  `id` int NOT NULL,
  `file_name` tinytext CHARACTER SET utf8 COLLATE utf8_icelandic_ci NOT NULL,
  `ip_address` tinytext CHARACTER SET utf8 COLLATE utf8_icelandic_ci NOT NULL,
  `date_taken` datetime NOT NULL,
  `date_uploaded` datetime NOT NULL,
  `gps_latitude` float NOT NULL,
  `gps_longitude` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_icelandic_ci;


ALTER TABLE `graffiti`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `graffiti`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
```