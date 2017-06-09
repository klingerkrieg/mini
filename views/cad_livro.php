<!-- Lembrando que o topo da pagina esta em layout/top.php -->

<script>
/**
* Para realizar o botao de excluir irei utilizar o mesmo formulario
* Apenas mudarei a url de envio, para usar a de exclusao
*/
function excluir(){
    //A funcao serverUrl() tem o endereco completo do server, facilita a construcao de links
    document.forms[0].action = "<?php print serverUrl(); ?>livros/excluir";
    document.forms[0].submit();
}
</script>

<a href="../../livros/">Listagem</a>

<!-- Defino a url para salvar padrao, essa url esta dentro de controllers/Livros/salvar -> http://localhost/fw/livros/salvar -->
<form method="post" action="<?php print serverUrl(); ?>livros/salvar">

<!-- Escondo um campo com a id do registro -->
<!-- Utilizo a funcao _v para evitar erros com casas que nao existam nesse array -->
<!-- Isso e devido a quando eu estiver criando um novo registro, o array estara vazio -->
<input name="id" type="hidden" value="<?php print _v($dados,'id'); ?>">

Nome
    <input name="nome"      value="<?php print _v($dados,'nome'); ?>"><br/>
Editora
    <input name="editora"   value="<?php print _v($dados,'editora'); ?>"><br/>
Edição
    <input name="edicao"    value="<?php print _v($dados,'edicao'); ?>"><br/>

Categoria
    <input name="categoriaNome"    value="<?php print _v($dados,'categoriaNome'); ?>"><br/>

Data de lançamento
    <input name="dataLancamento" class='date' value="<?php print _v($dados,'dataLancamento'); ?>"><br/>

<!-- Esses botoes nao possuem nenhuma validacao nesse exemplo, mas no seu sistema real devera existir tal validacao -->
<button type="submit">Salvar</button><button type="button" onclick="excluir()">Excluir</button>
</form>
