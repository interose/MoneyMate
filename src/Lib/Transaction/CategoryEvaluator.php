<?php

namespace App\Lib\Transaction;

use App\Entity\Category;
use App\Entity\CategoryAssignmentRule;
use App\Repository\CategoryAssignmentRuleRepository;
use Fhp\Model\StatementOfAccount\Transaction;
use Psr\Log\LoggerInterface;

class CategoryEvaluator
{
    private array $rules;

    public function __construct(
        private readonly CategoryAssignmentRuleRepository $repository,
        private readonly LoggerInterface $logger,
    ) {
        $this->loadRules();
    }

    public function evaluate(Transaction $transaction): ?Category
    {
        foreach ($this->rules as $rule) {
            if (CategoryAssignmentRule::TRANSACTION_FIELD_NAME === $rule['field']) {
                $value = $transaction->getName();
            } elseif (CategoryAssignmentRule::TRANSACTION_FIELD_DESCRIPTION === $rule['field']) {
                $value = $transaction->getDescription1();
            } else {
                $this->logger->error(sprintf('CategoryEvaluator: unknown transaction field: %d', $rule['field']));
                continue;
            }

            $comparative = $rule['comparative'] ?? '';
            if (0 === strlen($comparative)) {
                $this->logger->error('CategoryEvaluator: empty comparison value!');
                continue;
            }

            $category = $rule['category'] ?? null;
            if (null === $category) {
                $this->logger->error('CategoryEvaluator: missing category!');
                continue;
            }

            if (CategoryAssignmentRule::TYPE_SIMPLE === $rule['type']) {
                if ($comparative === $value) {
                    return $category;
                }
            } elseif (CategoryAssignmentRule::TYPE_REGEX === $rule['type']) {
                if (1 === preg_match($comparative, $value)) {
                    return $category;
                }
            } else {
                $this->logger->error(sprintf('CategoryEvaluator: unknown comparison type: %d', $rule['type']));
            }
        }

        return null;
    }

    private function loadRules(): void
    {
        $this->rules = array_map(function ($item) {
            return [
                'type' => $item->getType(),
                'comparative' => $item->getRule(),
                'field' => $item->getTransactionField(),
                'category' => $item->getCategory(),
            ];
        }, $this->repository->findAll());
    }
}
