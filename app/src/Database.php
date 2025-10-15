<?php

namespace App;

use ErrorException;
use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    public function __construct(
        readonly string $host,
        readonly string $dbname,
        readonly string $username,
        readonly string $password
    )
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
        } catch (PDOException $e) {
            throw new ErrorException("PDO connect: {$e->getMessage()}");
        }
    }

    public function query($sql): array
    {
        try {
            $result = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new ErrorException("PDO query: {$e->getMessage()}");
        }
        return $result;
    }

    public function migrateDemo(): void
    {

        $sql = "
            select * 
            from information_schema.tables 
            where table_schema = '{$this->dbname}' 
              AND (table_name = 'employees' OR table_name = 'software' OR table_name = 'software_employees' OR table_name = 'installation_requests')
        ";
        if (empty($this->query($sql))) {
            $this->query("
                create table employees(
                    employee_id int unsigned auto_increment,
                    employee varchar(255) default '' not null,
                    email varchar(255) default '' not null,
                    is_active int default 1  not null,
                    primary key (employee_id),
                    unique key (email)
                );
                insert into employees (employee, email) values ('X', 'x@gmail.com'), ('Y', 'y@gmail.com');
            ");
            $this->query("
                create table software(
                    software_id int unsigned auto_increment,
                    software varchar(255) default '' not null,
                    is_active int default 1  not null,
                    primary key (software_id)
                );
                insert into software (software) values ('А'), ('Б'), ('В'), ('Г'), ('Д'), ('Е - 0 установщиков'), ('Ж - 2 установщика');
            ");
            $this->query("
                create table software_employees
                (
                    software_id int unsigned not null,
                    employee_id int unsigned not null,
                    minutes int unsigned not null,
                    primary key (software_id, employee_id)
                );
                insert into software_employees (software_id, employee_id, minutes) values (1, 1, 30);
                insert into software_employees (software_id, employee_id, minutes) values (2, 1, 40);
                insert into software_employees (software_id, employee_id, minutes) values (3, 2, 20);
                insert into software_employees (software_id, employee_id, minutes) values (4, 2, 30);
                insert into software_employees (software_id, employee_id, minutes) values (5, 2, 60);
                insert into software_employees (software_id, employee_id, minutes) values (7, 1, 99);
                insert into software_employees (software_id, employee_id, minutes) values (7, 2, 111);
            ");
            $this->query("
                create table installation_requests(
                    request_id int unsigned auto_increment,
                    employee_id int unsigned not null,
                    software_id int unsigned not null,
                    time_from int unsigned not null,
                    time_to int unsigned not null,
                    company varchar(255) default '' not null,
                    contact varchar(255) default '' not null,
                    phone varchar(255) default '' not null,
                    email varchar(255) default '' not null,
                    primary key (request_id)
                );
            ");
        }
    }
}