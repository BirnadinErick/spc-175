-- -------------------------------------------------------------
-- -------------------------------------------------------------
-- TablePlus 1.1.8
--
-- https://tableplus.com/
--
-- Database: dev.sqlite
-- Generation Time: 2024-06-01 20:32:37.721238
-- -------------------------------------------------------------

DROP TABLE "users";


CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL,
    password TEXT NOT NULL,
    year_of_batch INTEGER NOT NULL,
    country CHAR(2) NOT NULL,
    address_line_1 TEXT NOT NULL,
    address_line_2 TEXT,
    zip_code TEXT NOT NULL,
    telephone TEXT NOT NULL,
    CONSTRAINT unique_email UNIQUE (email)
)

INSERT INTO "users" (`id`, `first_name`, `last_name`, `email`, `password`, `year_of_batch`, `country`, `address_line_1`, `address_line_2`, `zip_code`, `telephone`) VALUES 
('1', 'Birnadin', 'Erick', 'a@e.com', '$2y$10$ioC7lVfuUbtCCB3aF/nPQuMKXAT1ZQrYSu886mF.nHOcz4wHbzyBy', '2022', 'LK', 'No. 117', 'David Road', '40000', '0765812511'),
('2', 'Jesus', 'Christ', 'j@h', '$2y$10$Q3DAJeKBxN41A1P4v9DOkOV5dUyGNBCatoma0p0qF5Cf9jrjHRRNS', '2022', 'DE', 'hous 2', '', '94432', '392042384'),
('3', 'Jesus', 'Christ', 'j@h1', '$2y$10$zLKFFYJPZPIDJHC8jcCTjubOvZvgTPlNHNix1rxYvEjSiJ0i34pEm', '2022', 'DE', 'hous 2', '', '94432', '0392042384');

