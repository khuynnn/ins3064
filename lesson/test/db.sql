-- Tạo lại từ đầu để đảm bảo tính nhất quán
DROP TABLE IF EXISTS loans;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS publishers;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- 1. Bảng User
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) DEFAULT 'user' -- 'admin' hoặc 'user'
);

-- 2. Bảng Danh mục (Categories)
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

-- 3. Bảng Nhà xuất bản (Publishers) - [MỚI]
CREATE TABLE publishers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  address VARCHAR(255),
  phone VARCHAR(20)
);

-- 4. Bảng Sách (Books) - Cập nhật liên kết
CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  author VARCHAR(100) NOT NULL,
  year INT,
  quantity INT DEFAULT 1, -- Số lượng trong kho
  category_id INT,
  publisher_id INT, -- [MỚI] Liên kết NXB
  image VARCHAR(255) DEFAULT NULL, -- (Optional: Để mở rộng sau này)
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL
);

-- 5. Bảng Mượn Trả (Loans) - [MỚI]
CREATE TABLE loans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  borrow_date DATE DEFAULT CURRENT_DATE,
  return_date DATE DEFAULT NULL, -- Ngày thực tế trả
  status VARCHAR(20) DEFAULT 'borrowing', -- 'borrowing' (đang mượn), 'returned' (đã trả)
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- DỮ LIỆU MẪU
INSERT INTO users (fullname, username, email, password, role) VALUES 
('Admin System', 'admin', 'admin@gmail.com', '123456', 'admin'),
('Sinh Vien A', 'user', 'user@gmail.com', '123456', 'user');

INSERT INTO categories (name) VALUES ('Công nghệ'), ('Kinh tế'), ('Văn học');
INSERT INTO publishers (name, address, phone) VALUES ('NXB Trẻ', 'TPHCM', '090123456'), ('NXB Kim Đồng', 'Hà Nội', '098765432');

INSERT INTO books (title, author, year, quantity, category_id, publisher_id) VALUES 
('Lập trình PHP Nâng cao', 'Phạm Huy', 2024, 5, 1, 1),
('Doraemon Truyện Dài', 'Fujiko F', 2020, 3, 3, 2);