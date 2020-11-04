<?php
namespace Pila\Dashboard;

interface ApiInterface
{
    /**
     * Returns an array with all results from API call.
     */
    public function all(): array;

    /**
     * Calls the API.
     */
    public function call(): string;
}
