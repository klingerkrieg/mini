
<h4>Livros cadastrados</h4>

<a href="<?php print serverUrl() ?>/livros/cadastro">Cadastro</a>

<ol>
<?php
#a variavel $data['livros'] e transformada em $livros quando a view e chamada
#isso sempre acontecera quando uma view for chamada
foreach($livros as $livro){
	#comeco a imprimir os dados juntos do html

	#o primeiro sera um link que apontara para o endereco de edicao do livro
	#http://localhost/fw/livros/cadastro/{id} 
	print "<li><a href='".serverUrl()."livros/cadastro/{$livro->id}'>{$livro->nome}</a> -";
	print " {$livro->editora} - {$livro->edicao} - {$livro->dataLancamento} ";
	print " - {$livro->categoria->nome} </li>";
	#categoria e uma tabela auxiliar, nesse caso tenho que acessar a categoria e o campo que eu quero
	#poderia acessar por exemplo o campo 'id'
}
?>
</ol>
