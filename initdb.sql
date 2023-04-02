CREATE TABLE IF NOT EXISTS Users (
	-- ID used to identify the user
	userId	INTEGER	PRIMARY KEY AUTOINCREMENT NOT NULL,
	-- Name used to identify the user
	name	TEXT	NOT NULL UNIQUE,
	-- Hash value of the password
	pwHash	TEXT	NOT NULL,
	-- Name used for display
	display	TEXT
);
