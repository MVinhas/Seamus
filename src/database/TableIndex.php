<?php
namespace Database;

use Database\Interfaces\QueryInterface;
use Database\Query;

class TableIndex extends Query
{
    public $table;

    public $constraint;

    public $value;

    public $type;

    public function __construct($table)
    {
        parent::__construct();
        $this->table = $table;    
    }

    public function constraint($constraint)
    {
        $this->constraint = $constraint;
        return $this;
    }

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    public function done()
    {
  
        $sql = "ALTER TABLE $this->table ADD CONSTRAINT $this->constraint $this->type($this->value)";
        $sql = $this->db->real_escape_string($sql);
        return $this->db->query($sql);   
    }

}