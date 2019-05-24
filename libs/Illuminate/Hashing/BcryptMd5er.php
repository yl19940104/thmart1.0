<?php

namespace Illuminate\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class BcryptMd5er implements HasherContract
{
    protected $rounds = '';

    public function make($value, array $options = [])
    {
        return md5(md5($value) . $this->cost($options));
    }

    public function check($value, $hashedValue, array $options = [])
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }
        return md5(md5($value) . $this->cost($options)) == $hashedValue;
    }

    public function needsRehash($hashedValue, array $options = [])
    {

    }

    public function setRounds($rounds)
    {
        $this->rounds = (string) $rounds;
        return $this;
    }

    protected function cost(array $options = [])
    {
        return isset($options['rounds']) ? $options['rounds'] : $this->rounds;
    }
}
