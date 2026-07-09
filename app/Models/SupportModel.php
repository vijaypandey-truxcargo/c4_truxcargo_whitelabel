<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class SupportModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }
    protected $DBGroup = 'default';

    protected $returnType = 'object';

    protected $protectFields = false;

    /**
     * Find Single Column
     */
    public function show($table, $order = 'DESC')
    {
        return $this->db->table($table)
            ->orderBy('id', $order)
            ->get()
            ->getResult();
    }

    public function insert($table, $post)
    {
        $this->db->table($table)->insert($post);
        return $this->db->insertID();
    }

    public function delete($table, $id)
    {
        return $this->db->table($table)
            ->where('id', $id)
            ->delete();
    }

    public function find($table, $id)
    {
        return $this->db->table($table)
            ->where('id', $id)
            ->get()
            ->getRow();
    }

    public function find_col($table, $col, $id)
    {
        return $this->db->table($table)
            ->select($col)
            ->where('id', $id)
            ->get()
            ->getRow();
    }

    public function single($table, $order = 'ASC')
    {
        return $this->db->table($table)
            ->orderBy('id', $order)
            ->get()
            ->getRow();
    }

    public function update($table, $post, $id)
    {
        return $this->db->table($table)
            ->where('id', $id)
            ->update($post);
    }

    public function getRows($table, $params = [])
    {
        return $this->db->table($table)
            ->where($params)
            ->countAllResults();
    }

    public function distinct_getRows($table, $col, $condition = [])
    {
        return $this->db->table($table)
            ->select($col)
            ->distinct()
            ->where($condition)
            ->countAllResults();
    }

    public function show_limit_distinct($table, $col, $limit, $start, $order, $condition = [])
    {
        $id = explode(',', $col);

        return $this->db->table($table)
            ->select($col)
            ->distinct()
            ->where($condition)
            ->orderBy(trim($id[0]), $order)
            ->limit($limit, $start)
            ->get()
            ->getResult();
    }

    public function update_condition($table, $data, $condition = [])
    {
        return $this->db->table($table)
            ->where($condition)
            ->update($data);
    }

    public function show_limit($table, $limit, $start, $order, $condition = [])
    {
        return $this->db->table($table)
            ->where($condition)
            ->orderBy('id', $order)
            ->limit($limit, $start)
            ->get()
            ->getResult();
    }

    public function show_limit_col($table, $col, $limit, $start, $order, $condition = [])
    {
        return $this->db->table($table)
            ->select($col)
            ->where($condition)
            ->orderBy('id', $order)
            ->limit($limit, $start)
            ->get()
            ->getResult();
    }

    public function show_limit_col1($table, $col, $limit, $start, $order, $ocol, $condition = [])
    {
        return $this->db->table($table)
            ->select($col)
            ->where($condition)
            ->orderBy($ocol, $order)
            ->limit($limit, $start)
            ->get()
            ->getResult();
    }

    public function search($table, $condition = [], $order = 'DESC')
    {
        return $this->db->table($table)
            ->where($condition)
            ->orderBy('id', $order)
            ->get()
            ->getRow();
    }

    public function search_col($table, $col, $condition = [], $order = 'DESC')
    {
        return $this->db->table($table)
            ->select($col)
            ->where($condition)
            ->orderBy('id', $order)
            ->get()
            ->getRow();
    }

    public function show_condition($table, $order, $condition = [])
    {
        return $this->db->table($table)
            ->where($condition)
            ->orderBy('id', $order)
            ->get()
            ->getResult();
    }

    public function delete_condition($table, $condition = [])
    {
        return $this->db->table($table)
            ->where($condition)
            ->delete();
    }

    public function distinct_rows($table, $col, $order, $condition = [])
    {
        $id = explode(',', $col);

        return $this->db->table($table)
            ->select($col)
            ->distinct()
            ->where($condition)
            ->orderBy(trim($id[0]), $order)
            ->get()
            ->getResult();
    }

    public function select_rows($table, $col, $order, $condition = [])
    {
        return $this->db->table($table)
            ->select($col)
            ->where($condition)
            ->orderBy('id', $order)
            ->get()
            ->getResult();
    }

    public function select_rows_limit($table, $col, $order, $limit, $condition = [])
    {
        return $this->db->table($table)
            ->select($col)
            ->distinct()
            ->where($condition)
            ->orderBy('id', $order)
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function invoice($table, $col, $condition)
    {
        $builder = $this->db->table($table);

        $builder->selectSum($col, 'AMOUNT');

        if (!empty($condition)) {
            if (is_array($condition)) {
                $builder->where($condition);
            } else {
                $builder->where($condition, null, false);
            }
        }

        $row = $builder->get()->getRow();

        return $row ? ($row->AMOUNT ?? 0) : 0;
    }

}