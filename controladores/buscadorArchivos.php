<?php

$dir = $_SERVER['DOCUMENT_ROOT']."/intranet/Documentos/sgc";

$directorio = $_GET['directorio'];
define( 'ARCHIVO_BUSCADO', $_GET['archivo'] );
// Run the recursive function esapcio 

$dir .= "/$directorio";

$response = scan($dir,$directorio);


// This function scans the files folder recursively, and builds a large array

function scan($dir, $directorio){

	$files = array();

	// Is there actually such a folder/file?

	if(file_exists($dir)){
	
		foreach(scandir($dir) as $f) {
		
			if(!$f || $f[0] == '.') {
				continue; // Ignore hidden files
			}

			if(is_dir($dir . '/' . $f)) {

				// The path is a folder

				$files[] = array(
					"name" => $f,
					"type" => "folder",
					"path" => $dir . '/' . $f,
					"items" => scan($dir . '/' . $f, $directorio) // Recursively get the contents of the folder
				);
			}
			
			else {

                // It is a file
                
                // Convirtiendo los textos a minusculas para  que las coincidencias no distinga entre mayusculas y minusculas
                $archivoEncontrado = strtolower( $f ); 
                $archivo = strtolower( ARCHIVO_BUSCADO );
                $extension =  pathinfo("$dir/$f", PATHINFO_EXTENSION);
                if ( !file_exists($_SERVER['DOCUMENT_ROOT']."/intranet/assets/images/png/$extension.png" )  ) {
                    $extension = 'file';
                }

                if( $archivo != null && $archivo != ''){
                    if ( strpos($archivoEncontrado , $archivo) !== false ) {
                        $files[] = array(
                            "name" => $f,
                            "type" => "file",
                            'ext' => $extension,
                            "path" => '/intranet/Documentos/'.$directorio."/".$f,
                            "size" => filesize($dir . '/' . $f) // Gets the size of this file
                        );                    
                    }
                }   else{
                        $files[] = array(
                            "name" => $f,
                            "type" => "file",
                            'ext' => $extension,
                            "path" => '/intranet/Documentos/'.$directorio."/".$f,
                            "size" => filesize($dir . '/' . $f) // Gets the size of this file
                        );                               
                }
			}
		}
	
	}

	return $files;
}



// Output the directory listing as JSON

// header('Content-type: application/json');

echo json_encode(array(
	"name" => "files",
	"type" => "folder",
	"path" => $dir,
	"items" => $response
));
