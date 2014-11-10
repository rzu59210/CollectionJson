<?php

namespace spec\JsonCollection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DataSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('JsonCollection\Data');
    }

    function it_should_inject_data()
    {
        $data = array(
            'name'     => 'Data Name',
            'prompt'   => 'Data Prompt',
            'type'     => 'Data Type',
            'value'    => 'Data Value',
            'required' => true
        );
        $this->inject($data);
        $this->getName()->shouldBeEqualTo('Data Name');
        $this->getPrompt()->shouldBeEqualTo('Data Prompt');
        $this->getType()->shouldBeEqualTo('Data Type');
        $this->getValue()->shouldBeEqualTo('Data Value');
        $this->shouldBeRequired();
    }

    function it_should_not_set_the_name_if_it_is_not_string()
    {
        $this->setName(true);
        $this->getName()->shouldBeNull();
    }

    function it_should_not_set_the_prompt_if_it_is_not_string()
    {
        $this->setPrompt(true);
        $this->getPrompt()->shouldBeNull();
    }

    function it_should_not_set_the_type_if_it_is_not_string()
    {
        $this->setType(true);
        $this->getType()->shouldBeNull();
    }

    function it_should_not_set_the_value_if_it_is_not_string()
    {
        $this->setValue(true);
        $this->getValue()->shouldBeNull();
    }

    function it_should_not_set_required_if_it_is_not_boolean()
    {
        $this->setRequired(1);
        $this->shouldNotBeRequired();
    }

    function it_should_extract_an_empty_array_when_the_name_field_is_null()
    {
        $this->setValue('Value');
        $this->toArray()->shouldBeEqualTo(array());
    }

    function it_should_not_extract_empty_array_and_null_properties_apart_from_the_value_field()
    {
        $this->setName('Name');
        $this->toArray()->shouldBeEqualTo(
            array(
                'name' => 'Name',
                'value' => null
            )
        );
    }

    /**
     * @param \JsonCollection\ListData $list
     */
    function it_should_extract_the_list($list)
    {
        $list->toArray()->willReturn(
            array(
                'multiple' => true,
                'options' => array(
                    array(
                        'value' => 'value 1'
                    ),
                    array(
                        'value' => 'value 1'
                    )
                )
            )
        );

        $this->setName('Name');
        $this->setList($list);
        $this->toArray()->shouldBeEqualTo(
            array(
                'list' => array(
                    'multiple' => true,
                    'options' => array(
                        array(
                            'value' => 'value 1'
                        ),
                        array(
                            'value' => 'value 1'
                        )
                    )
                ),
                'name' => 'Name',
                'value' => null
            )
        );
    }
}
