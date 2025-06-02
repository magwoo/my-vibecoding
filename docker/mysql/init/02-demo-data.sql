USE phone_shop;

-- Добавление демо-телефонов
INSERT INTO products (name, description, price, brand, specs, image_path) VALUES
-- Apple
('iPhone 15 Pro', 'Флагманский смартфон Apple с высокой производительностью и продвинутой камерой', 124990.00, 'Apple', 
'{"ram": "8 ГБ", "storage": "256 ГБ", "screen": "6.1 дюйм", "processor": "A17 Pro", "camera": "48 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fpisces.bbystatic.com%2Fimage2%2FBestBuy_US%2Fimages%2Fproducts%2F6525%2F6525489_sd.jpg&f=1&nofb=1&ipt=f1c37107427c9be1acf2749439f7a39ea9615ad90ce5da36b6f0a295edfc937e'),

('iPhone 15', 'Новый смартфон Apple с улучшенной камерой и производительностью', 99990.00, 'Apple', 
'{"ram": "6 ГБ", "storage": "128 ГБ", "screen": "6.1 дюйм", "processor": "A16 Bionic", "camera": "48 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fstatic1.pocketnowimages.com%2Fwordpress%2Fwp-content%2Fuploads%2F2023%2F09%2Fpbi-iphone-15-plus.png&f=1&nofb=1&ipt=b7d951fac6a34a77b071e3e65bbcc7c6dcf3695c0b36d7a590375f60dac55137'),

('iPhone 14', 'Прошлогодний флагман Apple с отличной камерой и батареей', 79990.00, 'Apple', 
'{"ram": "6 ГБ", "storage": "128 ГБ", "screen": "6.1 дюйм", "processor": "A15 Bionic", "camera": "12 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fleoscherer.com.br%2Fwp-content%2Fuploads%2F2022%2F10%2FiPhone-14-Azul.png&f=1&nofb=1&ipt=4bb71eb9656fc9cf4fbcc982221ba1c516cfd52804c19593f0dff6eaa4d56faa'),

-- Samsung
('Samsung Galaxy S24 Ultra', 'Премиальный смартфон с мощным процессором и великолепным экраном', 134990.00, 'Samsung', 
'{"ram": "12 ГБ", "storage": "512 ГБ", "screen": "6.8 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "200 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fstatic.vecteezy.com%2Fsystem%2Fresources%2Fpreviews%2F041%2F329%2F788%2Fnon_2x%2Fsamsung-galaxy-s24-ultra-titanium-blue-back-view-free-png.png&f=1&nofb=1&ipt=299c53c25d6c21e1d26920b09fded73e378450987a146163265a6e342bccf8c1'),

('Samsung Galaxy S24+', 'Флагманский смартфон с большим экраном и отличной производительностью', 109990.00, 'Samsung', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.7 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fa.storyblok.com%2Ff%2F113473%2F1000x1000%2F05a722c3a0%2F233050_233054_samsung_galaxy_s24_plus_onyx-black.png&f=1&nofb=1&ipt=619e4374cb7ace7355a4bba781433f4c235f4b937a1a69c3009294047b1cf561'),

('Samsung Galaxy A54', 'Среднебюджетный смартфон с хорошим соотношением цены и качества', 34990.00, 'Samsung', 
'{"ram": "8 ГБ", "storage": "128 ГБ", "screen": "6.4 дюйм", "processor": "Exynos 1380", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fmobilestoreimages.blob.core.windows.net%2Fmobilestoreimages%2FSAMSUNG%2520GALAXY%2520A54%25205G-black-full.png&f=1&nofb=1&ipt=3be7e138bac883671c9854b9b6b0e1bd99ed0b69f2231ab260f515cdbabf388b'),

-- Xiaomi
('Xiaomi 14 Ultra', 'Флагманский смартфон с продвинутой системой камер Leica', 119990.00, 'Xiaomi', 
'{"ram": "16 ГБ", "storage": "512 ГБ", "screen": "6.73 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fgagadget.com%2Fmedia%2Fuploads%2Fxiaomi14ultra1.png&f=1&nofb=1&ipt=b9d2a6da20c68ac12b25a995903a164ea5207a13dd7833245a8f02d2d9e8ce0e'),

('Xiaomi 14', 'Премиальный смартфон с отличным экраном и производительностью', 89990.00, 'Xiaomi', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.36 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fwww.gizmochina.com%2Fwp-content%2Fuploads%2F2023%2F06%2Fpms_1698307433.52595447.png&f=1&nofb=1&ipt=c54124d45241c8a0f2a5194c5c92e14289b0095ae127bfca0d1d586cbbc1d5b4'),

('Xiaomi Redmi Note 13 Pro', 'Доступный смартфон с хорошими характеристиками', 29990.00, 'Xiaomi', 
'{"ram": "8 ГБ", "storage": "256 ГБ", "screen": "6.67 дюйм", "processor": "Snapdragon 7s Gen 2", "camera": "200 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ffscl01.fonpit.de%2Fdevices%2F64%2F3264.png&f=1&nofb=1&ipt=edd5b4eea4b38de1e07ad885ff1adea8b5b63e9056b12127122c66ae68495e5e'),

-- Google
('Google Pixel 8 Pro', 'Флагман Google с передовыми технологиями AI и отличной камерой', 99990.00, 'Google', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.7 дюйм", "processor": "Google Tensor G3", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fwww.att.com%2Fscmsassets%2Fglobal%2Fdevices%2Fphones%2Fgoogle%2Fgoogle-pixel-8-pro%2Fcarousel%2Fbay-1.png&f=1&nofb=1&ipt=4494c8a8c4d5b4827f4bc0371bd65fd97dd34e2b39cef5b95cde442247b05eed'),

('Google Pixel 8', 'Компактный флагман с мощной камерой и чистым Android', 79990.00, 'Google', 
'{"ram": "8 ГБ", "storage": "128 ГБ", "screen": "6.2 дюйм", "processor": "Google Tensor G3", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fmobilitycg.com%2Fwp-content%2Fuploads%2F2024%2F05%2FGoogle-Pixel-8.png&f=1&nofb=1&ipt=5b9b11d0242c1978fc925bbdf2dd65b284fb1417594f67dbefbcbc711d819c67'),

-- OnePlus
('OnePlus 12', 'Мощный флагман с быстрой зарядкой и высокой производительностью', 84990.00, 'OnePlus', 
'{"ram": "16 ГБ", "storage": "512 ГБ", "screen": "6.82 дюйм", "processor": "Snapdragon 8 Gen 3", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fimg-prd-pim.poorvika.com%2Fproduct%2FOneplus-12-5g-flowy-emerald-512gb-16gb-ram-Front-Back-View.png&f=1&nofb=1&ipt=98497e26313c07eee454586038190033efde8f75b4d8dec659feedda1e0bdb84'),

('OnePlus Nord 3', 'Доступный смартфон с хорошим экраном и быстрой зарядкой', 39990.00, 'OnePlus', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.74 дюйм", "processor": "Dimensity 9000", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Foasis.opstatics.com%2Fcontent%2Fdam%2Foasis%2Fpage%2F2023%2Fglobal%2Fproduct%2Fvitamin%2Fvitamin-spec-black.png&f=1&nofb=1&ipt=12ed973aebe619c3a3b01babc65cdf49467803045d3cabcdc5d4767e44400a7f'),

-- Huawei
('Huawei P60 Pro', 'Премиальный смартфон с великолепной камерой и дизайном', 89990.00, 'Huawei', 
'{"ram": "12 ГБ", "storage": "512 ГБ", "screen": "6.67 дюйм", "processor": "Snapdragon 8+ Gen 1", "camera": "48 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fconsumer.huawei.com%2Fcontent%2Fdam%2Fhuawei-cbg-site%2Fcommon%2Fmkt%2Fpdp%2Fadmin-image%2Fphones%2Fp60-art%2Fazure-blue.png&f=1&nofb=1&ipt=563c8a3045b1339dd8786ec12b0a3cc98dae637fe8cec835ed532e651edcd7e2'),

-- Nothing
('Nothing Phone (2)', 'Инновационный смартфон с уникальным дизайном и чистым Android', 59990.00, 'Nothing', 
'{"ram": "12 ГБ", "storage": "256 ГБ", "screen": "6.7 дюйм", "processor": "Snapdragon 8+ Gen 1", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fstorage.comprasmartphone.com%2Fsmartphones%2Fnothing-phone-2.png&f=1&nofb=1&ipt=61ebe1aa8638ea9664135c55f8f06faeff4092522c2f925529652a569713df00'),

-- Vivo
('Vivo X100 Pro', 'Флагманский смартфон с профессиональной камерой', 94990.00, 'Vivo', 
'{"ram": "16 ГБ", "storage": "512 ГБ", "screen": "6.78 дюйм", "processor": "Dimensity 9300", "camera": "50 Мп"}', 
'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fasia-exstatic-vivofs.vivo.com%2FPSee2l50xoirPK7y%2F1705113695844%2Fc3eabffa61e342632779907d3c8bcebc.png&f=1&nofb=1&ipt=cc2830c858f0c3603eda1448678884da78d322123444357247f333265d6ca4c3');
