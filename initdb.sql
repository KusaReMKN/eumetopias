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

CREATE TABLE IF NOT EXISTS Priorities (
	-- ID used to identify the priority
	priId	INTEGER	PRIMARY KEY NOT NULL,
	-- Title of the priority
	priTxt	TEXT	NOT NULL
);
INSERT INTO Priorities ( priId, priTxt ) VALUES (  3, 'やらないとまずいこと' );
INSERT INTO Priorities ( priId, priTxt ) VALUES (  2, 'やること'             );
INSERT INTO Priorities ( priId, priTxt ) VALUES (  1, 'やりたいこと'         );
INSERT INTO Priorities ( priId, priTxt ) VALUES (  0, 'やったこと'           );
INSERT INTO Priorities ( priId, priTxt ) VALUES ( -1, 'やりたくないこと'     );
INSERT INTO Priorities ( priId, priTxt ) VALUES ( -2, 'やらないこと'         );
INSERT INTO Priorities ( priId, priTxt ) VALUES ( -3, 'やるとまずいこと'     );

CREATE TABLE IF NOT EXISTS Tasks (
	-- ID used to identify the task
	taskId	TEXT	PRIMARY KEY NOT NULL,
	-- ID of the task owner
	owner	INTEGER	NOT NULL REFERENCES Users ( userId ),
	-- Title of the task
	title	TEXT	NOT NULL,
	-- Date and time of task creation
	ctime	NUMERIC	NOT NULL DEFAULT CURRENT_TIMESTAMP,
	-- Current priority of the task
	currPri	INTEGER	NOT NULL REFERENCES Priorities ( priId ),
	-- Date and time of task modification
	mtime	NUMERIC,
	-- Previous priority of the task
	PrevPri	INTEGER REFERENCES Priorities ( priId ),
	-- Details of the task
	details	TEXT
);
