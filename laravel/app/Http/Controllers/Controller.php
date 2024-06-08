<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected function replaceNullWithEmptyString(&$array) {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->replaceNullWithEmptyString($value);
            } elseif (is_null($value)) {
                $value = '';
            }
        }
    }
}
