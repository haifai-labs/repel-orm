CREATE TABLE admins
(
admin_id SERIAL,
company_id INTEGER,
login TEXT NOT NULL,
email TEXT NOT NULL,
password TEXT NOT NULL,
first_name TEXT NOT NULL,
last_name TEXT NOT NULL,
deleted INTEGER NOT NULL DEFAULT 0,
PRIMARY KEY (admin_id)
);

CREATE TABLE companies
(
company_id SERIAL,
name TEXT NOT NULL,
full_name TEXT NOT NULL,
deleted INTEGER NOT NULL DEFAULT 0,
PRIMARY KEY (company_id)
);

CREATE TABLE privileges
(
privilege_id SERIAL,
name TEXT NOT NULL,
description TEXT NOT NULL,
PRIMARY KEY (privilege_id)
);

CREATE TABLE admins_privileges
(
admin_id INTEGER,
privilege_id INTEGER,
date TIMESTAMPTZ NOT NULL DEFAULT now(),
deleted INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE documents
(
document_id SERIAL,
user_data_id INTEGER NOT NULL,
date TIMESTAMPTZ NOT NULL DEFAULT now(),
full_number TEXT NOT NULL,
content TEXT NOT NULL,
PRIMARY KEY (document_id)
);

CREATE TABLE users
(
user_id SERIAL,
login TEXT NOT NULL,
password TEXT NOT NULL,
date_created TIMESTAMPTZ NOT NULL DEFAULT now(),
deleted INTEGER NOT NULL DEFAULT 0,
PRIMARY KEY (user_id)
);

CREATE TABLE user_datas
(
user_data_id SERIAL,
user_id INTEGER NOT NULL,
date_created TIMESTAMPTZ NOT NULL DEFAULT now(),
first_name TEXT NOT NULL,
last_name TEXT NOT NULL,
address TEXT NOT NULL,
zip TEXT NOT NULL,
city TEXT NOT NULL,
PRIMARY KEY (user_data_id)
);

ALTER TABLE admins ADD FOREIGN KEY (company_id) REFERENCES companies (company_id);

ALTER TABLE admins_privileges ADD FOREIGN KEY (admin_id) REFERENCES admins (admin_id);

ALTER TABLE admins_privileges ADD FOREIGN KEY (privilege_id) REFERENCES privileges (privilege_id);

ALTER TABLE documents ADD FOREIGN KEY (user_data_id) REFERENCES user_datas (user_data_id);

ALTER TABLE user_datas ADD FOREIGN KEY (user_id) REFERENCES users (user_id);