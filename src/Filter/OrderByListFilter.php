<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class OrderByListFilter extends AbstractContextAwareFilter
{
    /**
     * Add configuration parameter
     * {@inheritdoc}
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        ?RequestStack $requestStack = null,
        LoggerInterface $logger = null,
        array $properties = null,
        NameConverterInterface $nameConverter = null,
    ) {
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);
    }

    /** {@inheritdoc} */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ) {
        if ($property !== 'orderByList') {
            return;
        }

        //$field = key($value);
        //$orderList = explode(',', $value[$field]);

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            // ->andWhere(sprintf('STRING_TO_ARRAY(%s.%s, %s")', $alias, 'type'/*$field*/, "'DOCUMENT'"))
            ->orderBy("$alias.type array_position(ARRAY['DOCUMENT'])")
        ;
        $a = $queryBuilder->getDQL();
        //dd($a);
        /*
         * select id
         * from the_table
         * where string_to_array(keyword,',') && array['php','java','html'];
         */
    }

    /** {@inheritdoc} */
    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'Find by order list array',
                ],
            ]
        ];
    }
}
