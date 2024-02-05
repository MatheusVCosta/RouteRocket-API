<?php

namespace App\Interfaces;

use Exception;
use Illuminate\Database\Eloquent\Collection;

interface RedirectInterface
{
    /**
     * method to fetch all items
     *
     * @return Collection
     */
    public function findAll() : Collection;

    /**
     * method to create a new item
     * 
     * @param array $params
     *  
     * @return bool|Exception
     */
    public function create(array $params): bool|Exception;

    /**
     * method to update a item
     * 
     * @param string $redirectCode
     *  
     * @return bool|Exception
     */
    public function update(string $redirectCode, array $params): bool|Exception;

    /**
     * method to delete a item
     * 
     * @param string $redirectCode
     *  
     * @return bool|Exception
     */
    public function delete(string $redirectCode): bool| Exception;
}