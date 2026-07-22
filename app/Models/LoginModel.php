<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $protectFields = false;

    public function login_valid($username, $password)
    {
        $row = $this->db->table($this->table)
            ->select('id')
            ->where([
                'userName' => $username,
                'password' => $password,
            ])
            ->get()
            ->getRow();

        return $row ? $row->id : false;
    }

    public function profile($id)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->get()
            ->getRow();
    }

    public function change($password)
    {
        return $this->db->table($this->table)
            ->where('id', session()->get('user_id'))
            ->update(['password' => $password]);
    }

    public function permission($type)
    {
        $row = $this->db->table('control')
            ->select('permission')
            ->where('type', $type)
            ->get()
            ->getRow();

        if (! $row || empty($row->permission)) {
            return [];
        }

        $permissions = array_map('trim', explode(',', $row->permission));
        return array_values(array_filter($permissions, fn ($value) => $value !== ''));
    }
}
