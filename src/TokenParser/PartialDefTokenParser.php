<?php

declare(strict_types=1);

namespace TwigPartials\TokenParser;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use TwigPartials\Node\PartialDefNode;

class PartialDefTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): PartialDefNode
    {
        $stream = $this->parser->getStream();
        $name = $stream->expect(Token::NAME_TYPE)->getValue();
        $stream->expect(Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse([$this, 'decideBlockEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new PartialDefNode($name, $body, $token->getLine());
    }

    public function decideBlockEnd(Token $token): bool
    {
        return $token->test('endpartialdef');
    }

    public function getTag(): string
    {
        return 'partialdef';
    }
}
