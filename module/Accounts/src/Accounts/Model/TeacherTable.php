<?php

namespace Accounts\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the teacher table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class TeacherTable
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
	 * The contructor of the TeacherTable class.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Get all records from the teacher table by pagination.
     *
	 * @param  int $pageNumber - current number of the page
     * @param  int $limit - max number of courses in a page
     * @param  int $catelogyID - the teaching field of the teachers
	 * @return an object which is an instance of ResultSet, which contains
	 *         data of all teachers.
	 */
	public function fetchAll($pageNumber, $limit, $catelogyID)
	{
		$offset     = ( $pageNumber - 1 ) * $limit;
        $resultSet  = $this->tableGateway->select(function (Select $select) use ($offset, $limit, $catelogyID) {
            if ( $catelogyID != 0 ) {
                $select->join('itp_teaching_field', 
                              'itp_teaching_field.uid = itp_teachers.uid');
                $select->where("course_type_id = $catelogyID");
            }
            $select->where("teacher_is_approved = true");

            $select->order(new Expression('CONVERT(teacher_name USING GBK)'));
            $select->group('itp_teachers.uid');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
	}

	/**
     * Get number of records in the teachers table.
     * @param  int $catelogyID - the teaching field of the teachers
     * @return an integer which stands for the number of records in the teacher
     *         table
     */
    public function getNumberOfTeachers($catelogyID)
    {
        return $this->tableGateway->select(function (Select $select) use ($catelogyID) {
            if ( $catelogyID != 0 ) {
                $select->join('itp_teaching_field', 
                              'itp_teaching_field.uid = itp_teachers.uid');
                $select->where("course_type_id = $catelogyID");
            }
        })->count();
    }

    /**
     * Get detail information of the teacher user.
     * @param  int $uid - the unique id of the user
     * @return an object of Teacher which contains all information of the 
     *         teacher
     */
    public function getTeacherInfo($uid)
    {
        $rowset     = $this->tableGateway->select(
            array(
                'uid'   => $uid,
            )
        );
        return $rowset->current();
    }

    /**
     * Handle asynchronous register requests for a teacher.
     * @return true if the query is successful
     */
    public function createNewTeacher($teacherInfo)
    {
        $this->tableGateway->insert($teacherInfo);
        return true;
    }
}