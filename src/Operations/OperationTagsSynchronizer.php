<?php

declare(strict_types=1);

/*
 * This file is part of the Compotes package.
 *
 * (c) Alex "Pierstoval" Rock <pierstoval@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Operations;

use App\Repository\OperationRepository;
use App\Repository\TagRepository;
use App\Repository\TagRuleRepository;
use Doctrine\ORM\EntityManagerInterface;

class OperationTagsSynchronizer
{
    private OperationRepository $operationRepository;
    private TagRepository $tagRepository;
    private TagRuleRepository $tagRuleRepository;
    private EntityManagerInterface $em;

    public function __construct(
        OperationRepository $operationRepository,
        TagRepository $tagRepository,
        TagRuleRepository $tagRuleRepository,
        EntityManagerInterface $em
    ) {
        $this->operationRepository = $operationRepository;
        $this->tagRepository = $tagRepository;
        $this->tagRuleRepository = $tagRuleRepository;
        $this->em = $em;
    }

    public function applyRulesOnAllOperations(): int
    {
        $rules = $this->tagRuleRepository->findAll();
        $this->tagRepository->findAll(); // Trick to automate loading all tags for all operations
        $operations = $this->operationRepository->findAll();

        $numberOfApplied = 0;

        foreach ($rules as $rule) {
            foreach ($operations as $operation) {
                if ($operation->applyRule($rule)) {
                    $this->em->persist($operation);
                    $numberOfApplied++;
                }
            }
        }

        if ($numberOfApplied) {
            $this->em->flush();
        }

        return $numberOfApplied;
    }
}
