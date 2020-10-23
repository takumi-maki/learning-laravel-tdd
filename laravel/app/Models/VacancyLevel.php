<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacancyLevel extends Model
{
    private $remainingCount;

    public function __construct(int $remainingCount)
    {
        $this->remainingCount = $remainingCount;
    }
    public function __toSting()
    {
        return $this->mark();
    }

    public function mark(): string
    {
        $marks = ['empty' => '×', 'few' => '△', 'enough' => '◎'];
        $slug = $this->slug();
        assert(isset($marks[$slug]), new \DomainException('invaild slug value.'));

        return $marks[$slug];
    }

    public function slug(): string
    {
        if($this->remainingCount === 0){
            return 'empty';
        }
        elseif($this->remainingCount < 5){
            return 'few';
        }
        else
        {
            return 'enough';
        }
    }
}