<?php
// 	function create_db()
// 	{
// 		global $dbname;
//
// 		if(!file_exists($dbname)) {
// 			$dbhandle = new SQLiteDatabase($dbname);
// 			$dbhandle->query("CREATE TABLE user (IDUser INTEGER PRIMARY KEY NOT NULL, username VARCHAR(80) UNIQUE NOT NULL, mess VARCHAR(255), mode VARCHAR(10))");
// 			$dbhandle->query("CREATE TABLE msg (IDMsg INTEGER PRIMARY KEY NOT NULL, message BLOB NOT NULL, data DATE NOT NULL, letto BOOLEAN NOT NULL, notified BOOLEAN NOT NULL, IDFrom VARCHAR(80) NOT NULL, IDTo VARCHAR(80) NOT NULL, FOREIGN KEY (IDFrom) references user(IDUser), FOREIGN KEY (IDTo) references user(IDUser))");
// 		}
// 	}
?>