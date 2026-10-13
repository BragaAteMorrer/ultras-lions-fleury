ALTER TABLE users
    ADD reset_password_token VARCHAR(128) DEFAULT NULL,
    ADD reset_password_token_expires_at DATETIME DEFAULT NULL;

CREATE INDEX IDX_RESET_PASSWORD_TOKEN ON users (reset_password_token);
