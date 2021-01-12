<?php declare(strict_types=1);

namespace ShopwarePsysh\Command;

use Psy\TabCompletion\Matcher\AbstractMatcher;
use Psy\TabCompletion\Matcher\ClassNamesMatcher;

class ShopwareClassesMatcher extends ClassNamesMatcher
{
    /**
     * {@inheritdoc}
     */
    public function getMatches(array $tokens, array $info = [])
    {
        $class = $this->getNamespaceAndClass($tokens);
        if (\strlen($class) > 0 && $class[0] === '\\') {
            $class = \substr($class, 1, \strlen($class));
        }
        $quotedClass = \preg_quote($class);

        return array_values(\array_map(
            function ($className) use ($class) {
                // get the number of namespace separators
                $nsPos = \substr_count($class, '\\');
                $pieces = \explode('\\', $className);
                //$methods = Mirror::get($class);
                return \implode('\\', \array_slice($pieces, $nsPos, \count($pieces)));
            },
            \array_filter(
                \get_declared_classes(),
                function ($className) use ($quotedClass) {
                    return AbstractMatcher::startsWith($quotedClass, $className) || (bool) strpos($className, $quotedClass);
                }
            )
        ));
    }
}
