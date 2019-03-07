<?php

namespace App\Utils;

use App\Entity\Movie;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/*
 But : isoler les partie redondante propre à une thematique , ici la gestion des fichiers images
*/
class FileUploader {

    private $path;

    public function __construct($pathFile)
    {
        $this->path = $pathFile;
    }

    /*
     Comme les fonctions renommer + deplacement du fichier sont interdependantes
     je creer une fonction "glue" qui permet d'executer ces deux fonctions de facon consecutives.

     Je retourne neanmoins le nom du fichier pour qu'il puisse etre setté dans  MovieController
    */
    public function renameAndMove($file){

        $fileName = $this->generateFilename($file);

        $this->fileMove($file, $fileName);

        return $fileName;
    }

    // cette fonction genere un nom de fichier unique auquel je concatene l'extension de depart
    private function generateFilename($file){

        return $this->generateUniqueId().'.'.$file->guessExtension();
    }

    // cette fonction permet de deplacer le fichier renommé au bon endroit et en recuperant le path a partir de services.yml
    private function fileMove($file, $fileName){

        try {

            //je deplace mon fichier dans le dossier souhaité
            $file->move(
                $this->path ,
                $fileName
            );
        } catch (FileException $e) {
            dump($e);
        }
    }

    private function generateUniqueId()
    {
        return md5(uniqid());
    } 
}