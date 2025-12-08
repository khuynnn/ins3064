-- Xoá theo thứ tự để không lỗi khoá ngoại
DROP TABLE IF EXISTS loans;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS publishers;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- 1) USERS
CREATE TABLE users (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  name      VARCHAR(100) NOT NULL,
  username  VARCHAR(50)  NOT NULL UNIQUE,
  password  VARCHAR(255) NOT NULL,
  role      ENUM('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) CATEGORIES
CREATE TABLE categories (
  id    INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) PUBLISHERS (thêm phone, address)
CREATE TABLE publishers (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  name     VARCHAR(100) NOT NULL UNIQUE,
  phone    VARCHAR(20),
  address  VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) BOOKS (thêm author, quantity)
CREATE TABLE books (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(255) NOT NULL,
  author       VARCHAR(150) NOT NULL,
  quantity     INT NOT NULL DEFAULT 0,
  category_id  INT NOT NULL,
  publisher_id INT NOT NULL,

  INDEX idx_books_category (category_id),
  INDEX idx_books_publisher (publisher_id),

  CONSTRAINT fk_books_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT fk_books_publisher
    FOREIGN KEY (publisher_id) REFERENCES publishers(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) LOANS
CREATE TABLE loans (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  book_id     INT NOT NULL,
  borrow_date DATE NOT NULL,
  return_date DATE NULL,
  is_returned TINYINT(1) NOT NULL DEFAULT 0,

  INDEX idx_loans_user (user_id),
  INDEX idx_loans_book (book_id),

  CONSTRAINT fk_loans_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

  CONSTRAINT fk_loans_book
    FOREIGN KEY (book_id) REFERENCES books(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
