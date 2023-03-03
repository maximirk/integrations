<?php

declare(strict_types=1);

namespace App\Action\Home;

use App\Action\Action;
use Psr\Http\Message\ResponseInterface as Response;

class HomeAction extends Action
{
    protected function action(): Response
    {
        $this->response->getBody()->write('Welcome!');

        return $this->response;
    }
}
