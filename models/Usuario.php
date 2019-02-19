<?php

class Usuario extends RBModel {
    
    
    /**
     @Varchar
     @Required
     * */
    public $nome;
    /**
     @Int
     * */
    public $idade;
    /**
     @Varchar
     * */
    public $sexo;
    /**
     @Date
     * */
    public $dataNascimento;
    /**
     @Double
     * */
    public $altura;
    /**
     @Bool
     * */
    public $ativo;
    /**
     @Datetime
     * */
    public $ultimoAcesso;
    /**
     @Money
     * */
    public $salario;
    /**
     @Int
     * */
    public $nivel;
}

