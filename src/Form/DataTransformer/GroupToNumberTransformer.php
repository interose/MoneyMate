<?php

namespace App\Form\DataTransformer;

use App\Entity\CategoryGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class GroupToNumberTransformer implements DataTransformerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    // Entity -> ID (to show in the hidden input)
    public function transform($group): mixed
    {
        if (null === $group) {
            return '';
        }
        return $group->getId();
    }

    // ID -> Entity (when the form is submitted)
    public function reverseTransform($groupId): mixed
    {
        if (!$groupId) {
            return null;
        }

        $group = $this->entityManager->getRepository(CategoryGroup::class)->findOneBy(['id' => $groupId]);

        if (null === $group) {
            throw new TransformationFailedException(sprintf('Group "%s" does not exist!', $groupId));
        }

        return $group;
    }
}
