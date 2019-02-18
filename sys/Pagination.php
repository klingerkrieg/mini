<?php

class Pagination implements Iterator  {

    private $index;
    private $elements = [];
    private $perPage = 10;
    private $actualPage = 1;
    private $count = 0;

    public function controls(){
        $html = "<div class='pagination'>";

        if ($this->actualPage > 1){
            $html .= "<a href='?_page=1'> << </a>";
            $html .= "<a href='?_page=". ($this->actualPage-1) ."'> < </a>";
        }

        $ini = 1;
        $max = ceil($this->count / $this->perPage);
        if ($max - $ini > 5){
            $max = $ini+5;
        }

        for ($i = $ini; $i <= $max; $i++){
            $cl = "";
            if ($i == $this->actualPage){
                $cl = "class='_actualPage'";
            }
            $html .= "<a $cl href='?_page=$i'> $i </a>";
        }

        if ($max > $this->actualPage){
            $html .= "<a href='?_page=". ($this->actualPage+1) ."'> > </a>";
            $html .= "<a href='?_page=$max'> >> </a>";
        }

        $html .= "</div>";

        print $html;
    }

    public function __construct($class, $sql = ""){

        if (isset($_REQUEST['_page'])){
            $this->actualPage = $_REQUEST['_page'];
        }

        $page = ($this->actualPage-1) * $this->perPage;
        $this->count = R::count(strtolower($class), $sql);
        $pagination = " ORDER BY id LIMIT $page, $this->perPage";
        $data = R::findAll(strtolower($class), $sql . $pagination );
        

        foreach($data as $dt){
            array_push($this->elements,$dt);
        }

    }

    public function current () {
        return $this->elements[$this->index];
    }

    public function key (){
        return $this->index;
    }

    public function next () {
        return $this->elements[$this->index++];
    }

    public function rewind () {
        $this->index = 0;
    }
    public function valid () {
        return $this->index < count($this->elements);
    }

}