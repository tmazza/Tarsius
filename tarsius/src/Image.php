<?php
/**
 * @author Tiago Mazzarollo <tmazza@email.com>
 */

namespace Tarsius;

abstract class Image 
{
    private $image;

    abstract public function load(string $imageName);
    
}