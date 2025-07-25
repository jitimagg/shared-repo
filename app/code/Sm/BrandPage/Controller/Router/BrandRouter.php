<?php
namespace Sm\BrandPage\Controller\Router;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ResponseInterface;

class BrandRouter implements RouterInterface
{
    protected $actionFactory;
    protected $response;

    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
    }

    public function match(RequestInterface $request)
    {
        $pathInfo = trim($request->getPathInfo(), '/');
        $parts = explode('/', $pathInfo);

        // ตรวจสอบว่า path เริ่มต้นด้วย 'brand' และมี brand label ตามมา
        if (count($parts) == 2 && $parts[0] == 'brand') {
            $brandLabel = $parts[1];

            // เซ็ต controller/action/params สำหรับ route
            $request->setModuleName('brand')
                    ->setControllerName('index')
                    ->setActionName('view')
                    ->setParam('brand', $brandLabel);

            // return controller action
            return $this->actionFactory->create(
                \Magento\Framework\App\Action\Forward::class,
                ['request' => $request]
            );
        }

        return false;
    }
}
