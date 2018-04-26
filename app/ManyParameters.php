<?php

// I don't have to refactor every time I see a function or method with over 4 arguments.
// If it causes some problems, or code smell
// Then refactor

// function compress($files, $fileType, $destination, $compressor, $yetAnother)
// {
//     if ($fileType == 'css') {
//         // Compress CSS
//     }

//     if ($fileType == 'javascript') {
//         if ($compressor) {
//             $compressor($files);
//         }
//     }
// }

class JavascriptDriver implements CompressionStrategy
{
    protected $files;

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function fire()
    {
        // Compressing Javascript-specific src files.
    }
}

class CSSDriver implements CompressionStrategy
{
    // Compress CSS specific src files.
}

function compress(CompressionStrategy $strategy, $destination)
{
    $compressed = $strategy->fire();
    // send it to $destination);
}

interface CompressionStrategy
{
    public function fire();
}

compress(new JavascriptDriver($files), $destination);
