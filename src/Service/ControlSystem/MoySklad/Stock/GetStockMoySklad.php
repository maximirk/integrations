<?php

declare(strict_types=1);

namespace App\Service\ControlSystem\MoySklad\Stock;

use App\Domain\Product\ProductRepositoryInterface;
use App\Service\ControlSystem\MoySklad\BaseMoySklad;
use App\Service\ControlSystem\MoySklad\Product\ProductDtoMoySklad;
use Assert\AssertionFailedException;
use Evgeek\Moysklad\Exceptions\ApiException;
use Evgeek\Moysklad\Exceptions\FormatException;
use Evgeek\Moysklad\Exceptions\InputException;

/**
 * МойСклад отдает остатки быстрым методом через "Отчеты". Нюансы:
 * МойСклад отдает остатки без баркодов, а нужен массив остатков с баркодами, поэтому баркоды берутся
 * из репозитория, который записывается в GetProductMoySklad
 */
class GetStockMoySklad extends BaseMoySklad
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected ProductDtoMoySklad $productDtoMoySklad,
    ) {
    }

    /**
     * @throws AssertionFailedException
     * @throws InputException
     * @throws FormatException
     * @throws ApiException
     */
    public function execute(array $taskData): void
    {
        $this->startValidFrom($taskData);

        $productRepositoryName = parent::TABLE_PRODUCTS_KEY_PRODUCT_ID . ":{$this->controlSystem->getIdentifier()}";

        $productsInMoySklad = $this->apiClient->getStockByStore($taskData["from"]['control_system_inner_id']);

        $stockRepositoryName = parent::TABLE_STOCK_KEY_BARCODE
            . ":{$this->controlSystem->getIdentifier()}:store_{$taskData["from"]['control_system_inner_id']}";
        $this->productRepository->deleteAll($stockRepositoryName);

        foreach ($productsInMoySklad as $productInMoySklad) {
            $productInRepository = $this->productRepository->get(
                $productInMoySklad["assortmentId"],
                $productRepositoryName
            );

            if (is_null($productInRepository) || empty($productInRepository->getBarcode())) {
                continue;
            }

            $product = array(
                'id' => $productInMoySklad["assortmentId"],
                'quantity' => $productInMoySklad["quantity"],
                'barcode' => $productInRepository->getBarcode()
            );

            $productDto = $this->productDtoMoySklad->createFromStock($product);
            $this->productRepository->add($productDto, $product["barcode"], $stockRepositoryName);
        }

        $this->logger
            ->info("Загрузились остатки из Мой склад со склада id {$taskData["from"]['control_system_inner_id']}");
    }
}
