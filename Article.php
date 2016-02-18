<?php

use Zend\Diactoros\ServerRequest as Request;
use Codeburner\Router\Exceptions\Http\NotFoundException;

/**
 * Class Article
 *
 * @author Alex Rohleder <contato@alexrohleder.com.br>
 */

class Article
{

    protected $articles = [
        1 => [
            1 => [
                "title" => "Hello World in PHP"
            ],
            2 => [
                "title" => "Making a API with codeburner"
            ]
        ],
        2 => [
            3 => [
                "title" => "Hello world in javascript"
            ]
        ]
    ];

    public function index(Request $request)
    {
        return $this->articles;
    }

    public function create(Request $request, array $args)
    {
        $this->articles[$args['category_id']][$args['id']] = ['title' => $args['title']];
        return ['title' => $args['title']];
    }

    public function show(Request $request, array $args)
    {
        if (!isset($this->articles[$args['category_id']][$args['id']])) {
            throw new NotFoundException;
        }

        return $this->articles[$args['category_id']][$args['id']];
    }

    public function update(Request $request, array $args)
    {
        if (!isset($this->articles[$args['category_id']][$args['id']])) {
            throw new NotFoundException;
        }

        $this->articles[$args['category_id']][$args['id']] = ['title' => $args['title']];
        return $this->articles[$args['category_id']][$args['id']];
    }

    public function destroy(Request $request, array $args)
    {
        if (!isset($this->articles[$args['category_id']][$args['id']])) {
            throw new NotFoundException;
        }
        
        $article = $this->articles[$args['category_id']][$args['id']];
        unset($this->articles[$args['category_id']][$args['id']]);
        return $article;
    }

}
