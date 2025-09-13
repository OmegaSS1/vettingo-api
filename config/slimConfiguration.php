<?php

namespace slim;

function path($path){
    #Tipos de dir que ele vai vasculhar
    $values = ['html', 'htdocs'];

    if($_SERVER['SERVER_PORT'] != '443'){
        for($i=0; $i<count($values); $i++){
            preg_match("/$values[$i]/", $path, $matches, PREG_OFFSET_CAPTURE);
            #Caso encontre algum deles, começa o tratamento para pegar o path da pasta
            if($matches){
                #Remove de onde ele encontrou ate o final do caminho
                $path = substr($path, strpos($path, $values[$i]) + strlen($values[$i]), strlen($path));

                #Coloca o separador correto de acordo com o sistema operacional
                return str_replace(DIRECTORY_SEPARATOR, '/', $path);
            }
        }
        return "";
    }
    else return "";
}

 ?>
