<?php

class AbiturientDataGateway 
{
    private $dbh;
    
    private static $col = ['name',
                           'surname',
                           'gender',
                           'groupNumber',
                           'email',
                           'sumUSE',
                           'yearOfBirth',
                           'location',
                           'code'
                          ];

    public function __construct(PDO $pdo) 
    {
        return $this->dbh = $pdo;
    }
    
    public function insert(array $set)
    {
        if (count($set) !== 8) {
            throw new Exception("Ошибка: не верный набор данных. В наборе должно
                                 восемь значений");
        }
        $columns = implode(',', self::$col);
        $set[8] = bin2hex(random_bytes(16));
        $values = implode(',', array_fill(0, count($set), '?'));       
        $sql = "INSERT INTO abiturient($columns)
                VALUES ($values)";
        $sth = $this->dbh->prepare($sql); 
        $sth->execute($set);
        return $this->dbh->lastInsertId();
    }
    
    public function show($orderBy = 'sumUSE', $direction = 'ASC')
    {
        $sth = $this->dbh->prepare("SELECT name, surname, groupNumber, sumUSE
                                    FROM abiturient
                                    ORDER BY $orderBy $direction
                                   ");
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function find($id, $orderBy = 'sumUSE', $direction = 'ASC')
    {
        $sth = $this->dbh->prepare("SELECT name, surname, groupNumber, sumUSE
                                    FROM abiturient
                                    WHERE id = $id
                                    ORDER BY $orderBy $direction");
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findWhere($str, $orderBy = 'sumUSE', $direction = 'ASC')
    {
        $str = urldecode($str);
        $sth = $this->dbh->prepare("SELECT name, surname, groupNumber, sumUSE
                                    FROM abiturient 
                                    WHERE concat(name, surname, groupNumber, sumUSE) 
                                    LIKE '%$str%'
                                    ORDER BY $orderBy $direction");        
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findEmail($value) 
    {
        $sth = $this->dbh->prepare("SELECT id
                                    FROM abiturient
                                    WHERE email = '$value'");
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_NUM);
    }
    
    public function findCode($id) 
    {
        $sth = $this->dbh->prepare("SELECT code
                                    FROM abiturient
                                    WHERE id = $id");
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_NUM);
    }
    
    public function showOne($code) 
    {
        $sth = $this->dbh->prepare("SELECT *
                                    FROM abiturient 
                                    WHERE code = '$code'");
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }   
    
    public function update($id, array $set)
    {
        if (count($set) !== 9) {
            throw new Exception("Ошибка: не верный набор данных. В наборе должно
                                 восемь значений");
        }        
        $assignment_list = '';
        $params = [];

        foreach (self::$col as $key => $value) {
            $assignment_list .= "`$value` = :$value,";
            $params[$value] = $set[$key];
        }

        $params['id'] = $id;
        $assignment_list = rtrim($assignment_list, ',');        
        $sth = $this->dbh->prepare("UPDATE abiturient
                SET $assignment_list
                WHERE id = :id");
        return $sth->execute($params);
    }
    
    public function delete($id) 
    {
        $sth = $this->dbh->prepare("DELETE FROM abiturient
                                    WHERE id = $id");
        return $sth->execute();
    }
    
}
