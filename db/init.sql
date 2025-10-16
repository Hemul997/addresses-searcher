use gr_test;

CREATE TABLE IF NOT EXISTS address_requests (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `search_text` char(255) NOT NULL,
    `created_at` DATETIME NOT NULL,
    INDEX search_text_idx (`search_text`),
    PRIMARY KEY (`id`)
) CHARSET=utf8 AUTO_INCREMENT=1;