CREATE DATABASE IF NOT EXISTS `bookshelf` DEFAULT CHARSET utf8mb4;
GRANT ALL ON `bookshelf`.* TO 'bookshelf'@'%';
FLUSH PRIVILEGES;