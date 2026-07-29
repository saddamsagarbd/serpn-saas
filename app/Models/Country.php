<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Nnjeim\World\Models\Country as BaseCountry;

class Country extends BaseCountry
{
    protected $connection = 'mysql';
}
