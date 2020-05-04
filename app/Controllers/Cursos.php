<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CursosModel;
use App\Models\ClientesModel;

class Cursos extends Controller

{
	/*=============================================
	=            MOSTRAR TODOS OS REGISTROS           =
	=============================================*/

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


	    			if (isset($_GET["page"])) {

	    				$cantidad = 10;
	    				$desde = ($_GET["page"]-1)*$cantidad;

	    				$query = $db->query('SELECT cursos.id, titulo, descripcion, instructor, imagen, precio, id_creador, nombre, apellido FROM cursos INNER JOIN clientes ON cursos.id_creador = clientes.id LIMIT '.$cantidad. ' OFFSET '.$desde);
	    				$cursos = $query->getResult();

	    			} else {
						$query = $db->query('SELECT cursos.id, titulo, descripcion, instructor, imagen, precio, id_creador, nombre, apellido FROM cursos INNER JOIN clientes ON cursos.id_creador = clientes.id');

						$cursos = $query->getResult();
	    			}
	    			

			
					if (!empty($cursos)) {
					       	
						$json = array(
									  'status' =>200 ,
									  'total_results' =>count($cursos),
									  'message' => $cursos
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


    	/*=============================================
	=           CRIAR NOVO REGISTRO           =
	=============================================*/

	public function create(){

        $request = \Config\Services::request();
        $validation =  \Config\Services::validation();


        $headers = $request->getHeaders();
    	
    	$clientesModel = new ClientesModel();
    	$clientes = $clientesModel->findAll();

    	foreach ($clientes as $key => $value) {

    		if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {


	    		if($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])){


			        //Tomar Datos       
			        $datos = array("titulo"=>$request->getVar("titulo"),
			                       "descripcion"=>$request->getVar("descripcion"),
			                       "instructor"=>$request->getVar("instructor"),
			                       "imagen"=>$request->getVar("imagen"),
			                       "precio"=>$request->getVar("precio")
			        );

			        if (!empty($datos)) {
			      
			            // Validar Datos
			            $validation->setRules([
			                'titulo' => 'required|string|max_length[255]is_unique[cursos.titulo]',
			                'descripcion' => 'required|string|max_length[255]is_unique[cursos.descripcion]',
			                'instructor' => 'required|string|max_length[255]',
			                'imagen' => 'required|string|max_length[255]',
			                'precio' => 'required|numeric'
			                
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
			         
			                $datos = array(
			                                'titulo' =>$datos["titulo"],
			                                'descripcion'=>$datos["descripcion"],
			                                'instructor' =>$datos["instructor"],                                
			                                'imagen' =>$datos["imagen"],	                                
			                                'precio' =>$datos["precio"],
			                                'id_creador'=>$value['id']	                                
			                );

			                $cursosModel = new CursosModel();
			                $cursosModel -> save($datos);

			                $json = array(
			                "status"=>200,
			                "detalhe"=>"Registro gravado com sucesso!"
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

			    }else{

			    	$json = array(
					  'status' =>404 ,
					  'message' => 'Token inválido!' 
					);

				}

			}else{

    			$json = array(
					'status' =>404 ,
					'message' => 'Sem autorização para gravar os dados!' 
						);
    		}

		}

		return json_encode($json,true);


	}


		/*=============================================
	=           Mostrar somente um registro          =
	=============================================*/

	public function show($id)
    {
    	$request = \Config\Services::request();
    	$headers = $request->getHeaders();
    	
    	$clientesModel = new ClientesModel();
    	$clientes = $clientesModel->findAll();

    	foreach ($clientes as $key => $value) {

    		if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {


	    		if($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])){

	    			$cursosModel = new CursosModel();
					$curso = $cursosModel->find($id);

					if (!empty($curso)) {
					       	
						$json = array(
									  'status' =>200 ,
									  'message' => $curso 
						);

					}else{

						$json = array(
									  'status' =>404 ,
						    		
						       		  'message' => 'Nenhum curso encontrado' 
						);

					}

					// return json_encode($json,true);  

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


      	/*=============================================
	=           ATUALIZAR  REGISTRO           =
	=============================================*/

	public function update($id){

        $request = \Config\Services::request();
        $validation =  \Config\Services::validation();


        $headers = $request->getHeaders();
    	
    	$clientesModel = new ClientesModel();
    	$clientes = $clientesModel->findAll();

    	foreach ($clientes as $key => $value) {

    		if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {


	    		if($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])){


			        //Tomar Datos       
			        // $datos = array("titulo"=>$request->getVar("titulo"),
			        //                "descripcion"=>$request->getVar("descripcion"),
			        //                "instructor"=>$request->getVar("instructor"),
			        //                "imagen"=>$request->getVar("imagen"),
			        //                "precio"=>$request->getVar("precio")
			        // );

			        $datos = $this->request->getRawInput();

			        if (!empty($datos)) {
			      
			            // Validar Datos
			            $validation->setRules([
			                'titulo' => 'required|string|max_length[255]',
			                'descripcion' => 'required|string|max_length[255]',
			                'instructor' => 'required|string|max_length[255]',
			                'imagen' => 'required|string|max_length[255]',
			                'precio' => 'required|numeric'
			                
			            ]);
			            
			            $validation->withRequest($this->request)->run();

			            if($validation->getErrors()){
			                $errors = $validation->getErrors();
			                $json = array(
			                    "status"=>404,
			                    "detalhe"=>$errors
			                );
			        
			                return json_encode($json,true);


			            }else{

			            		$cursosModel = new CursosModel();
								$curso = $cursosModel->find($id);
								
								

							if ($value["id"] == $curso["id_creador"]) {

								   $datos = array(
			                                'titulo' =>$datos["titulo"],
			                                'descripcion'=>$datos["descripcion"],
			                                'instructor' =>$datos["instructor"],
			                                'imagen' =>$datos["imagen"],                                
			                                'precio' =>$datos["precio"]                                
			                		);

			                		$cursosModel = new CursosModel();
			                		$cursosModel -> update($id, $datos);

			                		

				               		$json = array(
				                		"status"=>200,
				                		"detalhe"=>"Registro atualizado com sucesso!"
				                		
			               			);


			        
			                return json_encode($json,true);


							}else{

								 $json = array(
			                   	 	"status"=>404,
			                    	"detalhe"=>"Registro não encontrado!"
			               		 );
			        
			               		 return json_encode($json,true);

							}
			         
			             
			            }

			        }else{
			             $json = array(
			                    "status"=>404,
			                    "detalhe"=>"Registro com erros!"
			                );
			        
			                return json_encode($json,true);
			        }

			    }else{

			    	$json = array(
					  'status' =>404 ,
					  'message' => 'Token inválido!' 
					);

				}

			}else{

    			$json = array(
					'status' =>404 ,
					'message' => 'Sem autorização para atualizar registro!' 
						);
    		}

		}

		return json_encode($json,true);


	}


	/*=============================================
	=           APAGAR  REGISTRO           =
	=============================================*/

	public function delete($id){

		 $request = \Config\Services::request();
        $validation =  \Config\Services::validation();

        $headers = $request->getHeaders();
    	
    	$clientesModel = new ClientesModel();
    	$clientes = $clientesModel->findAll();

    	foreach ($clientes as $key => $value) {

    		if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {


	    		if($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])){

					$cursosModel = new CursosModel();
					$validar = $cursosModel->find($id);


					if (!empty($validar)) {

						if ($value["id"] == $validar["id_creador"]) {
							
							$cursosModel = new CursosModel();
	                		$cursosModel -> delete($id);

	    					$json = array(
				            	"status"=>200,
				                "detalhe"=>"Registro apagado com sucesso!"
				            );
							
							return json_encode($json,true);

						} else {

							$json = array(

						  		'status' =>404 ,
			 			 		'message' => 'Não está autorizado a apagar este curso!' 
							);
						}
    				}else{

				    	$json = array(
						  'status' =>404 ,
						  'message' => 'Registro não encontrado!' 
						);

					}

	    		}else{
		    		$json = array(
						'status' =>404 ,
						'message' => 'token inválido!' 
							);
	    		}
			}else{

				$json = array(
						'status' =>404 ,
						'message' => 'sem permissão!!!!' 
				);

			}
	    }

	    return json_encode($json,true);
	}

}