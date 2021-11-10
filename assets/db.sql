CREATE TABLE `urls` (
  id INT PRIMARY KEY AUTO_INCREMENT,
  url VARCHAR(255)
) ENGINE = InnoDB;

CREATE TABLE `urls_images` (
  id INT PRIMARY KEY AUTO_INCREMENT,
  url_id INT,
  count SMALLINT,
  date DATE,
  CONSTRAINT url_date_unique UNIQUE (url_id, date),
  FOREIGN KEY (url_id)
    REFERENCES urls (id)
    ON UPDATE RESTRICT
) ENGINE = InnoDB;


INSERT INTO `urls` (`id`, `url`) VALUES
(1, 'https://burst.shopify.com/'),
(2, 'https://pixabay.com/'),
(3, 'https://www.freeimages.com/'),
(4, 'https://kaboompics.com/'),
(5, 'https://www.lifeofpix.com/');

INSERT INTO `urls_images` (`url_id`, `count`, `date`) VALUES
(1, 20, '2021-11-10'),
(2, 30, '2021-11-10'),
(3, 35, '2021-11-10'),
(4, 40, '2021-11-10'),
(5, 45, '2021-11-10');