<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\ValueObject;

use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Spaceship;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\PropertyHook;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Enum_;
use TomasVotruba\Lines\FeatureCounter\Enum\FeatureName;
use TomasVotruba\Lines\FeatureCounter\Enum\NodeClassToName;

final class FeatureCollector
{
    /**
     * @var PhpFeature[]
     */
    private array $phpFeatures = [];

    public function __construct()
    {
        $this->phpFeatures[] = new PhpFeature(
            70000,
            'Parameter types',
            function (\PhpParser\Node $node): bool {
                return $node instanceof \PhpParser\Node\Param && $node->type !== null;
            },
        );

        $this->phpFeatures[] = new PhpFeature(
            70000,
            'Return types',
            function (\PhpParser\Node $node): bool {
                return $node instanceof \PhpParser\Node\FunctionLike && $node->getReturnType() !== null;
            },
        );

        $this->phpFeatures[] = new PhpFeature(
            70400,
            'Typed properties',
            function (\PhpParser\Node $node): bool {
                return $node instanceof \PhpParser\Node\Stmt\Property && $node->type !== null;
            },
        );

        $this->phpFeatures[] = new PhpFeature(
            70000,
            'Strict declarations',
            function (\PhpParser\Node $node): bool {
                return $node instanceof Declare_;
            },
        );

        $this->phpFeatures[] = new PhpFeature(
            70000,
            'Space ship <=> operator ',
            function (\PhpParser\Node $node): bool {
                return $node instanceof Spaceship;
            },
        );

        $this->phpFeatures[] = new PhpFeature(
            70100,
            'Nullable type (?type)',
            function (\PhpParser\Node $node): bool {
                return $node instanceof \PhpParser\Node\NullableType;
            },
        );

        $this->phpFeatures[] = new PhpFeature(
            70100,
            'Void return type',
            function (\PhpParser\Node $node): bool {
                return $node instanceof \PhpParser\Node\FunctionLike && $node->getReturnType() instanceof \PhpParser\Node\Identifier && $node->getReturnType()->name === 'void';
            },
        );

        $this->phpFeatures[] = new PhpFeature(
            70200,
            'Object type',
            function (\PhpParser\Node $node): bool {
                return $node instanceof Identifier && $node->toString() === 'object';
            },
        );


        // @todo
    }

    /**
     * @var array<string, array<FeatureName::*, int>>
     */
    public array $structureCounterByPhpVersion = [
        '7.1' => [
            FeatureName::CLASS_CONSTANT_VISIBILITY => 0,
        ],
        '7.4' => [
            FeatureName::TYPED_PROPERTIES => 0,
        ],
        '8.0' => [
            FeatureName::PROPERTY_PROMOTION => 0,
            FeatureName::NAMED_ARGUMENTS => 0,
            FeatureName::UNION_TYPES => 0,
        ],
        '8.1' => [
            FeatureName::FIRST_CLASS_CALLABLES => 0,
            FeatureName::READONLY_PROPERTY => 0,
        ],
        '8.2' => [
            FeatureName::READONLY_CLASS => 0,
        ],
        '8.3' => [
            FeatureName::TYPED_CLASS_CONSTANTS => 0,
        ],
        '8.4' => [
            FeatureName::PROPERTY_HOOKS => 0,
        ],
    ];

    /**
     * @var array<string, array<class-string, int>>
     */
    public array $nodesTypesCounterByPhpVersion = [
        '7.0' => [
            \PhpParser\Node\Expr\BinaryOp\Coalesce::class => 0,
            Spaceship::class => 0,
            Declare_::class => 0,
        ],
        '7.4' => [
            ArrowFunction::class => 0,
            Coalesce::class => 0,
        ],
        '8.0' => [
            Match_::class => 0,
            NullsafePropertyFetch::class => 0,
            NullsafeMethodCall::class => 0,
            AttributeGroup::class => 0,
            Throw_::class => 0,
        ],
        '8.1' => [
            IntersectionType::class => 0,
            Enum_::class => 0,
        ],
        '8.4' => [
            PropertyHook::class => 0,
        ],
    ];

    /**
     * @return array<string, int>
     */
    public function getFeatureCountByPhpVersion(): array
    {
        $data = [];

        foreach ($this->structureCounterByPhpVersion as $phpVersion => $countByType) {
            $phpFeaturesCount = array_sum($countByType);
            $data[$phpVersion] = $phpFeaturesCount;
        }

        return $data;
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function getGroupedFeatureCountedByPhpVersion(): array
    {
        $data = $this->structureCounterByPhpVersion;

        foreach ($this->nodesTypesCounterByPhpVersion as $phpVersion => $nodesCountByNodeClass) {
            foreach ($nodesCountByNodeClass as $nodeClass => $count) {
                $description = NodeClassToName::LIST[$nodeClass];
                $data[$phpVersion][$description] = $count;
            }

            ksort($data[$phpVersion]);
        }

        ksort($data);

        return $data;
    }
}
