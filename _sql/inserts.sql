--
-- Data for comapnies
--

INSERT INTO companies (company_id, name, full_name, deleted) VALUES (1, 'KGH', 'KGH Inc.', 0);
INSERT INTO companies (company_id, name, full_name, deleted) VALUES (2, 'GL', 'GoodLuck', 0);
INSERT INTO companies (company_id, name, full_name, deleted) VALUES (3, 'RepelORM', 'RepelORM Developers', 0);

--
-- Data for admins
--

INSERT INTO admins (admin_id, company_id, login, email, password, first_name, last_name, deleted) VALUES (1, 1, 'alice', 'alice@repelorm.org', 'mySecretPassword123', 'Alice', 'Kowalski', 0);
INSERT INTO admins (admin_id, company_id, login, email, password, first_name, last_name, deleted) VALUES (2, 1, 'bob', 'bob@repelorm.org', 'iLikeIt!', 'Bob', 'Starowsky', 0);
INSERT INTO admins (admin_id, company_id, login, email, password, first_name, last_name, deleted) VALUES (3, 2, 'charlie', 'charlie@repelorm.org', 'cH4rl!3', 'Charlie', 'Smith', 0);
INSERT INTO admins (admin_id, company_id, login, email, password, first_name, last_name, deleted) VALUES (4, 2, 'dirck', 'dirck@propelorm.org', 'postgreSQL99', 'Dirck', 'Bush', 0);
INSERT INTO admins (admin_id, company_id, login, email, password, first_name, last_name, deleted) VALUES (5, 2, 'ester', 'ester@propelorm.org', 'qwertyYTREWQ', 'Ester', 'Wozniacki', 0);
INSERT INTO admins (admin_id, company_id, login, email, password, first_name, last_name, deleted) VALUES (6, 3, 'lukasz', 'lukasz@propelorm.org', 'logic()', 'Lukasz', 'Schmidtke', 0);
INSERT INTO admins (admin_id, company_id, login, email, password, first_name, last_name, deleted) VALUES (7, 3, 'konrad', 'konrad@propelorm.org', 'maciej', 'Konrad', 'Frysiak', 0);

--
-- Data for privileges
--

INSERT INTO privileges (privilege_id, name, description) VALUES (1, 'am', 'Admin Manager');
INSERT INTO privileges (privilege_id, name, description) VALUES (2, 'um', 'User Manager');
INSERT INTO privileges (privilege_id, name, description) VALUES (3, 'dm', 'Document Manager');
INSERT INTO privileges (privilege_id, name, description) VALUES (4, 'cm', 'Company Manager');

--
-- Data for users
--

INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (1, 'alan77', 'iLikeDogs', '2014-12-19 07:43:59.221211+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (2, 'barry1970', '1qazxsw2', '2014-12-19 07:44:21.037437+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (3, 'chuck_', 'arc29!00', '2014-12-19 07:45:02.755987+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (5, 'elieee', 'Fatal1ty', '2014-12-19 07:46:05.869375+01', 1);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (8, 'honda', 's2000@ftw', '2014-12-19 07:47:03.157455+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (10, 'jessie', '!@DfSE5rygd', '2014-12-19 07:47:53.62149+01', 1);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (11, 'kessler', '!@#dzaqqqw3jhgfERT', '2014-12-19 07:48:08.125548+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (13, 'marc', 'HeHeHeHe:)', '2014-12-19 07:48:37.237629+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (17, 'rubyMaster', 'poiuylkjhg!@#321', '2014-12-19 07:49:34.5814+01', 1);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (18, 'smithAgent', 'm4tR!X', '2014-12-19 07:49:53.58174+01', 1);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (19, 'teresa', '<\html/>', '2014-12-19 07:50:33.004951+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (21, 'ugo_44', '20141205', '2014-12-19 07:51:03.485663+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (4, 'dany-123', 'd1a2n3y4', '2014-12-19 07:45:32.213351+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (6, 'forest1', 'runForestRun!', '2014-12-19 07:46:19.41332+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (7, 'gary90', 'P0kEmoN!', '2014-12-19 07:46:37.668917+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (9, 'inter78', 'Viv4MeD!0L4n', '2014-12-19 07:47:42.395578+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (12, 'larry7', 'RTFUf76$%RYThgfd', '2014-12-19 07:48:19.49348+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (14, 'norbertD', 'dttfessfDDDD', '2014-12-19 07:48:49.909481+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (15, 'olaf_bb', '3246546532', '2014-12-19 07:48:57.493339+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (16, 'parent', 'qwedsazxc123', '2014-12-19 07:49:16.989677+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (22, 'wUwU', 'fGGr545555', '2014-12-19 07:52:48.341779+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (23, 'xades', 'f!nGeR', '2014-12-19 07:53:07.197323+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (24, 'yetta', 'yRMaiRP', '2014-12-19 07:53:43.093805+01', 0);
INSERT INTO users (user_id, login, password, date_created, deleted) VALUES (25, 'zick', 'dCtWtZnnDn()', '2014-12-19 07:54:04.541967+01', 0);

--
-- Data for user_datas
--

INSERT INTO user_datas (user_data_id, user_id, date_created, first_name, last_name, address, zip, city) VALUES (3, 1, '2014-12-19 07:56:36.197805+01', 'Alan', 'Coen', '1634 Crystal Goose Path', '328-1077', 'Sublimity');
INSERT INTO user_datas (user_data_id, user_id, date_created, first_name, last_name, address, zip, city) VALUES (4, 2, '2014-12-19 07:57:55.174165+01', 'Barry', 'McEwan', '8621 Noble Gardens', '898-7778', 'Manhattan');
INSERT INTO user_datas (user_data_id, user_id, date_created, first_name, last_name, address, zip, city) VALUES (7, 3, '2014-12-19 07:58:30.636186+01', 'Chuck', 'Hardy', '9731 Hidden Cloud Wood', '792-2056', 'Dennehotso');
INSERT INTO user_datas (user_data_id, user_id, date_created, first_name, last_name, address, zip, city) VALUES (8, 4, '2014-12-19 08:00:29.157095+01', 'Dany', 'Rosario', '5527 Colonial Manor', '712-9086', 'Luseland');
INSERT INTO user_datas (user_data_id, user_id, date_created, first_name, last_name, address, zip, city) VALUES (9, 5, '2014-12-19 08:01:40.261976+01', 'Eliabeth', 'Ho', '3673 Sleepy Pine Walk', '144-4901', 'Wealthy');
INSERT INTO user_datas (user_data_id, user_id, date_created, first_name, last_name, address, zip, city) VALUES (10, 6, '2014-12-19 08:02:39.942368+01', 'Barry', 'Forest', '9947 Dusty Forest Close', '885-2184', 'Radisson');
