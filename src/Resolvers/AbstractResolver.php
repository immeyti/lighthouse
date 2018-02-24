<?php

namespace Nuwave\Lighthouse\Resolvers;

use GraphQL\Language\AST\ArgumentNode;
use GraphQL\Language\AST\DirectiveNode;

abstract class AbstractResolver
{
    /**
     * Check if object type definition has a specified directive.
     *
     * @param  mixed $node
     * @param string $name
     *
     * @return bool
     */
    protected function hasDirective($node, $name)
    {
        return collect($node->directives)
            ->reduce(function ($match, DirectiveNode $directive) use ($name) {
                return $match ?: $directive->name->value == $name ? true : false;
            }, false);
    }

    /**
     * Check if node has any of the provided directives.
     *
     * @param  mixed $node
     * @param  array $names
     * @return bool
     */
    protected function hasAnyDirective($node, array $names = [])
    {
        return collect($node->directives)
            ->reduce(function ($has, DirectiveNode $directive) use ($names) {
                return $has ?: in_array($directive->name->value, $names);
            });
    }

    /**
     * Find field directive by name.
     *
     * @param mixed  $node
     * @param string $name
     * @param mixed  $default
     *
     * @return DirectiveNode
     */
    protected function getDirective($node, $name, $default = null)
    {
        return $this->fieldDirective($node, $name, $default);
    }

    /**
     * Find field directive by name.
     *
     * @param mixed $node
     * @param string $name
     * @param mixed  $default
     *
     * @return DirectiveNode
     */
    protected function fieldDirective($node, $name, $default = null)
    {
        return collect($node->directives)
            ->first(function (DirectiveNode $directive) use ($name) {
                return $directive->name->value == $name;
            }, $default);
    }

    /**
     * Get the enum description from provided argument(s).
     *
     * @param DirectiveNode $node
     * @param string        $key
     * @param mixed         $default
     *
     * @return string|null
     */
    protected function directiveArgValue(DirectiveNode $node, $key, $default = null)
    {
        $argument = collect($node->arguments)
            ->first(function (ArgumentNode $arg) use ($key) {
                return $arg->name->value == $key;
            });

        return $argument ? $argument->value->value : $default;
    }

    /**
     * Strip description of invalid characters.
     *
     * @param  string $description
     * @return string
     */
    protected function safeDescription($description = "")
    {
        return trim(str_replace(["\n", "\t"], "", $description));
    }
}