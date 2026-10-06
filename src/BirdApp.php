
<?php

declare(strict_types=1);

abstract class BirdApp
{
    public function __construct(
        protected string $name
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function create(): string
    {
        return "The {$this->name} app would create a new record here.";
    }

    public function read(): string
    {
        return "The {$this->name} app would read records here.";
    }

    public function update(): string
    {
        return "The {$this->name} app would update an existing record here.";
    }

    public function delete(): string
    {
        return "The {$this->name} app would delete a record here.";
    }

    public function page(): string
    {
        $appName = htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8');
        $slug = strtolower($this->name);

        return <<<HTML
            <h1>{$appName} App</h1>

            <p>Select an operation:</p>

            <ul>
                <li><a href="/{$slug}/create">Create</a></li>
                <li><a href="/{$slug}/read">Read</a></li>
                <li><a href="/{$slug}/update">Update</a></li>
                <li><a href="/{$slug}/delete">Delete</a></li>
            </ul>

            <p><a href="/">Return to home page</a></p>
        HTML;
    }
}

