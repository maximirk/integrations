<?php

declare(strict_types=1);

namespace App\Service\ControlSystem\MoySklad\Product;

use App\Domain\Product\ProductRepositoryInterface;
use App\Service\ControlSystem\MoySklad\BaseMoySklad;
use App\Service\Product\ProductService;
use Assert\AssertionFailedException;
use Evgeek\Moysklad\Exceptions\ApiException;
use Evgeek\Moysklad\Exceptions\FormatException;
use Evgeek\Moysklad\Exceptions\GeneratorException;

/**
 * Так как товаров много, получать их долго, поэтому надо их сохранить для дальнейших задач.
 * У товаров в МойСклад есть модификации, каждая модификация имеет свой id.
 * В репозитории с товарами, для ключа, используется id товара если он без модификаций, либо id модификации.
 * В итоге получается репозиторий, где отдельно записан каждый товар, не важно модификация это или нет.
 */
class GetProductMoySklad extends BaseMoySklad
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected ProductService $productService,
        protected ProductDtoMoySklad $productDtoMoySklad,
    ) {
    }

    /**
     * @throws AssertionFailedException
     * @throws FormatException
     * @throws ApiException
     * @throws GeneratorException
     */
    public function execute(array $taskData): void
    {
        $this->startValidFrom($taskData);

        $productModifications = array();
        foreach ($this->apiClient->getModifications() as $modification) {
            $productId = basename($modification["product"]["meta"]["href"]); //id сущности product из метаданных.
            $productModifications[$productId][] = $modification; //собрать в массив все модификации товара
        }

        $products = $this->apiClient->getProducts();

        $availableProductIds = array();
        $productRepositoryName = parent::TABLE_PRODUCTS_KEY_PRODUCT_ID . ":{$this->controlSystem->getIdentifier()}";

        foreach ($products as $product) {
            $results = $this->productProcessing(
                $product,
                $productModifications,
                $productRepositoryName
            );
            $availableProductIds = $results ? array_merge($availableProductIds, $results) : $availableProductIds;
        }

        //если товар удалили из МС, то надо пометить его архивным
        $missingProducts = $this->productService->getMissingProducts($availableProductIds, $productRepositoryName);
        empty($missingProducts) ?: $this->productService->archivedProducts($missingProducts, $productRepositoryName);
    }

    /**
     * Если есть модификации, то товары создаются из них. Если нет, то создается 1 товар из данных товара.
     * @throws AssertionFailedException
     */
    private function productProcessing(
        array $product,
        array $modifications,
        string $repositoryName
    ): ?array {
        $productDtoIds = array();

        if (isset($product["variantsCount"]) && $product["variantsCount"] > 0) {
            if (!isset($modifications[$product["id"]])) {
                $this->logger->info("Для товара id - {$product["id"]}, не загрузились модификации");
                return null;
            }

            foreach ($modifications[$product["id"]] as $modification) {
                $productDto = $this->productDtoMoySklad->createFromProduct($product, $modification);
                $this->productRepository->add($productDto, $productDto->id, $repositoryName);
                $productDtoIds[] = $productDto->id;
            }
        } else {
            $productDto = $this->productDtoMoySklad->createFromProduct($product);
            $this->productRepository->add($productDto, $productDto->id, $repositoryName);
            $productDtoIds[] = $productDto->id;
        }

        return $productDtoIds;
    }
}
