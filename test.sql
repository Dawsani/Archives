DROP TABLE IF EXISTS password_reset;

CREATE TABLE password_reset (
    user_id INT NOT NULL,
    code CHAR(6) NOT NULL,
    expire DATETIME NOT NULL,
    UNIQUE(code)
);

ALTER TABLE user ADD UNIQUE (email);

SELECT user_id
FROM password_reset
WHERE expire > NOW();
