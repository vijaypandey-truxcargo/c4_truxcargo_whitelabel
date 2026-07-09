<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'registration';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $protectFields = false;

    public function username()
    {
        return $this->db->table($this->table)
            ->select('id')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow()->id;
    }

    public function insertUser($data)
    {
        $this->db->table($this->table)->insert($data);
        return $this->db->insertID();
    }

    public function login_valid($username, $password)
    {
        $row = $this->db->table($this->table)
            ->select('id')
            ->where([
                'username' => $username,
                'password' => $password
            ])
            ->where('status !=', 2)
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

    public function forgot($username)
    {
        return $this->db->table($this->table)
            ->where('username', $username)
            ->countAllResults();
    }

    public function password($username)
    {
        $row = $this->db->table($this->table)
            ->select('password')
            ->where('username', $username)
            ->get()
            ->getRow();

        return $row ? $row->password : null;
    }

    public function email($username)
    {
        $row = $this->db->table($this->table)
            ->select('email')
            ->where('username', $username)
            ->get()
            ->getRow();

        return $row ? $row->email : null;
    }

    public function update_password($password, $id)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update([
                'password' => $password
            ]);
    }

    public function profile_update($post)
    {
        return $this->db->table($this->table)
            ->where('id', session()->get('login_id'))
            ->update($post);
    }

    public function wallet($id)
    {
        $row = $this->db->table('wallet')
            ->selectSum('AMOUNT')
            ->where('login_id', $id)
            ->where('status !=', 'Cancelled')
            ->get()
            ->getRow();

        return $row ? $row->AMOUNT : 0;
    }

    public function checkUser($email)
    {
        $row = $this->db->table($this->table)
            ->select('id')
            ->where('email', $email)
            ->get()
            ->getRow();

        return $row ? $row->id : false;
    }
}