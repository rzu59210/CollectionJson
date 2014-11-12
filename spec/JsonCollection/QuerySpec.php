<?php

namespace spec\JsonCollection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QuerySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('JsonCollection\Query');
        $this->shouldImplement('JsonCollection\DataAware');
        $this->shouldImplement('JsonCollection\DataInjectable');
        $this->shouldImplement('JsonCollection\ArrayConvertible');
        $this->shouldImplement('JsonSerializable');
    }

    function it_should_be_chainable()
    {
        $this->setHref('value')->shouldHaveType('JsonCollection\Query');
        $this->setRel('value')->shouldHaveType('JsonCollection\Query');
        $this->setName('value')->shouldHaveType('JsonCollection\Query');
        $this->setPrompt('value')->shouldHaveType('JsonCollection\Query');
    }

    function it_should_inject_data()
    {
        $data = [
            'href'   => 'Query Href',
            'rel'    => 'Query Rel',
            'name'   => 'Query Name',
            'prompt' => 'Query Prompt'
        ];
        $this->inject($data);
        $this->getHref()->shouldBeEqualTo('Query Href');
        $this->getRel()->shouldBeEqualTo('Query Rel');
        $this->getName()->shouldBeEqualTo('Query Name');
        $this->getPrompt()->shouldBeEqualTo('Query Prompt');
    }

    function it_should_not_set_the_href_field_if_it_is_not_a_string()
    {
        $this->setHref(true);
        $this->getHref()->shouldBeNull();
    }

    function it_should_not_set_the_rel_field_if_it_is_not_a_string()
    {
        $this->setRel(true);
        $this->getRel()->shouldBeNull();
    }

    function it_should_not_set_the_name_field_if_it_is_not_a_string()
    {
        $this->setName(true);
        $this->getName()->shouldBeNull();
    }

    function it_should_not_set_the_prompt_field_if_it_is_not_a_string()
    {
        $this->setPrompt(true);
        $this->getPrompt()->shouldBeNull();
    }

    function it_should_extract_an_empty_array_when_the_href_field_is_null()
    {
        $this->setRel('Rel value');
        $this->toArray()->shouldBeEqualTo([]);
    }

    function it_should_extract_an_empty_array_when_the_rel_field_is_null()
    {
        $this->setHref('Href value');
        $this->toArray()->shouldBeEqualTo([]);
    }

    function it_should_not_extract_null_and_empty_array_fields()
    {
        $this->setRel('Rel value');
        $this->setHref('Href value');
        $this->toArray()->shouldBeEqualTo([
            'href'   => 'Href value',
            'rel'    => 'Rel value',
        ]);
    }

    /**
     * @param \JsonCollection\Data $data
     */
    function it_should_add_data($data)
    {
        $this->addData($data);
        $this->countData()->shouldBeEqualTo(1);
    }

    function it_should_add_data_when_passed_as_a_array()
    {
        $this->addData(['value' => 'value 1']);
        $this->countData()->shouldBeEqualTo(1);
    }

    /**
     * @param \JsonCollection\Data $data
     */
    function it_should_add_a_data_set($data)
    {
        $this->addDataSet([$data, ['value' => 'value 2'], new \stdClass()]);
        $this->countData()->shouldBeEqualTo(2);
    }

    /**
     * @param \JsonCollection\Data $data1
     * @param \JsonCollection\Data $data2
     */
    function it_should_extract_the_data_set($data1, $data2)
    {
        $data1->toArray()->willReturn(['value' => 'value 1']);
        $data2->toArray()->willReturn(['value' => 'value 2']);

        $this->addData($data1);
        $this->addData($data2);
        $this->setRel('Rel value');
        $this->setHref('Href value');
        $this->toArray()->shouldBeEqualTo([
            'data'   => [
                ['value' => 'value 1'],
                ['value' => 'value 2'],
            ],
            'href'   => 'Href value',
            'rel'    => 'Rel value'
        ]);
    }

    /**
     * @param \JsonCollection\Data $data1
     * @param \JsonCollection\Data $data2
     */
    function it_should_retrieve_the_data_by_name($data1, $data2)
    {
        $data1->getName()->willReturn('name1');
        $data2->getName()->willReturn('name2');

        $this->addDataSet([$data1, $data2]);

        $this->getDataByName('name1')->shouldBeEqualTo($data1);
        $this->getDataByName('name2')->shouldBeEqualTo($data2);
    }

    function it_should_return_null_when_data_is_not_the_set()
    {
        $this->getDataByName('name1')->shouldBeNull(null);
    }
}
