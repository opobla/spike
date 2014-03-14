<?php
namespace CALM\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;

class CALM_ori
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

}
