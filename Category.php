<?php

use Zend\Diactoros\ServerRequest as Request;
use Codeburner\Router\Exceptions\Http\NotFoundException;

/**
 * Class Category
 *
 * @author Alex Rohleder <contato@alexrohleder.com.br>
 */

class Category
{

    protected $categories = [
        1 => [
            "name" => "php"
        ],
        2 => [
            "name" => "javascript"
        ]
    ];

    public function index(Request $request)
    {
        return $this->categories;
    }

    public function create(Request $request, array $args)
    {
        $this->categories[] = ['name' => $args['name']];
        return ['name' => $args['name']];
    }

    public function show(Request $request, array $args)
    {
        if (!isset($this->categories[$args['id']])) {
            throw new NotFoundException;
        }

        return $this->categories[$args['id']];
    }

    public function update(Request $request, array $args)
    {
        if (!isset($this->categories[$args['id']])) {
            throw new NotFoundException;
        }

        $this->categories[$args['id']]['name'] = $args['name'];
        return $this->categories[$args['id']];
    }

    public function destroy(Request $request, array $args)
    {
        if (!isset($this->categories[$args['id']])) {
            throw new NotFoundException;
        }
        
        $category = $this->categories[$args['id']];
        unset($this->categories[$args['id']]);
        return $category;
    }

}
