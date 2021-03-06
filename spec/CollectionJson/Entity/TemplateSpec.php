<?php

namespace spec\CollectionJson\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use CollectionJson\Entity\Data;

class TemplateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('CollectionJson\Entity\Template');
        $this->shouldImplement('CollectionJson\DataAware');
        $this->shouldImplement('CollectionJson\ArrayConvertible');
        $this->shouldImplement('JsonSerializable');
    }

    function it_should_return_the_object_type()
    {
        $this->getObjectType()->shouldBeEqualTo('template');
    }

    /**
     * @param \CollectionJson\Entity\Data $data
     */
    function it_may_be_construct_with_an_array_representation_of_the_template($data)
    {
        $data->getName()->willReturn('name 2');
        $data->getValue()->willReturn('value 2');

        $data = [
            'data' => [
                [
                    'name' => 'name 1',
                    'value' => 'value 1'
                ],
                $data
            ]
        ];
        $template = $this::fromArray($data);
        $template->getDataSet()->shouldHaveCount(2);
        $template->findDataByName('name 1')->getValue()->shouldBeEqualTo('value 1');
        $template->findDataByName('name 2')->getValue()->shouldBeEqualTo('value 2');
    }

    function it_should_be_chainable()
    {
        $this->addData([])->shouldHaveType('CollectionJson\Entity\Template');
        $this->addDataSet([])->shouldHaveType('CollectionJson\Entity\Template');
    }

    function it_should_not_return_null_values_and_empty_arrays()
    {
        $this->toArray()->shouldBeEqualTo([]);
    }

    /**
     * @param \CollectionJson\Entity\Data $data
     */
    function it_should_add_data_when_it_is_passed_as_an_object($data)
    {
        $this->addData($data);
        $this->getDataSet()->shouldHaveCount(1);
    }

    function it_should_add_data_when_it_is_passed_as_an_array()
    {
        $this->addData(['value' => 'value 1']);
        $this->getDataSet()->shouldHaveCount(1);
    }

    /**
     * @param \CollectionJson\Entity\Data $data
     */
    function it_should_add_a_data_set($data)
    {
        $this->addDataSet([$data, ['value' => 'value 2'], new \stdClass()]);
        $this->getDataSet()->shouldHaveCount(2);
    }

    /**
     * @param \CollectionJson\Entity\Data $data1
     * @param \CollectionJson\Entity\Data $data2
     */
    function it_should_return_an_array_with_the_data_list($data1, $data2)
    {
        $data1->toArray()->willReturn(['value' => 'value 1']);
        $data2->toArray()->willReturn(['value' => 'value 2']);

        $this->addData($data1);
        $this->addData($data2);
        $this->toArray()->shouldBeEqualTo([
            'data'   => [
                ['value' => 'value 1'],
                ['value' => 'value 2'],
            ]
        ]);
    }
    
    /**
     * @param \CollectionJson\Entity\Data $data
     */
    function it_should_add_an_envelope($data)
    {
        $data->toArray()->willReturn(['value' => 'value 1']);

        $this->addData($data);
        $this->wrap('template');
        $this->toArray()->shouldBeEqualTo([
            'template' => [
                'data' => [
                    ['value' => 'value 1']
                ]
            ]
        ]);
    }

    /**
     * @param \CollectionJson\Entity\Data $data1
     * @param \CollectionJson\Entity\Data $data2
     */
    function it_should_retrieve_the_data_by_name($data1, $data2)
    {
        $data1->getName()->willReturn('name1');
        $data2->getName()->willReturn('name2');

        $this->addDataSet([$data1, $data2]);

        $this->findDataByName('name1')->shouldBeEqualTo($data1);
        $this->findDataByName('name2')->shouldBeEqualTo($data2);
    }

    function it_should_return_null_when_data_is_not_in_the_set()
    {
        $this->findDataByName('name1')->shouldBeNull();
    }

    function it_should_return_the_first_data_in_the_set()
    {
        $data1 = Data::fromArray(['value' => 'value1']);
        $data2 = Data::fromArray(['value' => 'value2']);
        $data3 = Data::fromArray(['value' => 'value3']);

        $this->addDataSet([$data1, $data2, $data3]);

        $this->getFirstData()->shouldReturn($data1);
    }

    function it_should_return_null_when_the_first_data_in_not_the_set()
    {
        $this->getFirstData()->shouldBeNull();
    }

    function it_should_return_the_last_data_in_the_set()
    {
        $data1 = Data::fromArray(['value' => 'value1']);
        $data2 = Data::fromArray(['value' => 'value2']);
        $data3 = Data::fromArray(['value' => 'value3']);

        $this->addDataSet([$data1, $data2, $data3]);

        $this->getLastData()->shouldReturn($data3);
    }

    function it_should_return_null_when_the_last_data_in_not_the_set()
    {
        $this->getLastData()->shouldBeNull();
    }
}
