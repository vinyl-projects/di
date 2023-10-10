<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithArguments;

class ClassA
{
    public int $a;
    public ?int $b;
    public float $c;
    public ?float $d;
    public bool $e;
    public ?bool $f;
    public string $g;
    public ?string $h;
    public object $i;
    public ?object $j;
    public array $k;
    public ?array $l;
    public array $m;
    public ?array $n;
    public ClassB $o;
    public ?ClassB $p;
    public int $aa;
    public ?int $bb;
    public float $cc;
    public ?float $dd;
    public float $ddd;
    public bool $ee;
    public ?bool $ff;
    public string $gg;
    public ?string $hh;
    public ?object $jj;
    public array $kk;
    public ?array $ll;
    public ?ClassB $pp;
    public $q;
    public ?ClassB $ppp;
    public array $variadic;

    public $noType;

    public function __construct(
        int $a,
        ?int $b,
        float $c,
        ?float $d,
        bool $e,
        ?bool $f,
        string $g,
        ?string $h,
        object $i,
        ?object $j,
        array $k,
        ?array $l,
        array $m,
        ?array $n,
        ClassB $o,
        ?ClassB $p,
        ?ClassB $ppp,
        $q,
        int $aa = 42,
        ?int $bb = null,
        float $cc = 42.5,
        ?float $dd = null,
        float $ddd = 42,
        bool $ee = true,
        ?bool $ff = null,
        string $gg = 'hello world',
        ?string $hh = null,
        ?object $jj = null,
        array $kk = [1, 2, 3],
        ?array $ll = null,
        ?ClassB $pp = null,
        $noType = 1,
        ...$variadic
    ) {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
        $this->d = $d;
        $this->e = $e;
        $this->f = $f;
        $this->g = $g;
        $this->h = $h;
        $this->i = $i;
        $this->j = $j;
        $this->k = $k;
        $this->l = $l;
        $this->m = $m;
        $this->n = $n;
        $this->o = $o;
        $this->p = $p;
        $this->aa = $aa;
        $this->bb = $bb;
        $this->cc = $cc;
        $this->dd = $dd;
        $this->ddd = $ddd;
        $this->ee = $ee;
        $this->ff = $ff;
        $this->gg = $gg;
        $this->hh = $hh;
        $this->jj = $jj;
        $this->kk = $kk;
        $this->ll = $ll;
        $this->pp = $pp;
        $this->q = $q;
        $this->ppp = $ppp;
        $this->noType = $noType;
        $this->variadic = $variadic;
    }
}
