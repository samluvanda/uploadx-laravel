<?php

return [
    'default' => 'default',
    'profiles' => [
        'default' => [
            'disk' => 'local',
            'path' => 'uploads',
            'combine_chunks' => true,
            'validate' => [
                'file' => [
                    'required',
                    'file',
                ],
                'chunk' => [
                    'required_with:chunks',
                    'integer',
                    'min:0',
                    function ($attribute, $value, $fail) {
                        $chunks = request()->input('chunks');
                        if (is_numeric($chunks) && $value >= (int) $chunks) {
                            $fail("The $attribute field must be less than chunks.");
                        }
                    },
                ],
                'chunks' => [
                    'required_with:chunk',
                    'integer',
                    'min:1',
                ],
                'name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ],
        ]
    ]
];
