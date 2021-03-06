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

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\TagRule;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\DoctrineTools\AbstractFixture;

class TagRuleFixtures extends AbstractFixture implements ORMFixtureInterface
{
    protected function getEntityClass(): string
    {
        return TagRule::class;
    }

    protected function getObjects(): array
    {
        return [
            [
                'matchingPattern' => 'JUST AN EXAMPLE',
                'regex' => false,
                'tags' => [
                    $this->getTag('Musique-livres-films'),
                ],
            ],
        ];
    }

    private function getTag(string $tagName): Tag
    {
        return $this->getReference('tag-'.$tagName);
    }
}
