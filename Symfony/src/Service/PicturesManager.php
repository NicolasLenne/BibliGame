<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
/**
 * Manage add and delete of pictures in BDD and folders
 */
class PicturesManager
{

    private $params;
    private $filesystem;
    private $slugger;

    public function __construct(ParameterBagInterface $params, Filesystem $filesystem, SluggerInterface $slugger)
    {
        $this->params = $params;
        $this->filesystem = $filesystem;
        $this->slugger = $slugger;
    }
    
    /**
     * Check and delete an image file from BDD and the physical folder for an entity given and this property
     *
     * @param object $entity The entity to work
     * @param string $propertyNamePicture Name property of the entity containing the image
     * @param string $directory The name of directory contained in the parameter in service.yaml or put directly your directory
     * @return void
     */
    public function delete($entity, $propertyNamePicture, $directory){

        // Set a variable method to call with $propertyNamePicture
        $getImage = "get".$propertyNamePicture;
        $setImage = "set".$propertyNamePicture;

        // We get the path with filesystem method and the variable method
        $path = $this->params->get($directory).'/'.$entity->$getImage();

        // If file exist we performs the actions and return the object
        $this->filesystem->remove($path);

        $entity->$setImage(null);

        return $entity;
    }

    /**
     * Add an image for an entity given and this property
     *
     * @param object $entity The entity to work
     * @param string $propertyNamePicture Name property of the entity containing the image
     * @param object $imageFile the image retrieved via the form
     * @param string $directory The name of directory contained in the parameter in service.yaml or put directly your directory
     * @return void
     */
    public function add($entity, $propertyNamePicture, $imageFile, $directory){
        // Set a variable method to call with $propertyNamePicture
        $getImage = "get".$propertyNamePicture;
        $setImage = "set".$propertyNamePicture;

        // Check if it's an image replacement 
        if($entity->getId() !== null && $entity->$getImage() !== null){
            $this->delete($entity, $propertyNamePicture, $directory);
        }
        
        // Rename of file and send it if no error detected while the move
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
        try {
            $imageFile->move(
                $this->params->get($directory),
                $newFilename
            );
        } catch (FileException $e) {
            return false;
        }

        $entity->$setImage($newFilename);

        return $entity;
    }

}