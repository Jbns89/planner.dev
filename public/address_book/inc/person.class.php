<?php

require_once 'model.class.php';
require_once 'address.class.php';

class Person extends Model
{
    protected $first_name = '';
    protected $last_name  = '';
    protected $phone;

    protected $addresses;

    public function __get($key)
    {
        if ($key == 'addresses') {
            
            if (!isset($this->addresses)) {
                
                $this->loadAddresses();
            }
            return $this->addresses;
            
        } elseif ($key == 'phone') {
            
            if (empty($this->phone)) {
                
                return $this->phone;
                
            } else {
                
                return '(' . substr($this->phone, 0, 3) . ') ' . substr($this->phone, 3, 3) . '-' . substr($this->phone, 6);
            }
            
        } else {
            
            return parent::__get($key);
        }
    }

    public function __set($key, $value)
    {
        switch ($key) {
            
            case 'first_name':
            // The validation is checking  that the variable -> ($value), is for the 
              //  field -> 'First Name' does not exceed the number of characters (31)
                $this->validate($value, 'First name', 31);
                break;
                
            case 'last_name':
                $this->validate($value, 'Last name', 63);
                break;
                
            case 'phone':
                if (empty($value)) {
                    
                    $this->phone = $value;
                    
                    //This is using regular expressions for configuring phone numbers 
                } elseif (preg_match('/\D*(\d{3})\D*(\d{3})\D*(\d{4})\D*/', $value, $matches)) {
                    
                    $this->phone = $matches[1].$matches[2].$matches[3];
                    
                } else {
                    
                    throw new Exception('Phone numbers should be formatted as (999) 999-9999');
                    
                }
                return;
                
            case 'addresses':
                // this feels hacky; I'm sorry
                //This is checking to make sure the parameter being passed is
                // an array as opposed null or string
                if (!is_array($value)) {
                    
                    throw new InvalidArgumentException('Addresses property must be an array');
                }

                $this->addresses = $value;
                return;
        }

        parent::__set($key, $value);
    }

    public static function all()
    {
        global $dbc;

        return $dbc->query('SELECT id, first_name, last_name, phone FROM people')->fetchAll(PDO::FETCH_CLASS, 'Person', [$dbc]);
    }

    public static function find($id)
    {
        global $dbc;
        //Selecting a users contact info based off their id 
        $stmt = $dbc->prepare('SELECT id, first_name, last_name, phone FROM people WHERE id = ?');
        $person = $stmt->execute([$id])->fetchObject('Person', [$dbc]);

        if (empty($person)) {
            throw new OutOfBoundsException("Could not find a person for ID: $id");
        }

        return $person;
    }

    protected function insert()
    {
        $stmt = $this->dbc->prepare('INSERT INTO people (first_name, last_name, phone) VALUES (:firstName, :lastName, :phone)');

        $stmt->bindValue(':firstName',   $this->first_name, PDO::PARAM_STR);
        $stmt->bindValue(':lastName',    $this->last_name,  PDO::PARAM_STR);
        $stmt->conditionalBind(':phone', $this->phone,      PDO::PARAM_STR);

        $stmt->execute();

        $this->id = $this->dbc->lastInsertId();

        return $this->id;
    }

    protected function update()
    {
        $stmt = $this->dbc->prepare('UPDATE people SET first_name = :firstName, last_name = :lastName, phone = :phone WHERE id = :id');

        $stmt->bindValue(':id',          $this->id,         PDO::PARAM_INT);
        $stmt->bindValue(':firstName',   $this->first_name, PDO::PARAM_STR);
        $stmt->bindValue(':lastName',    $this->last_name,  PDO::PARAM_STR);
        $stmt->conditionalBind(':phone', $this->phone,      PDO::PARAM_STR);

        $stmt->execute();
    }

    public function delete()
    {
        if (!isset($this->id)) {
            return false;
        }

        $addrStmt = $this->dbc->prepare('DELETE FROM addresses WHERE id IN (
                                           SELECT address_id FROM person_address
                                           WHERE person_id = ? AND address_id IN (
                                             SELECT address_id FROM person_address
                                             GROUP BY address_id HAVING count(*) = 1
                                           )
                                         )');

        $addrStmt->execute([$this->id]);

        $this->dbc->prepare('DELETE FROM people WHERE id = ?')->execute([$this->id]);
    }

    public function addAddress(Address $address)
    {
        $this->addresses[] = $address;
    }

    public static function globalData($limit = DEFAULT_LIMIT, $offset = 0)
    {
        global $dbc;
        
        //p = people | pa = people address | a = addresses
        $infoSql = 'SELECT p.id, p.first_name, p.last_name, p.phone,
                           a.id AS address_id, a.street, a.apt, a.city, a.state, a.zip, a.plus_four
                    FROM people p
                    LEFT JOIN person_address pa ON p.id = pa.person_id
                    LEFT JOIN addresses a ON pa.address_id = a.id
                    LIMIT :limit OFFSET :offset';

        $infoStmt = $dbc->prepare($infoSql);
        $infoStmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $infoStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $infoStmt->execute();

        $people = array();
        
        //Once fetch runs out of rows it will return false setting
        // $row to false
        while ($row = $infoStmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($people[$row['id']])) {
                
                //Creating a new person object
                $person = new Person($dbc);

                $person->first_name = $row['first_name'];
                $person->last_name  = $row['last_name'];
                $person->phone      = $row['phone'];
                $person->id         = $row['id'];

                // Manually set addresses property to an empty array
                // Make sure model doesn't try to auto load them if empty
                $person->addresses  = array();

                $people[$row['id']] = $person;
            }

            if (!empty($row['address_id'])) {
                
                //creating a new address object
                $address = new Address($dbc);

                $address->street    = $row['street'];
                $address->apt       = $row['apt'];
                $address->city      = $row['city'];
                $address->state     = $row['state'];
                $address->zip       = $row['zip'];
                //the plus four is an addition to the zip
                $address->plus_four = $row['plus_four'];
                $address->id        = $row['address_id'];

                $people[$row['id']]->addAddress($address);
            }
        }

        return $people;
    }
    
    protected function loadAddresses()
    {
        $this->addresses = [];
        
        //if this id doesn't exist go back
        if (!isset($this->id)) {
            return;
        }
        
        $query = 'SELECT id, street, apt, city, state, zip, plus_four
                  FROM addresses WHERE id IN (
                    SELECT address_id FROM person_address WHERE person_id = ?
                  )';

        $this->addresses = $this->dbc->prepare($query)->execute([$this->id])->fetchAll(PDO::FETCH_CLASS, 'Address', [$dbc]);
    }
}
