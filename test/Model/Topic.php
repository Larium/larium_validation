<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

use Larium\Validations\ValidatableInterface;
use Larium\Validations\Validate;

class Topic implements ValidatableInterface
{
    use Validate;

    public $title;

    public $author_name;

    public $content;

    public $approved;

    public $created_at;
}
