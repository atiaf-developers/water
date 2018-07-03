<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use Request;
use Image;
use App\Traits\ModelTrait;

class MyModel extends Model {

    use ModelTrait;

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

}
