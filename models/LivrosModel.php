<?php
/**
* Tutorial CRUD
* Autor:Alan Klinger 05/06/2017
*/

#A classe sempre ira iniciar com letra maiuscula
#Tera sempre apos o nome a palavra Model
class LivrosModel {

	#nome da tabela desse model
	private $tabela = "livros";

	/**
	* Metodo que retorna todos os registros desse model
	*/
	function getAll(){
		#Usando o mod do RedBeans
		$lista = Rb::findAll($this->tabela);
		//for ($i = 0; $i < sizeof($lista); $i++){

		foreach($lista as $el) {
		
			#desconverto a data do formato americano para o brasileiro
			$el->dataLancamento = dateToBr($el->dataLancamento);
			
			//Para cada registro eu aplico o fetchAs
			//Esse modo será mais custoso
			//O modo menos custoso é usando o Rb::getAll
			//Ver na documentacao do Rb
			$el->fetchAs( 'categorias' )->categoria;


		}
		return $lista;
	}

	/**
	* Passe um Array para esse metodo que ele ira salvar no banco
	*/
	function save($data){

		#categoria sera uma tabela auxiliar
		#caso nao exista a categoria que o usuario digitou, ele ira criar
		#caso ja exista ele retorna a id para ser usada
		$categoria = R::findOrCreate( 'categorias', ['nome' => $data['categoriaNome']] );


		#Defino a tabela de livros
		$livro = Rb::tbl($this->tabela);
		$livro->id 			= $data['id'];#defino os campos
		$livro->nome 		= $data['nome'];
		$livro->editora 	= $data['editora'];
		$livro->categoria 	= $categoria;//insere como chave estrangeira
		$livro->edicao 		= $data['edicao'];
		#converto a data para o formato americano que e aceito pelo RedBean
		$livro->dataLancamento = dateToEUA($data['dataLancamento']);#a data deve ser salva no formato americano YYYY-MM-DD
		return Rb::save($livro);#retorna a id
	}

	/**
	* Passe uma id e ele retornara apenas 1 registro referente a essa id
	*/
	function get($id){
		$obj = Rb::load($this->tabela,$id);
		$obj->fetchAs( 'categorias' )->categoria;
		$obj->dataLancamento = dateToBr($obj->dataLancamento);
		return $obj;
	}

	/**
	* Passe uma id e ele ira deletar esse registro do banco
	*/
	function delete($id){
		Rb::delete($this->tabela,$id);
	}

}

