<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;
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
        $builder = $this->db->table($table);
        $this->applyWhere($builder, $params);

        return $builder->countAllResults();
    }

    public function distinct_getRows($table, $col, $condition = [])
    {
        $builder = $this->db->table($table)
            ->select($col)
            ->distinct();
        $this->applyWhere($builder, $condition);

        return $builder->countAllResults();
    }

    public function show_limit_distinct($table, $col, $limit, $start, $order, $condition = [])
    {
        $id = explode(',', $col);

        $builder = $this->db->table($table)
            ->select($col)
            ->distinct();
        $this->applyWhere($builder, $condition);

        return $builder->orderBy(trim($id[0]), $order)
            ->limit($limit, $start)
            ->get()
            ->getResult();
    }

    public function update_condition($table, $data, $condition = [])
    {
        $builder = $this->db->table($table);
        $this->applyWhere($builder, $condition);

        return $builder->update($data);
    }

    public function show_limit($table, $limit, $start, $order, $condition = [])
    {
        $builder = $this->db->table($table);
        $this->applyWhere($builder, $condition);

        return $builder->orderBy('id', $order)
            ->limit($limit, $start)
            ->get()
            ->getResult();
    }

    public function show_limit_col($table, $col, $limit, $start, $order, $condition = [])
    {
        $builder = $this->db->table($table)
            ->select($col);
        $this->applyWhere($builder, $condition);

        return $builder->orderBy('id', $order)
            ->limit($limit, $start)
            ->get()
            ->getResult();
    }

    public function show_limit_col1($table, $col, $limit, $start, $order, $ocol, $condition = [])
    {
        $builder = $this->db->table($table)
            ->select($col);
        $this->applyWhere($builder, $condition);

        return $builder->orderBy($ocol, $order)
            ->limit($limit, $start)
            ->get()
            ->getResult();
    }

    public function search($table, $condition = [], $order = 'DESC')
    {
        $builder = $this->db->table($table);
        $this->applyWhere($builder, $condition);

        return $builder->orderBy('id', $order)
            ->get()
            ->getRow();
    }

    public function search_col($table, $col, $condition = [], $order = 'DESC')
    {
        $builder = $this->db->table($table)
            ->select($col);
        $this->applyWhere($builder, $condition);

        return $builder->orderBy('id', $order)
            ->get()
            ->getRow();
    }

    public function show_condition($table, $order, $condition = [])
    {
        $builder = $this->db->table($table);
        $this->applyWhere($builder, $condition);

        return $builder->orderBy('id', $order)
            ->get()
            ->getResult();
    }

    public function delete_condition($table, $condition = [])
    {
        $builder = $this->db->table($table);
        $this->applyWhere($builder, $condition);

        return $builder->delete();
    }

    public function distinct_rows($table, $col, $order, $condition = [])
    {
        $id = explode(',', $col);

        $builder = $this->db->table($table)
            ->select($col)
            ->distinct();
        $this->applyWhere($builder, $condition);

        return $builder->orderBy(trim($id[0]), $order)
            ->get()
            ->getResult();
    }

    public function select_rows($table, $col, $order, $condition = [])
    {
        $builder = $this->db->table($table)
            ->select($col);
        $this->applyWhere($builder, $condition);

        return $builder->orderBy('id', $order)
            ->get()
            ->getResult();
    }

    public function select_rows_limit($table, $col, $order, $limit, $condition = [])
    {
        $builder = $this->db->table($table)
            ->select($col)
            ->distinct();
        $this->applyWhere($builder, $condition);

        return $builder->orderBy('id', $order)
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function invoice($table, $col, $condition)
    {
        $builder = $this->db->table($table);

        $builder->selectSum($col, 'AMOUNT');

        if (!empty($condition)) {
            $this->applyWhere($builder, $condition);
        }

        $row = $builder->get()->getRow();

        return $row ? ($row->AMOUNT ?? 0) : 0;
    }

    public function wallet($condition = [])
    {
        $builder = $this->db->table('wallet')
            ->selectSum('amount', 'AMOUNT');
        $this->applyWhere($builder, $condition);

        $row = $builder->get()->getRow();

        return $row ? ($row->AMOUNT ?? 0) : 0;
    }

    public function getCustomerServiceCharge($limit, $start, $condition = [])
    {
        $builder = $this->db->table('customer_service_charge csc')
            ->select('csc.*, s.name as service_name, v.name as vendor_name')
            ->join('service s', 's.id = csc.service_id', 'left')
            ->join('vendor v', 'v.id = csc.vendor_id', 'left');
        $this->applyWhere($builder, $condition);

        return $builder->orderBy('csc.id', 'DESC')
            ->limit($limit, $start)
            ->get()
            ->getResult();
    }

    private function applyWhere(BaseBuilder $builder, $condition): void
    {
        if ($condition === [] || $condition === null || $condition === '') {
            return;
        }

        if (is_string($condition)) {
            $condition = trim($condition);
            if ($condition === '' || $condition === '1=1') {
                return;
            }

            $builder->where($condition, null, false);
            return;
        }

        $builder->where($condition);
    }

}
