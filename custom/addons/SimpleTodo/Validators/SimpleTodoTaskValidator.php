<?php

namespace Addons\SimpleTodo\Validators;

use Illuminate\Support\Facades\Validator;

class SimpleTodoTaskValidator
{
    public function getValidator($input)
    {
        return Validator::make($input, [
                'start_at' => 'required',
                'title'    => 'required'
            ]
        );
    }
}