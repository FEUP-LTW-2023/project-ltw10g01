PRAGMA foreign_keys=on;

.mode columns
.headers on
.nullvalue NULL

DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Role;
DROP TABLE IF EXISTS Department;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS Hashtag;
DROP TABLE IF EXISTS Reply;

DROP TABLE IF EXISTS User_Roles;
DROP TABLE IF EXISTS User_Departments;
DROP TABLE IF EXISTS Ticket_Hashtags;

CREATE TABLE User (
    idUser INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT NOT NULL,
    email TEXT NOT NULL,
    password TEXT NOT NULL,
    CONSTRAINT UNIQUE_User_username UNIQUE (username),
    CONSTRAINT UNIQUE_User_email UNIQUE (email)
);

CREATE TABLE Role(
    idRole INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL
    CONSTRAINT CHECK_Role_name CHECK (name = 'ADMIN' OR name = 'CLIENT' OR name = 'AGENT'),
);

CREATE TABLE Department(
    idDepartment INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    CONSTRAINT UNIQUE_Department_name UNIQUE (name)
);

CREATE TABLE Ticket(
    idTicket INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    priority INTEGER NOT NULL,
    stage TEXT NOT NULL,
    create_date DATE,
    version INTEGER,
    current_version BIT(1),
    UUID INTEGER NOT NULL,
    cria REFERENCES User,
    resolve REFERENCES User,
    idDepartment REFERENCES Department
);

CREATE TABLE Hashtag(
    idTicket INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    CONSTRAINT UNIQUE_Hashtag_name UNIQUE (name)
);

CREATE TABLE Reply(
    idReply INTEGER PRIMARY KEY AUTOINCREMENT,
    message TEXT NOT NULL,
    create_date DATE,
    idTicket REFERENCES Ticket
)

CREATE TABLE User_Roles(
    idUser INTEGER REFERENCES User,
    idRole INTEGER REFERENCES Role,
    PRIMARY KEY (idUser, idRole)
);

CREATE TABLE User_Departments(
    idUser INTEGER REFERENCES User,
    idDepartment INTEGER REFERENCES Department,
    PRIMARY KEY (idUser, idDepartment)
);

CREATE TABLE Ticket_Hashtags(
    idTicket INTEGER REFERENCES Ticket,
    idHashtag INTEGER REFERENCES Hashtag,
    PRIMARY KEY (idTicket, idHashtag)
);