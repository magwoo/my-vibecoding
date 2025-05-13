-- Добавление полей name, phone и address в таблицу users
ALTER TABLE users ADD COLUMN name VARCHAR(255) NULL AFTER role;
ALTER TABLE users ADD COLUMN phone VARCHAR(50) NULL AFTER name;
ALTER TABLE users ADD COLUMN address TEXT NULL AFTER phone; 