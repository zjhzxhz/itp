<?php

namespace Solutions\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the course types table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class CourseTypeTable
{
    /**
     * The Table Gateway object is intended to provide an object that 
     * represents a table in a database, and the methods of this object 
     * mirror the most common operations on a database table.
     * 
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * The contructor of the UserTable class.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Get all records from the course_types table.
     * @return an object which is an instance of ResultSet, which contains
     *         data of all course types.
     */
    public function fetchAll()
    {
        $resultSet  = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * Get the unique id of the course type by its slug.
     * @param  int $courseTypeSlug - the unique slug of the course type
     * @return an Object of CourseType which contains information of
     *         the course type
     */
    public function getCourseTypeID($courseTypeSlug)
    {
        $rowset     = $this->tableGateway->select(
            array( 
                'course_type_slug'  => $courseTypeSlug,
            )
        );
        return $rowset->current();
    }

    /**
     * Get the unique slug of the course type by its id.
     * @param  int $courseTypeID - the unique id of the course type
     * @return an Object of CourseType which contains information of
     *         the course type
     */
    public function getCourseTypeSlug($courseTypeID)
    {
        $rowset     = $this->tableGateway->select(
            array( 
                'course_type_id'  => $courseTypeID,
            )
        );
        return $rowset->current();
    }
}