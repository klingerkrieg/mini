<?php
/**
* Tutorial CRUD
* Autor:Alan Klinger 05/06/2017
*/

#A classe devera sempre iniciar com letra maiuscula
#e tera sempre o mesmo nome do arquivo
class Livros {

	/**
	* Para acessar http://localhost/fw/livros/index
	**/
	function index(){
		#carrego o model para poder utiliza-lo
		$livros = model('LivrosModel');

		#peco todos os registros do banco desse model
		$data['livros'] = $livros->getAll();

		#construo minha pagina iniciando com o topo onde terei todos os includes de css e javascript
		#pode tambem ter o layout da pagina, ja com o menu
		view("layout/top",$data);
		#incluo o arquivo relacionado ao index, nesse caso e a listagem
		#a variavel $data tera seus valores transformado para variaveis comuns dentro da view
		#ex $data['livros'] sera $livros dentro da view/listar.php
		view("listar",$data);
		#concluo fechando a pagina
		view("layout/bottom",$data);
	}


	/**
	* Para acessar http://localhost/fw/livros/cadastro
	* Para acessar http://localhost/fw/livros/cadastro/1 se for com uma id de livro
	* Posso ou nao passar uma id que sera a id do livro a ser editado
	**/
	function cadastro($id=null){ 
		
		$livros = model('LivrosModel');
		#Caso nao seja para editar nenhum livro
		#tenho pelo menos que criar uma variavel array vazio
		#dentro de $data para ser enviada para a view (evitando problemas na view)
		$data['dados'] = array();

		#se uma id veio via parametro
		if ($id != null){
			#busco o livro referente a essa id no banco atraves do model
			$data['dados'] = $livros->get($id);
			#configuro para que a categoria que e uma tabela auxiliar
			#apareca apenas o nome da categoria na chave categoriaNome
			#esse procedimento normalmente so sera feito com tabelas auxiliares
			#pois o Rb nao as carrega automaticamente, para evitar uso desnecessario de memoria
			$data['dados']['categoriaNome'] = $data['dados']['categoria']['nome'];
		}
		
		view("layout/top",$data);
		#carrego a view referente ao cadastro
		view("cad_livro",$data);
		view("layout/bottom",$data);
	}

	/**
	* Para acessar http://localhost/fw/livros/salvar
	* Esse endereco sera usado apenas para submeter o formulario
	**/
	function salvar(){
		$livros = model('LivrosModel');
		#mando salvar, dentro desse metodo ele ira atualizar
		#caso ja esteja vindo com alguma id
		$livros->save($_POST);
		#redireciona para a pagina principal de listagem
		redirect(serverUrl() . "/livros/");
	}

	/**
	* Para acessar http://localhost/fw/livros/excluir
	* Esse endereco sera usado apenas para submeter o formulario
	**/
	function excluir(){
		$livros = model('LivrosModel');
		#passo apenas a id para ele deletar do banco
		#o registro com essa id desse model
		$livros->delete($_POST['id']);
		redirect(serverUrl() . "/livros/");
	}

}
