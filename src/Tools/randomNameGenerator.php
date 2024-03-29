<?php

namespace App\Tools;

class randomNameGenerator
{

    public $output;
    /**
     * @var string[]
     */
    public array $allowedFormats;
    /**
     * @var string
     */
    public string $inputFormat;

    public function __construct($output = 'array')
    {
        $this->allowedFormats = array('array', 'json', 'associative_array');
        $this->inputFormat = 'json';
        if (!in_array($output, $this->allowedFormats)) {
            throw new Exception('Unrecognized format');
        }
        $this->output = $output;
    }

    private function getList($type)
    {
        $json = file_get_contents($type . '.' . $this->inputFormat, FILE_USE_INCLUDE_PATH);

        return json_decode($json, true);
    }

    /**
     * @return string
     */
    public function generateNames($num)
    {

        if (!is_numeric($num)) {
            throw new Exception('Not a number');
        }

        $first_names = $this->getList('first-names');
        $last_names  = $this->getList('last-names');

        $count = range(1, $num);
        $name_r = array();

        for ($count = 0; $count<=$num; $count++) {
                $random_fname_index = array_rand($first_names);
                $random_lname_index = array_rand($last_names);

                $first_name = $first_names[$random_fname_index];
                $last_name = $last_names[$random_lname_index];

            if ($this->output == 'array') {
                $name_arr[] = $first_name . ' ' . $last_name;
            } elseif ($this->output == 'associative_array' || $this->output == 'json') {
                $name_arr[] = array( 'first_name' => $first_name, 'last_name' => $last_name );
            }
        }

        if ($this->output == 'json') {
            $name_arr = json_encode($name_arr);
        }

        return $name_arr;
    }
}
