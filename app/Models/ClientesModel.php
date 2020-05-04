<?php namespace App\Models;

use CodeIgniter\Model;

class ClientesModel extends Model
{
	protected $useTimestamps = true;
    protected $table = 'clientes';
    protected $allowedFields = ['nombre','apellido','email','id_cliente','llave_secreta'];
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}