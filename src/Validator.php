<?php

class Validator 
{
    public $gw;
    private $errors = [];
    static $accept = [
        'name'        => [
                'limit'  => 50,
                'symbol' => '/[^-a-z0-9а-яё\'\s]+/iu',
                'symbolToText' => 'любые буквы и цифры, дефис и апостроф'
                         ],
        'surname'     => [
                'limit'  => 60,
                'symbol' => '/[^-a-z0-9а-яё\'\s]+/iu',
                'symbolToText' => 'любые буквы и цифры, дефис и апостроф'
                         ],
        'gender'      => [
                'male'   => Abiturient::GENDER_MALE,
                'female' => Abiturient::GENDER_FEMALE,
                'valueToText'  => Abiturient::GENDER_MALE . " или " 
                                . Abiturient::GENDER_FEMALE
                         ],
        'groupNumber' => [
                'limit'  => 2,
                'symbol' => '/[^0-9а-яё]+/iu',
                'symbolToText' => 'цифры и буквы русского алфавита',
                'format' => '/[а-я]{1}[0-9]{1}/iu',
                'formatToText' => 'одна буква и одна цифра'
                         ],
        'email'       => [
                'limit'  => 70,
                'format' => '/.+@.+\..+/i',
                'formatToText' => 'один или более символов (имя), затем знак @, затем один'
                                . ' или более символ (хост), затем точка (.) и зона'
                         ],
        'sumUSE'      => [
                'low'    => 0,
                'high'   => 300,
                'valueToText' => 'от 0 до 300 включительно'
                         ],
        'yearOfBirth' => [
                'min'    => 16,
                'max'    => 80,
                'valueToText' => 'возраст должен быть не моложе 16 и не страше 80 лет'
                         ],
        'location'    => [
                'local'  => Abiturient::LOCATION_LOCAL,
                'non'    => Abiturient::LOCATION_NON,
                'valueToText' => Abiturient::LOCATION_LOCAL . " или " 
                               . Abiturient::LOCATION_NON
                         ]
    ];
                        
    private static $typeError = [ 
                        0 => "Достигнут предел символов",
                        1 => "Используются недопустимые символы",
                        2 => "Используется недопустимое значение",
                        3 => "Неверный формат ввода",
                        4 => "Электронный адрес уже используется"
                         ];

    public function __construct(AbiturientDataGateway $abiturientDataGateway) {
        return $this->gw = $abiturientDataGateway;
    }


    public function validateSet(array $set) 
    {
        if (count($set) !== 8) {
            throw new Exception("Ошибка: не верный набор данных. В наборе должно
                                 восемь значений");
        }
        if ($a = $this->name($set['name'], self::$accept['name']['limit'])) {
            $this->errors['name']        = $a;
        }
        if ($b = $this->name($set['surname'], self::$accept['surname']['limit'])) {
            $this->errors['surname']     = $b;
        }
        if ($c = $this->gender($set['gender'])) {
            $this->errors['gender']      = $c;
        }
        if ($d = $this->group($set['groupNumber'], self::$accept['groupNumber']['limit'])) {
            $this->errors['groupNumber']       = $d;
        }
        if ($e = $this->email($set['email'], self::$accept['email']['limit'])) {
            $this->errors['email']       = $e;
        }
        if ($f = $this->sumUSE($set['sumUSE'])) {
            $this->errors['sumUSE']      = $f;
        }
        if ($g = $this->year($set['yearOfBirth'])) {
            $this->errors['yearOfBirth'] = $g;
        }
        if ($h = $this->location($set['location'])) {
            $this->errors['location']    = $h;
        }
        
        return $this->errors ?: 0;
    }
    
    private function name($value, $len)
    {
        $error = [];
         
        if (mb_strlen($value) > $len) {
            $error[0] = mb_strlen($value);
        }
        
        if ($a = $this->invalidSymbol($value, self::$accept['name']['symbol'])) {
            $error[1] = $a;
        }
        
        return $error;
    }
    
    private function gender($value) 
    {
        $error = [];
        
        if ($value !== self::$accept['gender']['male']) {
            if ($value !== self::$accept['gender']['female']) {
                $error[2] = $value;
                return $error;   
            }
        }
    }
    
    private function group($value, $len) 
    {
        $error = [];
        
        if (mb_strlen($value) > $len) {
            $error[0] = mb_strlen($value);
        }
        
        if ($a = $this->invalidSymbol($value, self::$accept['groupNumber']['symbol'])) {
            $error[1] = $a;
        }
        
        if ($b = $this->formatString($value, self::$accept['groupNumber']['format'])) {
            $error[3] = $b;
        }
        
        return $error;
    }
    
    private function email($value, $len) 
    {
        $error = [];
        
        if (mb_strlen($value) > $len) {
            $error[0] = mb_strlen($value);
        }
        
        if ($b = $this->formatString($value, self::$accept['email']['format'])) {
            $error[3] = $b;
        }
        
        if ($this->gw->findEmail($value)) {
            $error[4] = true;
        }
        
        return $error;
    }
    
    private function sumUSE($value) 
    {
        $error = [];
        
        if ($value > self::$accept['sumUSE']['high'] || 
                $value < self::$accept['sumUSE']['low']) {
            $error[2] = $value;
        }
              
        return $error;
    }
    
    private function year($value) 
    {
        $error = [];
        $year = date("Y");
        
        if ($value > ($year - self::$accept['yearOfBirth']['min']) || 
                $value < ($year - self::$accept['yearOfBirth']['max'])) {
            $error[2] = $value;
        }
              
        return $error;
    }

    private function location($value) 
    {
        $error = [];
        
        if ($value !== self::$accept['location']['local']) {
            if ($value !== self::$accept['location']['non']) {
                $error[2] = $value;
                return $error;   
            }
        }
    }
    
    private function invalidSymbol($field, $regexp) 
    {
        $str = ''; 
        
        if (preg_match_all($regexp, 
                        $field, 
                        $matches, 
                        PREG_SET_ORDER)) {
            if ($matches) {
                foreach ($matches as $value) {
                    $str .= $value[0];
                }
                for ($i = 0; $i < mb_strlen($str); $i++) {
                    $a = mb_substr($str, $i, 1);
                    for ($j = 0; $j < mb_strlen($str); $j++) {
                    	if ($j == $i) continue;
                    	if (mb_substr($str, $j, 1) == $a) {
                    	    $str = mb_substr($str, 0, $j) . mb_substr($str, $j + 1);
                            $j--;
                    	}
                    }   
                }
            }
        }
        return $str;   
    }
    
    private function formatString($value, $regexp) 
    {       
        if (!preg_match($regexp, $value)) {
            return $value;
        }
    }
    
}

