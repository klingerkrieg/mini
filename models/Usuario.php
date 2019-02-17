<?php

class Usuario extends RBModel {
    
    
    /**
     * @Varchar
     * */
    public $nome;
    /**
     * @Int
     * */
    public $idade;
    /**
     * @Date
     * */
    public $dataNascimento;
    /**
     * @Double
     * */
    public $altura;
    /**
     * @Bool
     * */
    public $ativo;
    /**
     * @Datetime
     * */
    public $ultimoAcesso;
    /**
     * @Money
     * */
    public $salario;
}

