<?php

declare(strict_types=1);

namespace App\Service\ControlSystem\MoySklad;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Evgeek\Moysklad\Exceptions\ApiException;
use Evgeek\Moysklad\Exceptions\ConfigException;
use Evgeek\Moysklad\Exceptions\FormatException;
use Evgeek\Moysklad\Exceptions\GeneratorException;
use Evgeek\Moysklad\Exceptions\InputException;
use Evgeek\Moysklad\Formatters\ArrayFormat;
use Evgeek\Moysklad\Http\GuzzleSender;
use Evgeek\Moysklad\MoySklad;

class ApiMoySklad
{
    protected Moysklad $apiClient;

    /**
     * Ограничения по запросам:
     * https://dev.moysklad.ru/doc/api/remap/1.2/#mojsklad-json-api-obschie-swedeniq-ogranicheniq
     * @throws AssertionFailedException
     * @throws ConfigException
     */
    public function createClient(string $login, string $password): self
    {
        Assertion::notEmpty($login);
        Assertion::notEmpty($password);

        $this->apiClient = new MoySklad(
            credentials: [$login, $password],
            formatter: ArrayFormat::class,
            requestSender: new GuzzleSender()
        );

        return $this;
    }

    /**
     * @throws FormatException
     * @throws ApiException
     * @throws InputException
     */
    public function getStockByStore(string $storeId): array
    {
        return $this->apiClient->query()->endpoint('report')
            ->method('stock')
            ->method('bystore')
            ->method('current')
            ->param('stockType', 'quantity')
            ->param('include', 'zeroLines')
            ->filter('storeId', $storeId)
            ->send('GET');
    }

    /**
     * @throws FormatException
     * @throws GeneratorException
     * @throws ApiException
     */
    public function getModifications(int $limit = 1000, ?int $numberOfModifications = null): array
    {
        $modifications = array();

        $generator = $this->apiClient->query()->endpoint('entity')
            ->method('variant')
            ->param('limit', $limit)
            ->getGenerator();

        foreach ($generator as $modification) {
            if (!is_null($numberOfModifications) && count($modifications) >= $numberOfModifications) {
                break;
            }

            $modifications[] = $modification;
        }

        return $modifications;
    }

    /**
     * @throws FormatException
     * @throws GeneratorException
     * @throws ApiException
     */
    public function getProducts(int $limit = 1000, ?int $numberOfProducts = null): array
    {
        $products = array();

        $generator = $this->apiClient->query()->entity()
            ->product()
            ->limit($limit)
            ->getGenerator();

        foreach ($generator as $product) {
            if (!is_null($numberOfProducts) && count($products) >= $numberOfProducts) {
                break;
            }

            $products[] = $product;
        }

        return $products;
    }
}
