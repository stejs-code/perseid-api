<?php

namespace App\modules\movies;

use App\Boot;
use App\modules\DefaultHashDocument;

class Document extends DefaultHashDocument
{
    public function __construct(Boot $application)
    {
        $this->index = "movies";
        parent::__construct($application);
    }

}