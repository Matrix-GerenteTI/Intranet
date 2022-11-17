<?php


class FileManager  
{
    
    public function deleteFiles( $path )
    {
        //accediendo a la ruta del 
        if ( strpos( $path , "C:") !== false  ) { //Es una carpeta 
            $rutaDesglozada = explode( "/" , $path );
            $nSubfolders = sizeof( $rutaDesglozada );
            //Nombre del ultimo elemento de la ruta
            $carpetaAEliminar =  $rutaDesglozada[ $nSubfolders -1 ];
            //Eliminando el ultimo item de la ruta para ser sustituido por el nombre "#eliminado+Nombre"
            unset( $rutaDesglozada[ $nSubfolders - 1]);
            $pathToFile = implode("/",  $rutaDesglozada );
            $pathToFile .= "/_ELIMINADO_".$carpetaAEliminar;

            return rename( $path , $pathToFile );
        } else {
            # code...
        }
        
        $pathToFile = $_SERVER['DOCUMENT_ROOT'];
    }

}
