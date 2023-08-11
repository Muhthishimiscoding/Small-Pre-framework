<?php
namespace Worth\core;

use Worth\core\middlewares\BaseMiddleware;

class Controller
{
    /**
     * Summary of middleWares
     * @var \Worth\core\middlewares\BaseMiddleware[];
     */
    protected array $middleWares = [];
    public string $action ='';
    public function render($view, $params = [])
    {
        return Application::app()->router->renderView($view, $params);
    }
    public function reigisterMiddleWare(BaseMiddleware $middleWare)
    {
        $this->middleWares[] = $middleWare;
    }

	/**
	 * Summary of middleWares
	 * @return array
	 */
	public function getMiddleWares(): array {
		return $this->middleWares;
	}
}