CREATE DATABASE IF NOT EXISTS `bookshelf` DEFAULT CHARSET utf8mb4;
CREATE DATABASE IF NOT EXISTS `bookshelf_test` DEFAULT CHARSET utf8mb4;
GRANT ALL ON `bookshelf`.* TO 'bookshelf'@'%';
GRANT ALL ON `bookshelf_test`.* TO 'bookshelf'@'%';
FLUSH PRIVILEGES;
