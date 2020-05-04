<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ClientesModel;

class Registro extends Controller
{
    public function index()
    {
        $request = \Config\Services::request();
        $headers = $request->getHeaders();
        
        $clientesModel = new ClientesModel();
        $clientes = $clientesModel->findAll();

        $db = \Config\Database::connect();

        foreach ($clientes as $key => $value) {

            if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {


                if($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])){


                    

                       $query = $db->query('SELECT clientes.id, nombre, apellido, email FROM clientes');
                        $clientes = $query->getResult();

                  
                    

            
                    if (!empty($clientes)) {
                            
                        $json = array(
                                      'status' =>200 ,
                                      'total_results' =>count($clientes),
                                      'message' => $clientes
                        );

                    }else{

                        $json = array(
                                      'status' =>404 ,
                                      'total_results'=>0,
                                      'message' => 'Nenhum registro encontrado' 
                        );

                    }

                    return json_encode($json,true);  

                } else{
                        $json = array(
                                      'status' =>404 ,
                                      'message' => 'Token inválido!' 
                        );

                }   

            } else{
                $json = array(
                                      'status' =>404 ,
                                      'message' => 'Sem autorização para receber os dados!' 
                        );
            }           
        }

        return json_encode($json,true);  
        
    }

    /*============================================ 
        CRIAR UM REGISTRO
    /*============================================ */

    public function create(){

        $request = \Config\Services::request();
        $validation =  \Config\Services::validation();

        //Tomar Datos       
        $datos = array("nombre"=>$request->getVar("nombre"),
                       "apellido"=>$request->getVar("apellido"),
                       "email"=>$request->getVar("email"));

        if (!empty($datos)) {
      
            // Validar Datos
            $validation->setRules([
                'nombre' => 'required|string|max_length[255]',
                'apellido' => 'required|string|max_length[255]',
                'email' => 'required|valid_email|is_unique[clientes.email]'
            ]);
            
            $validation->withRequest($this->request)
               ->run();

            if($validation->getErrors()){
                $errors = $validation->getErrors();
                $json = array(
                    "status"=>404,
                    "detalhe"=>$errors
                );
        
                return json_encode($json,true);


            }else{

         
                $id_cliente = crypt($datos["nombre"].$datos["apellido"].$datos["email"], '$2a$07$rasmuslerdfdsldkfjdkf$');
                $llave_secreta = crypt($datos["email"].$datos["apellido"].$datos["nombre"], '$2a$07$rasmuslerdfdsldkfjdkf$');
                
                $datos = array(
                                'nombre' =>$datos["nombre"],
                                'apellido'=>$datos["apellido"],
                                'email' =>$datos["email"],
                                'id_cliente'=> str_replace('$', 'a', $id_cliente),
                                'llave_secreta'=> str_replace('$', 'o', $llave_secreta));

                $clientesModel = new ClientesModel();
                $clientesModel -> save($datos);

                $json = array(
                "status"=>200,
                "detalhe"=>"Registro gravado, pegue suas credenciais e guarde",
                "credenciales"=>array('id_cliente' => str_replace('$', 'a', $id_cliente) ,
                "llave_secreta"=> str_replace('$', 'o', $llave_secreta))
                );
        
                return json_encode($json,true);
            }

        }else{
             $json = array(
                    "status"=>404,
                    "detalhe"=>"Registros com erros!"
                );
        
                return json_encode($json,true);
        }
    }

}