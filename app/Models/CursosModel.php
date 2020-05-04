<?php namespace App\Models;

use CodeIgniter\Model;

class CursosModel extends Model
{
    protected $table = 'cursos';
   
    protected $allowedFields = ['titulo','descripcion','instructor','imagen','precio','id_creador'];
     protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}