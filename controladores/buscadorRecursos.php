<?php
require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/nomina/trabajadores.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class BuscadorArchivos  
{
    protected  $basePath;
    
    public function setBasePath_folder($root, $subFolders)
    {
        $this->basePath = $_SERVER['DOCUMENT_ROOT']."/intranet/Empresa/Recursos"; 
        $extractDepartamento = explode( '_', $root);
        $this->basePath .= "/".$extractDepartamento[0];
        $root_subFolders = $extractDepartamento[0];
        if ( $subFolders != '' ) {
            $extractFolders = explode('>', $subFolders);
            $canSubFolder = sizeof( $extractFolders );
            for ($i=0; $i < $canSubFolder; $i++) { 
                if ( $i < $canSubFolder) {
                    $this->basePath .= "/".$extractFolders[$i];
                    $root_subFolders .= "/".$extractFolders[$i];
                }
            }
        }
        
        return ['fullUri' => $this->basePath, 'searchFolder' => $root_subFolders];;
    }

    public function scan($dir, $directorio)
    {
        

        $files = array();

        // Is there actually such a folder/file?

        if(file_exists($dir)){
            
            foreach(scandir($dir) as $f) {
            
                if(!$f || $f[0] == '.') {
                    continue; // Ignore hidden files
                }

                
                if(is_dir($dir . '/' . $f)) {

                    // The path is a folder
                    if ( strpos($f , "_ELIMINADO_") === false ) {
                        $files[] = array(
                            "name" => $f,
                            "type" => "folder",
                            "ext" => "folder",
                            "path" => $dir . '/' . $f,
                            "items" => $this->scan($dir . '/' . $f, $directorio) // Recursively get the contents of the folder
                        );
                    }

                }
                
                else {

                    $extension =  pathinfo("$dir/$f", PATHINFO_EXTENSION);
                    if ( !file_exists($_SERVER['DOCUMENT_ROOT']."/intranet/assets/images/png/$extension.png" )  ) {
                        $extension = 'file';
                    }          
                    $modeloTrabajador = new Trabajador;
                    
                    $verificacionAccesoRecurso = $modeloTrabajador->getRecursos($_SERVER['DOCUMENT_ROOT'].'/intranet/Empresa/Recursos/'.$directorio."/".$f, $_SESSION['nip']);          
                    // It is a file
                    
                    if ( sizeof( $verificacionAccesoRecurso ) || $_SESSION['nivel'] == "ADMINISTRADOR" ) {
                        if ( strpos( $f , "_ELIMINADO_" ) === false ) {
                            $files[] = array(
                                "name" => $f,
                                "type" => "file",
                                'ext' => $extension,
                                "path" => '/intranet/Empresa/Recursos/'.$directorio."/".$f,
                                "size" => filesize($dir . '/' . $f) // Gets the size of this file
                            );                                 
                        }
     
                    }
                     

                }
            }
        
        }
        usort($files, ['BuscadorArchivos' , "cmp"]);
        // array_multisort( $files,SORT_DESC );
        return $files;        
    }
    
    public static  function cmp($a, $b)
    {
        return strcmp($b["type"], $a["type"]);
    }





}


$fileListing =  new BuscadorArchivos;
$directorios = $fileListing->setBasePath_folder( $_GET['root'], $_GET['subFolders']);
extract( $directorios );
 echo json_encode( $fileListing->scan($fullUri, $searchFolder) );