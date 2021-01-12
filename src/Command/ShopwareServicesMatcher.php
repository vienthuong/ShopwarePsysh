<?php declare(strict_types=1);

namespace ShopwarePsysh\Command;

use Psy\TabCompletion\Matcher\AbstractContextAwareMatcher;
use Psy\TabCompletion\Matcher\AbstractMatcher;
use Symfony\Component\DependencyInjection\Container;

class ShopwareServicesMatcher extends AbstractContextAwareMatcher
{
    /**
     * {@inheritdoc}
     */
    public function getMatches(array $tokens, array $info = [])
    {
        if (count($tokens) <= 5 || $tokens[1][1] !== '$container') {
            return [];
        }
        $input = str_replace("'", '', $tokens[5][1]);

        $object = $this->getVariable('container');

        if (!$object instanceof Container) {
            return [];
        }

        $services = $object->getServiceIds();

        return \array_filter(
            $services,
            function ($var) use ($input) {
                return AbstractMatcher::startsWith($input, $var) || (bool) strpos($var, $input);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hasMatched(array $tokens)
    {
        $token     = \array_pop($tokens);
        $prevToken = \array_pop($tokens);

        if (empty($tokens[1][1]) || $tokens[1][1] !== '$container') {
            return false;
        }

        if ($prevToken === '(' || $token === '(') {
            return true;
        }
    }
}
