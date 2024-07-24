-- -------------------------------------------------------------
-- -------------------------------------------------------------
-- TablePlus 1.1.8
--
-- https://tableplus.com/
--
-- Database: dev.sqlite
-- Generation Time: 2024-06-01 20:32:13.812445
-- -------------------------------------------------------------

DROP TABLE "comments";


CREATE TABLE comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    text TEXT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT DEFAULT NULL,
    is_reply INTEGER DEFAULT 0,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    upvotes INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)

INSERT INTO "comments" (`id`, `text`, `user_id`, `parent_id`, `is_reply`, `date_created`, `upvotes`) VALUES 
('10', 'hello 103', '2', '103', '0', '1715112500', '0'),
('11', 'I have a question about the second paragraph. Can you clarify?', '2', '100', '0', '2024-05-13 06:47:20', '0'),
('12', 'hello 103 again', '2', '103', '0', '1715583093', '0'),
('13', 'nice writing style. can you tech me?', '2', '103', '0', '1715587205', '0'),
('14', 'hello from fend', '2', '101', '0', '1715590462', '0'),
('15', 'hellow again', '2', '101', '0', '1715590966', '0'),
('16', 'hello from dynamic', '2', '101', '0', '1715608570', '0'),
('17', 'hello from dynamic', '2', '101', '0', '1715608677', '0'),
('18', 'hello from dynamic', '2', '101', '0', '1715608802', '0'),
('19', 'hello now better', '2', '101', '0', '1715608936', '0'),
('20', 'hello now better', '2', '101', '0', '1715608948', '0'),
('21', 'hey there', '2', '101', '0', '1715609224', '0'),
('22', 'hey2', '2', '101', '0', '1715609291', '0'),
('23', 'first comment', '3', '102', '0', '1715609333', '0'),
('24', 'second comment', '3', '102', '0', '1715609385', '0');

