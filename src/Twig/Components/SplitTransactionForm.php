<?php

namespace App\Twig\Components;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
class SplitTransactionForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?Transaction $transaction;

    public array $categoryGroups = [];

    public function __construct(private CategoryRepository $categoryRepository)
    {
        $this->categoryGroups = $categoryRepository->getCategoriesForDropdown();
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TransactionType::class, $this->transaction, [
            'action' => $this->transaction->getId() ? $this->generateUrl('app_transaction_update', ['id' => $this->transaction->getId()]) : '',
        ]);
    }
}