<?php

declare(strict_types=1);

namespace Frago9876543210\forms;

use Frago9876543210\forms\element\BaseElement;
use Frago9876543210\forms\element\Dropdown;
use Frago9876543210\forms\element\Input;
use Frago9876543210\forms\element\Label;
use Frago9876543210\forms\element\Slider;
use Frago9876543210\forms\element\StepSlider;
use Frago9876543210\forms\element\Toggle;
use function array_shift;

class CustomFormResponse{

	/** @phpstan-param list<BaseElement&element\BaseElementWithValue<mixed>> $elements */
	public function __construct(private array $elements){ }

	/**
	 * @template T&BaseElement&element\BaseElementWithValue<mixed>
	 * @phpstan-param class-string<T&BaseElement&element\BaseElementWithValue<mixed>> $expected
	 * @phpstan-return T&BaseElement&element\BaseElementWithValue<mixed>
	 * @throws \UnexpectedValueException
	 */
	public function get(string $expected) : BaseElement{
		$element = array_shift($this->elements);
		return match (true) {
			is_null($element) => throw new \UnexpectedValueException("There are no elements in the container"),
			$element instanceof Label => $this->get($expected), //skip labels
			!($element instanceof $expected) => throw new \UnexpectedValueException("Unexpected type of element"),
			default => $element,
		};
	}

	public function getDropdown() : Dropdown{ return $this->get(Dropdown::class); }

	public function getInput() : Input{ return $this->get(Input::class); }

	public function getSlider() : Slider{ return $this->get(Slider::class); }

	public function getStepSlider() : StepSlider{ return $this->get(StepSlider::class); }

	public function getToggle() : Toggle{ return $this->get(Toggle::class); }

	/** @phpstan-return list<mixed> */
	public function getValues() : array{
		$values = [];

		foreach($this->elements as $element){
			if($element instanceof Label){
				continue;
			}

			$values[] = match (true) {
				$element instanceof Dropdown => $element->getSelectedOption(),
				default => $element->getValue(),
			};
		}

		return $values;
	}
}