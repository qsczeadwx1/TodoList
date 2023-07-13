CREATE DATABASE todo_list;

USE todo_list;

CREATE TABLE todo_list_info(
            list_no INT PRIMARY KEY AUTO_INCREMENT
            ,list_title VARCHAR(100) NOT NULL
            ,list_detail VARCHAR(1000)
            ,list_start_date DATETIME NOT NULL
            ,list_due_date DATETIME NOT NULL
            ,list_clear_date DATETIME 
            ,list_clear_flg CHAR(1) NOT NULL DEFAULT('0')
            ,list_imp_flg CHAR(1) NOT NULL DEFAULT('0')
            );
