<?php
/**
* Tutorial CRUD
* Autor:Alan Klinger 05/06/2017
*/

#A classe devera sempre iniciar com letra maiuscula
#e tera sempre o mesmo nome do arquivo
class Usuarios {

	/**
	* Para acessar http://localhost/mini/usuarios/index
	**/
	function index($id = null){
		
		$usu = null;
		if ($id != null){
			$usu = Usuario::findById($id);
		}

		$usuarios = new Pagination( Usuario::class );

		render("form_min",["usuarios"=>$usuarios, "usuario"=>$usu]);
	}

	/**
	@Valid{success:'Salvo com sucesso', error:"Erro ao salvar"}
	 */
	function salvar(Usuario $usuario, ?int $idTipo){
		$usuario->save();
		redirect("Usuarios/index/");
	}

	function deletar(int $id){
		Usuario::deleteById($id);
		redirect("Usuarios/index/");
	}


}
