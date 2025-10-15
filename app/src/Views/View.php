<?php

namespace App\Views;

abstract class View
{
    private array $tplVars;

    public function __construct(string $title = 'Title')
    {
        $this->tplVars = [
            'title' => $title,
        ];
    }

    public function show(): void
    {
        $this->showHeader();
        $this->showBody();
        $this->showFooter();
    }

    public function showHeader(): void
    {
        require_once __DIR__ . '/../templates/header.php';
    }

    abstract public function showBody(): void;

    public function showFooter(): void
    {
        require_once __DIR__ . '/../templates/footer.php';
    }

    public function setVar(string $varName, mixed $var): void
    {
        $this->tplVars[$varName] = $var;
    }

    public function getVar(string $varName): mixed
    {
        return $this->tplVars[$varName] ?? $varName;
    }

    public function delVar(string $varName): void
    {
        unset($this->tplVars[$varName]);
    }
}