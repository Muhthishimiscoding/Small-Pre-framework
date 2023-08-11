<?php
namespace MuhthishimisCoding\PreFramework;

use MuhthishimisCoding\PreFramework\middlewares\BaseMiddleware;

class Controller
{
    /**
     * Summary of middleWares
     * @var \MuhthishimisCoding\PreFramework\middlewares\BaseMiddleware[];
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