USE phone_shop;

-- Добавление демо-телефонов
INSERT INTO products (name, description, price, brand, specs, image_path) VALUES
-- Apple
('iPhone 15 Pro', 'Флагманский смартфон Apple с высокой производительностью и продвинутой камерой', 124990.00, 'Apple', 
'{"ram": "8 ГБ", "storage": "256 ГБ", "screen": "6.1 дюйм", "processor": "A17 Pro", "camera": "48 Мп"}', 
'/uploads/iphone15pro.jpg'),

('iPhone 15', 'Новый смартфон Apple с улучшенной камерой и производительностью', 99990.00, 'Apple', 
'{"ram": "6 ГБ", "storage": "128 ГБ", "screen": "6.1 дюйм", "processor": "A16 Bionic", "camera": "48 Мп"}', 
'/uploads/iphone15.jpg'),

('iPhone 14', 'Прошлогодний флагман Apple с отличной камерой и батареей', 79990.00, 'Apple', 
'{"ram": "6 ГБ", "storage": "128 ГБ", "screen": "6.1 дюйм", "processor": "A15 Bionic", "camera": "12 Мп"}', 
'/uploads/iphone14.jpg'),

-- Samsung
('Samsung Galaxy S24 Ultra', 'Премиальный смартфон с мощным процессором и великолепным экраном', 134990.00, 'Samsung', 
'{"ram": "12 ГБ", "storage": "512 ГБ", "screen": "6.8 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "200 Мп"}', 
'/uploads/s24ultra.jpg'),

('Samsung Galaxy S24+', 'Флагманский смартфон с большим экраном и отличной производительностью', 109990.00, 'Samsung', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.7 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "50 Мп"}', 
'/uploads/s24plus.jpg'),

('Samsung Galaxy A54', 'Среднебюджетный смартфон с хорошим соотношением цены и качества', 34990.00, 'Samsung', 
'{"ram": "8 ГБ", "storage": "128 ГБ", "screen": "6.4 дюйм", "processor": "Exynos 1380", "camera": "50 Мп"}', 
'/uploads/a54.jpg'),

-- Xiaomi
('Xiaomi 14 Ultra', 'Флагманский смартфон с продвинутой системой камер Leica', 119990.00, 'Xiaomi', 
'{"ram": "16 ГБ", "storage": "512 ГБ", "screen": "6.73 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "50 Мп"}', 
'/uploads/xiaomi14ultra.jpg'),

('Xiaomi 14', 'Премиальный смартфон с отличным экраном и производительностью', 89990.00, 'Xiaomi', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.36 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "50 Мп"}', 
'/uploads/xiaomi14.jpg'),

('Xiaomi Redmi Note 13 Pro', 'Доступный смартфон с хорошими характеристиками', 29990.00, 'Xiaomi', 
'{"ram": "8 ГБ", "storage": "256 ГБ", "screen": "6.67 дюйм", "processor": "Snapdragon 7s Gen 2", "camera": "200 Мп"}', 
'/uploads/redminote13pro.jpg'),

-- Google
('Google Pixel 8 Pro', 'Флагман Google с передовыми технологиями AI и отличной камерой', 99990.00, 'Google', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.7 дюйм", "processor": "Google Tensor G3", "camera": "50 Мп"}', 
'/uploads/pixel8pro.jpg'),

('Google Pixel 8', 'Компактный флагман с мощной камерой и чистым Android', 79990.00, 'Google', 
'{"ram": "8 ГБ", "storage": "128 ГБ", "screen": "6.2 дюйм", "processor": "Google Tensor G3", "camera": "50 Мп"}', 
'/uploads/pixel8.jpg'),

-- OnePlus
('OnePlus 12', 'Мощный флагман с быстрой зарядкой и высокой производительностью', 84990.00, 'OnePlus', 
'{"ram": "16 ГБ", "storage": "512 ГБ", "screen": "6.82 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "50 Мп"}', 
'/uploads/oneplus12.jpg'),

('OnePlus Nord 3', 'Доступный смартфон с хорошим экраном и быстрой зарядкой', 39990.00, 'OnePlus', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.74 дюйм", "processor": "Dimensity 9000", "camera": "50 Мп"}', 
'/uploads/oneplusnord3.jpg'),

-- Huawei
('Huawei P60 Pro', 'Премиальный смартфон с великолепной камерой и дизайном', 89990.00, 'Huawei', 
'{"ram": "12 ГБ", "storage": "512 ГБ", "screen": "6.67 дюйм", "processor": "Snapdragon 8+ Gen 1", "camera": "48 Мп"}', 
'/uploads/huaweip60pro.jpg'),

-- Nothing
('Nothing Phone (2)', 'Инновационный смартфон с уникальным дизайном и чистым Android', 59990.00, 'Nothing', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.7 дюйм", "processor": "Snapdragon 8+ Gen 1", "camera": "50 Мп"}', 
'/uploads/nothingphone2.jpg'),

-- Vivo
('Vivo X100 Pro', 'Флагманский смартфон с профессиональной камерой', 94990.00, 'Vivo', 
'{"ram": "16 ГБ", "storage": "512 ГБ", "screen": "6.78 дюйм", "processor": "Dimensity 9300", "camera": "50 Мп"}', 
'/uploads/vivox100pro.jpg');
