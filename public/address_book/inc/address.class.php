<?php

require_once 'model.class.php';

class Address extends Model
{
    protected $street = '';
    protected $city   = '';
    protected $state  = '';
    protected $zip    = '';
    protected $apt;
    protected $plus_four;

    public function __set($key, $value)
    {
        global $states;

        switch ($key) {
            case 'street':
                $this->validate($value, 'Street', 127);
                break;
            case 'apt':
                if (!empty($value)) {
                    $this->validate($value, 'Apt', 31);
                }
                break;
            case 'city':
                $this->validate($value, 'City', 63);
                break;
            case 'state':
                if ($value === 0 || $value === '0') {
                    throw new Exception('Please select a state.');
                } elseif (!array_key_exists($value, $states)) {
                    throw new Exception('Invalid value for state.');
                }
                break;
            case 'zip':
                if (!preg_match('/\d{5}/', $value)) {
                    throw new Exception('Zip codes should be exactly five digits.');
                }
                break;
            case 'plus_four':
                if (!empty($value) && !preg_match('/\d{4}/', $value)) {
                    throw new Exception('Zip+4 should be exactly four digits.');
                }
                break;
        }

        parent::__set($key, $value);
    }

    public static function all()
    {
        global $dbc;

        $stmt = $dbc->query('SELECT id, street, apt, city, state, zip, plus_four FROM addresses');
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Address', [$dbc]);
    }

    public static function find($id)
    {
        global $dbc;

        $stmt = $dbc->prepare('SELECT id, street, apt, city, state, zip, plus_four FROM addresses WHERE id = ?');
        $address = $stmt->execute([$id])->fetchObject('Address', [$dbc]);

        if (empty($address)) {
            throw new OutOfBoundsException("Could not find an addres for ID: $id");
        }

        return $address;
    }

    protected function insert()
    {
        $stmt = $this->dbc->prepare('INSERT INTO addresses (street, apt, city, state, zip, plus_four)
                                     VALUES (:street, :apt, :city, :state, :zip, :plusFour)');

        $stmt->bindValue(':street',         $this->street,    PDO::PARAM_STR);
        $stmt->bindValue(':city',           $this->city,      PDO::PARAM_STR);
        $stmt->bindValue(':state',          $this->state,     PDO::PARAM_STR);
        $stmt->bindValue(':zip',            $this->zip,       PDO::PARAM_STR);
        $stmt->conditionalBind(':apt',      $this->apt,       PDO::PARAM_STR);
        $stmt->conditionalBind(':plusFour', $this->plus_four, PDO::PARAM_STR);

        $stmt->execute();

        $this->id = $this->dbc->lastInsertId();

        return $this->id;
    }

    protected function update()
    {
        $stmt = $this->dbc->prepare('UPDATE addresses SET street = :street, apt = :apt, city = :city, state = :state,
                                       zip = :zip, plus_four = :plusFour WHERE id = :id');

        $stmt->bindValue(':street',         $this->street,    PDO::PARAM_STR);
        $stmt->bindValue(':city',           $this->city,      PDO::PARAM_STR);
        $stmt->bindValue(':state',          $this->state,     PDO::PARAM_STR);
        $stmt->bindValue(':zip',            $this->zip,       PDO::PARAM_STR);
        $stmt->conditionalBind(':apt',      $this->apt,       PDO::PARAM_STR);
        $stmt->conditionalBind(':plusFour', $this->plus_four, PDO::PARAM_STR);
        $stmt->bindValue(':id',             $this->id,        PDO::PARAM_INT);

        $stmt->execute();
    }

    public function delete()
    {
        if (!isset($this->id)) {
            return false;
        }

        $this->dbc->prepare('DELETE FROM addresses WHERE id = ?')->execute([$this->id]);
    }

    public function linkPerson($personId)
    {
        if (!isset($this->id)) {
            return false;
        }

        $stmt = $this->dbc->prepare('INSERT INTO person_address (person_id, address_id) VALUES (:personId, :addressId)');

        $stmt->bindValue(':personId',  $personId, PDO::PARAM_INT);
        $stmt->bindValue(':addressId', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function unlinkPerson($personId)
    {
        if (!isset($this->id)) {
            return false;
        }

        $stmt = $this->dbc->prepare('DELETE FROM person_address WHERE person_id = :personId AND address_id = :addressId');

        $stmt->bindValue(':personId',  $personId, PDO::PARAM_INT);
        $stmt->bindValue(':addressId', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($this->personCount() == 0) {
            $this->delete();
        }
    }

    public function personCount()
    {
        if (!isset($this->id)) {
            return 0;
        }

        return $this->dbc->prepare('SELECT count(*) FROM person_address WHERE address_id = ?')->execute([$this->id])->fetchColumn();
    }

    public function isLinked($personId)
    {
        if (!isset($this->id)) {
            return false;
        }

        $stmt = $this->dbc->prepare('SELECT count(*) FROM person_address WHERE person_id = ? AND address_id = ?');
        return $stmt->execute([$personId, $this->id])->fetchColumn();
    }
}
