<?php
namespace Pandora3\Twig;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Class RenderTokenParser
 * @package Pandora3\Plugins\Twig
 */
class RenderTokenParser extends AbstractTokenParser {

	/**
	 * {@inheritdoc}
	 */
	public function parse(Token $token) {
		$stream = $this->parser->getStream();

		$value = $this->parser->getExpressionParser()->parseExpression();
		$stream->expect(Token::BLOCK_END_TYPE);

		return new RenderNode($value, $token->getLine(), $this->getTag());
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getTag() {
		return 'render';
	}

}