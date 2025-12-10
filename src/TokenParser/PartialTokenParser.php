<?php

declare(strict_types=1);

namespace TwigPartials\TokenParser;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use TwigPartials\Node\PartialNode;

class PartialTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): PartialNode
    {
        $stream = $this->parser->getStream();
        $name = $stream->expect(Token::NAME_TYPE)->getValue();
        $stream->expect(Token::BLOCK_END_TYPE);

        return new PartialNode($name, $token->getLine());
    }

    public function getTag(): string
    {
        return 'partial';
    }
}
