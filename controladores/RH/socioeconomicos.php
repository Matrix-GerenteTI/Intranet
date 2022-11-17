<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/modelos/nomina/trabajadores.php";

class Socioeconomicos {

    protected  $basePath;

    public function setBasePath_folder($root, $subFolders)
    {
        $this->basePath = $_SERVER['DOCUMENT_ROOT']."/nomina/socioeconomico"; 
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
    
    public function getEvidencias($dir , $toAccess)
    {
        $files = array();

        // Is there actually such a folder/file?

        if(file_exists($dir)){
        
            foreach(scandir($dir) as $f) {
            
                if(!$f || $f[0] == '.') {
                    continue; // Ignore hidden files
                }

                if(is_dir($dir . '/' . $f)) {
                    $modeloTrabajador = new Trabajador;
                    $trabajador = $modeloTrabajador->getDatosSocioeconomico( $f );
                    if( sizeof( $trabajador) === 0 ){
                        
                        continue;
                    }else if( $trabajador[0]['status'] == 99){
                        continue;
                    }
                    // The path is a folder

                    $files[] = array(
                        'folderEmpleado' => utf8_encode( $trabajador[0]['nombre'] ),
                        'observaciones'=> utf8_encode( $trabajador[0]['comentarios'] ),
                        'evaluador' => utf8_encode( $trabajador[0]['evaluador'] ),
                        'fechaRealizado' => utf8_encode( $trabajador[0]['fechaRealizacion'] ),
                        "name" => $f,
                        "type" => "folder",
                        "ext" => "folder",
                        "path" => $dir . '/' . $f,
                        "items" => $this->getEvidencias($dir . '/' . $f, $toAccess) // Recursively get the contents of the folder
                    );
                }
                
                else {

                    $extension =  pathinfo("$dir/$f", PATHINFO_EXTENSION);
                    if ( !file_exists($_SERVER['DOCUMENT_ROOT']."/intranet/assets/images/png/$extension.png" )  ) {
                        $extension = 'file';
                    }          
                    

                    
                    // $verificacionAccesoRecurso = $modeloTrabajador->getRecursos($_SERVER['DOCUMENT_ROOT'].'/intranet/Empresa/Recursos/'.$toAccess."/".$f, $_SESSION['nip']);          
                    // It is a file
                    // if ( sizeof( $verificacionAccesoRecurso ) || $_SESSION['nivel'] == "ADMINISTRADOR" ) {
                            $files[] = array(
                                "name" => $f,
                                "type" => "file",
                                'creacion' => date( 'd/m/Y', ( filemtime( "$dir/$f")  ) ),
                                'ext' => $extension,
                                "path" => '/nomina/socioeconomico/'.$toAccess."/".$f,
                                "size" => filesize($dir . '/' . $f) // Gets the size of this file
                            );          
                    // }
                     

                }
            }
        
        }
        // usort($files, ['BuscadorArchivos' , "cmp"]);
        // array_multisort( $files,SORT_DESC );
        return $files;    
    }

}

$fileListing =  new Socioeconomicos;
$directorios = $fileListing->setBasePath_folder( '', $_GET['folderEmpleado']);
extract( $directorios );
 echo json_encode( $fileListing->getEvidencias($fullUri, $searchFolder) );