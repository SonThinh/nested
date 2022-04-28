<?php

namespace App\Http\Controllers;

use Nekman\LuhnAlgorithm\Number;
use ReverseRegex\Generator\Scope;
use ReverseRegex\Lexer;
use ReverseRegex\Parser;
use ReverseRegex\Random\SimpleRandom;

class RandomNumberRegexController extends Controller
{
    /**
     * @throws \ReverseRegex\Exception
     */
    public function generateNumber($amount = null)
    {
        //if (is_null($amount)) {
        //    $amount = request('amount');
        //}
        $pattern = '([13579])[0-9]{6}[2468][13579][2468][13579]\1[2468]';
        $list = [];
        //for ($i = 0; $i < $amount; $i++) {
        $lexer = new Lexer($pattern);

        $generator = new SimpleRandom();
        $number = '';

        $parser = new Parser($lexer, new Scope(), new Scope());
        $parser->parse()->getResult()->generate($number, $generator);
        //array_push($list, $number);
        //}

        //return array_unique($list);
        return $number;
    }

    //public function test()
    //{
    //    switch (request('type')) {
    //        case CourseTypeEnum::COURSE_1:
    //            $firstNumber = 1;
    //            break;
    //        case CourseTypeEnum::COURSE_2:
    //            $firstNumber = 2;
    //            break;
    //        default:
    //            $firstNumber = 3;
    //    }
    //    $number = $this->generateUniqueCode($firstNumber, $this->generateNumber());
    //
    //    $user = User::create([
    //        'login_id'      => 'user',
    //        'password'      => 'password',
    //        'name'          => 'user',
    //        'email'         => 'user@gmail.com',
    //        'furigana_name' => 'user',
    //        'unique_code'   => $number,
    //    ]);
    //
    //    return $this->httpCreated($user, UserTransformer::class);
    //}
    //
    //public function generateUniqueCode($first, $last): string
    //{
    //    $uniqueCode = '';
    //    while (! $uniqueCode) {
    //        $uniqueCode = $first.$last;
    //        $a = (int) substr($uniqueCode, 0, -2);
    //        $b = (int) substr($uniqueCode, 1, -1);
    //        $c = (int) substr($uniqueCode, 2);
    //        $d = User::query()->where('unique_code', 'like', '%'.$a.'%')
    //                 ->orWhere('unique_code', 'like', '%'.$b.'%')
    //                 ->orWhere('unique_code', 'like', '%'.$c.'%');
    //
    //        if (strlen($b) != 12 && strlen($c) != 12 || $d->count() > 0) {
    //            $uniqueCode = '';
    //        }
    //    }
    //
    //    return $uniqueCode;
    //}
}
