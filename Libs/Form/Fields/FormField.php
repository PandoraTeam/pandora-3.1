<?php
namespace Pandora3\Form\Fields;

/**
 * Class FormField
 * @package Pandora3\Form\Fields
 */
abstract class FormField {

	/** @var mixed|null */
	public $value;

	/** @var string */
	public $name;

	/** @var array */
	public $params;

	/** @var string */
	protected $path;
	
	/**
	 * FormField constructor
	 * @param string $name
	 * @param mixed|null $value
	 * @param array $params
	 */
	public function __construct(string $name, $value, array $params = []) {
		$this->name = $name;
		$this->value = $value;
		$this->params = $params;
		try {
			$this->path = dirname((new \ReflectionClass(static::class))->getFileName());
		} catch (\ReflectionException $ex) {
			$className = static::class;
			throw new \RuntimeException("Unable to create ReflectionClass for [$className]", E_ERROR, $ex);
		}
	}

	/**
	 * @return string
	 */
	protected function getView(): string {
		return $this->path.'/view.php';
	}

	/**
	 * @return mixed|null
	 */
	protected function getValue() {
		return $this->value;
	}

	/**
	 * @return array
	 */
	protected function getViewParams(): array {
		$htmlAttribs = $this->buildHtmlAttribs($this->getHtmlAttribs());
		$fieldHtmlAttribs = $this->buildHtmlAttribs($this->getFieldHtmlAttribs());
		return array_replace([
			'name' => $this->name,
			'wrap' => true,
			'value' => $this->getValue(),
			'htmlAttribs' => $htmlAttribs,
			'fieldHtmlAttribs' => $fieldHtmlAttribs,
		], $this->params);
	}
	
	/**
	 * @param array $attribs
	 * @return string
	 */
	protected function buildHtmlAttribs(array $attribs): string {
		$htmlAttribs = '';
		foreach ($attribs as $key => $value) {
			if (is_null($value)) {
				continue;
			}
			if (!is_bool($value)) {
				$htmlAttribs .= $key.'="'.htmlspecialchars($value).'" ';
			} else if ($value) {
				$htmlAttribs .= $key.' ';
			}
		}
		return $htmlAttribs;
	}
	
	/**
	 * @return array
	 */
	protected function getFieldHtmlAttribs(): array {
		$fieldAttribs = $this->params['fieldAttribs'] ?? [];
		$fieldClass = $fieldAttribs['class'] ?? '';
		$fieldAttribs['class'] = 'field ' . $fieldClass;
		return $fieldAttribs;
	}

	/**
	 * @return array
	 */
	protected function getHtmlAttribs(): array {
		$attribs = $this->params['attribs'] ?? [];
		return array_replace(
			$attribs, [
				'class' => $this->params['class'] ?? null,
				'id' => $this->params['id'] ?? null,
			]
		);
	}

	/**
	 * @return string
	 */
	public function render(): string {
		extract($this->getViewParams());
		ob_start();
			require($this->getView());
		return ob_get_clean();
	}

}