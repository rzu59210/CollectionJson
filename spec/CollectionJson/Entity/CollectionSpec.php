<?php

namespace spec\CollectionJson\Entity;

use CollectionJson\Entity\Item;
use CollectionJson\Entity\Link;
use CollectionJson\Entity\Query;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('CollectionJson\Entity\Collection');
        $this->shouldImplement('CollectionJson\ArrayConvertible');
        $this->shouldImplement('JsonSerializable');
    }

    function it_should_return_the_object_type()
    {
        $this->getObjectType()->shouldBeEqualTo('collection');
    }

    function it_may_be_construct_with_an_array_representation_of_the_collection()
    {
        $data = [
            'error'    => [
                'code' => "error code",
                'message' => "message code",
                'title' => "title code",
            ],
            'href' => 'http://example.com',
            'items' => [
                [
                    'data' => [
                        [
                            'name' => 'name 1',
                            'value' => 'value 1'
                        ]
                    ],
                    'href' => 'http://www.example1.com',
                ],
                [
                    'data' => [
                        [
                            'name' => 'name 2',
                            'value' => 'value 2'
                        ]
                    ],
                    'href' => 'http://www.example2.com'
                ]
            ],
            'links' => [
                [
                    'href'   => 'http://www.example1.com',
                    'rel'    => 'Rel value 1',
                    'render' => 'link'
                ],
                [
                    'href'   => 'http://www.example2.com',
                    'rel'    => 'Rel value 2',
                    'render' => 'link'
                ]
            ],
            'template' => [
                'data' => [
                    [
                        'name' => 'name 1',
                        'value' => 'value 1'
                    ]
                ]
            ]
        ];
        $collection = $this::fromArray($data);
        $collection->getHref()->shouldBeEqualTo('http://example.com');
        $collection->getError()->shouldHaveType('CollectionJson\Entity\Error');
        $collection->getTemplate()->shouldHaveType('CollectionJson\Entity\Template');
        $collection->getItemsSet()->shouldHaveCount(2);
        $collection->getLinksSet()->shouldHaveCount(2);
        $collection->toArray()->shouldBeEqualTo([
            'collection' => array_merge(['version' => '1.0'], $data)
        ]);
    }


    function it_may_be_construct_from_a_json_representation_of_the_collection()
    {
        $json = '
        {
            "collection": {
                "version": "1.0",
                "href": "http://example.org/friends/",
                "links": [
                    {
                        "rel": "feed",
                        "href": "http://example.org/friends/rss"
                    }
                ],
                "items": [
                    {
                        "href": "http://example.org/friends/jdoe",
                        "data": [
                            {
                                "name": "full-name",
                                "value": "J. Doe",
                                "prompt": "Full Name"
                            },
                            {
                                "name": "email",
                                "value": "jdoe@example.org",
                                "prompt": "Email"
                            }
                        ],
                        "links": [
                            {
                                "rel": "blog",
                                "href": "http://examples.org/blogs/jdoe",
                                "prompt": "Blog"
                            },
                            {
                                "rel": "avatar",
                                "href": "http://examples.org/images/jdoe",
                                "prompt": "Avatar",
                                "render": "image"
                            }
                        ]
                    },
                    {
                        "href": "http://example.org/friends/msmith",
                        "data": [
                            {
                                "name": "full-name",
                                "value": "M. Smith",
                                "prompt": "Full Name"
                            },
                            {
                                "name": "email",
                                "value": "msmith@example.org",
                                "prompt": "Email"
                            }
                        ],
                        "links": [
                            {
                                "rel": "blog",
                                "href": "http://examples.org/blogs/msmith",
                                "prompt": "Blog"
                            },
                            {
                                "rel": "avatar",
                                "href": "http://examples.org/images/msmith",
                                "prompt": "Avatar",
                                "render": "image"
                            }
                        ]
                    },
                    {
                        "href": "http://example.org/friends/rwilliams",
                        "data": [
                            {
                                "name": "full-name",
                                "value": "R. Williams",
                                "prompt": "Full Name"
                            },
                            {
                                "name": "email",
                                "value": "rwilliams@example.org",
                                "prompt": "Email"
                            }
                        ],
                        "links": [
                            {
                                "rel": "blog",
                                "href": "http://examples.org/blogs/rwilliams",
                                "prompt": "Blog"
                            },
                            {
                                "rel": "avatar",
                                "href": "http://examples.org/images/rwilliams",
                                "prompt": "Avatar",
                                "render": "image"
                            }
                        ]
                    }
                ],
                "queries": [
                    {
                        "rel": "search",
                        "href": "http://example.org/friends/search",
                        "prompt": "Search",
                        "data": [
                            {
                                "name": "search",
                                "value": ""
                            }
                        ]
                    }
                ],
                "template": {
                    "data": [
                        {
                            "name": "full-name",
                            "value": "",
                            "prompt": "Full Name"
                        },
                        {
                            "name": "email",
                            "value": "",
                            "prompt": "Email"
                        },
                        {
                            "name": "blog",
                            "value": "",
                            "prompt": "Blog"
                        },
                        {
                            "name": "avatar",
                            "value": "",
                            "prompt": "Avatar"
                        }
                    ]
                }
            }
        }';

        $collection = $this::fromJson($json);
        $collection->getHref()->shouldBeEqualTo('http://example.org/friends/');
        $collection->getTemplate()->shouldHaveType('CollectionJson\Entity\Template');
        $collection->getTemplate()->getDataSet()->shouldHaveCount(4);
        $collection->getItemsSet()->shouldHaveCount(3);
        $collection->getQueriesSet()->shouldHaveCount(1);
        $collection->getLinksSet()->shouldHaveCount(1);
    }

    function it_should_throw_an_exception_when_setting_the_href_field_with_an_invalid_url()
    {
        $this->shouldThrow(
            new \DomainException("Field href must be a valid URL, uri given")
        )->duringSetHref('uri');
    }

    /**
     * @param \CollectionJson\Entity\Item $item
     * @param \CollectionJson\Entity\Query $query
     * @param \CollectionJson\Entity\Error $error
     * @param \CollectionJson\Entity\Template $template
     */
    function it_should_be_chainable($item, $query, $error, $template)
    {
        $this->setHref('http://www.example.com')->shouldHaveType('CollectionJson\Entity\Collection');
        $this->addItem($item)->shouldHaveType('CollectionJson\Entity\Collection');
        $this->addItemsSet([$item])->shouldHaveType('CollectionJson\Entity\Collection');
        $this->addQuery($query)->shouldHaveType('CollectionJson\Entity\Collection');
        $this->addQueriesSet([$query])->shouldHaveType('CollectionJson\Entity\Collection');
        $this->setError($error)->shouldHaveType('CollectionJson\Entity\Collection');
        $this->setTemplate($template)->shouldHaveType('CollectionJson\Entity\Collection');
        $this->addLink([])->shouldHaveType('CollectionJson\Entity\Collection');
        $this->addLinksSet([])->shouldHaveType('CollectionJson\Entity\Collection');
    }

    function it_should_not_extract_null_and_empty_array_fields()
    {
        $this->toArray()->shouldBeEqualTo([
            'collection' => [
                'version' => '1.0'
            ]
        ]);
    }

    /**
     * @param \CollectionJson\Entity\Item $item
     */
    function it_should_add_a_item($item)
    {
        $this->addItem($item);
        $this->getItemsSet()->shouldHaveCount(1);
    }

    function it_should_add_a_item_when_passing_array()
    {
        $this->addItem([
            'href' => 'http://www.example.com'
        ]);
        $this->getItemsSet()->shouldHaveCount(1);
    }

    /**
     * @param \CollectionJson\Entity\Item $item1
     * @param \CollectionJson\Entity\Item $item2
     */
    function it_should_add_multiple_items($item1, $item2)
    {
        $this->addItemsSet([$item1, $item2]);
        $this->getItemsSet()->shouldHaveCount(2);
    }

    function it_should_return_the_first_item_in_the_set()
    {
        $item1 = new Item();
        $item2 = new Item();
        $item3 = new Item();

        $this->addItemsSet([$item1, $item2, $item3]);

        $this->getFirstItem()->shouldReturn($item1);
    }

    function it_should_return_null_when_the_first_item_in_not_the_set()
    {
        $this->getFirstItem()->shouldBeNull();
    }

    function it_should_return_the_last_item_in_the_set()
    {
        $item1 = new Item();
        $item2 = new Item();
        $item3 = new Item();

        $this->addItemsSet([$item1, $item2, $item3]);

        $this->getLastItem()->shouldReturn($item3);
    }

    function it_should_return_null_when_the_last_item_in_not_the_set()
    {
        $this->getLastItem()->shouldBeNull();
    }

    /**
     * @param \CollectionJson\Entity\Query $query
     */
    function it_should_add_a_query($query)
    {
        $this->addQuery($query);
        $this->getQueriesSet()->shouldHaveCount(1);
    }

    function it_should_add_a_query_when_passing_an_array()
    {
        $this->addQuery([
            'href'   => 'http://example.com',
            'rel'    => 'Query Rel',
            'name'   => 'Query Name',
            'prompt' => 'Query Prompt',
            'data' => [
                [
                    'name' => 'name 1',
                    'value' => 'value 1'
                ]
            ]
        ]);
        $this->getQueriesSet()->shouldHaveCount(1);
    }

    /**
     * @param \CollectionJson\Entity\Query $query1
     * @param \CollectionJson\Entity\Query $query2
     */
    function it_should_add_multiple_queries($query1, $query2)
    {
        $this->addQueriesSet([$query1, $query2]);
        $this->getQueriesSet()->shouldHaveCount(2);
    }

    function it_should_return_the_first_query_in_the_set()
    {
        $query1 = new Query();
        $query2 = new Query();
        $query3 = new Query();

        $this->addQueriesSet([$query1, $query2, $query3]);

        $this->getFirstQuery()->shouldReturn($query1);
    }

    function it_should_return_null_when_the_first_data_in_not_the_set()
    {
        $this->getFirstQuery()->shouldBeNull();
    }

    function it_should_return_the_last_data_in_the_set()
    {
        $query1 = new Query();
        $query2 = new Query();
        $query3 = new Query();

        $this->addQueriesSet([$query1, $query2, $query3]);

        $this->getLastQuery()->shouldReturn($query3);
    }

    function it_should_return_null_when_the_last_data_in_not_the_set()
    {
        $this->getLastQuery()->shouldBeNull();
    }

    /**
     * @param \CollectionJson\Entity\Link $link
     */
    function it_should_add_a_link($link)
    {
        $this->addLink($link);
        $this->getLinksSet()->shouldHaveCount(1);
    }

    function it_should_retrieve_the_link_by_relation()
    {
        $link1 = Link::fromArray(['rel' => 'rel1', 'href' => 'http://example.com']);
        $link2 = Link::fromArray(['rel' => 'rel2', 'href' => 'http://example2.com']);

        $this->addLinksSet([$link1, $link2]);

        $this->findLinkByRelation('rel1')->shouldBeEqualTo($link1);
        $this->findLinkByRelation('rel2')->shouldBeEqualTo($link2);
    }

    function it_should_return_null_when_link_is_not_in_the_set()
    {
        $this->findLinkByRelation('rel1')->shouldBeNull();
    }

    function it_should_add_a_link_when_passing_an_array()
    {
        $this->addLink([
            'href'   => 'http://example.com',
            'rel'    => 'Rel value',
            'render' => 'link'
        ]);
        $this->getLinksSet()->shouldHaveCount(1);
    }

    /**
     * @param \CollectionJson\Entity\Link $link1
     */
    function it_should_add_a_link_set($link1)
    {
        $this->addLinksSet([
            $link1,
            [
                'href'   => 'http://example.com',
                'rel'    => 'Rel value2',
                'render' => 'link'
            ],
            new \stdClass()
        ]);
        $this->getLinksSet()->shouldHaveCount(2);
    }

    function it_should_return_the_first_link_in_the_set()
    {
        $link1 = Link::fromArray(['rel' => 'rel1', 'href' => 'http://example.com']);
        $link2 = Link::fromArray(['rel' => 'rel2', 'href' => 'http://example2.com']);
        $link3 = Link::fromArray(['rel' => 'rel3', 'href' => 'http://example3.com']);

        $this->addLinksSet([$link1, $link2, $link3]);

        $this->getFirstLink()->shouldReturn($link1);
    }

    function it_should_return_null_when_the_first_link_in_not_the_set()
    {
        $this->getFirstLink()->shouldBeNull();
    }

    function it_should_return_the_last_link_in_the_set()
    {
        $link1 = Link::fromArray(['rel' => 'rel1', 'href' => 'http://example.com']);
        $link2 = Link::fromArray(['rel' => 'rel2', 'href' => 'http://example2.com']);
        $link3 = Link::fromArray(['rel' => 'rel3', 'href' => 'http://example3.com']);

        $this->addLinksSet([$link1, $link2, $link3]);

        $this->getLastLink()->shouldReturn($link3);
    }

    function it_should_return_null_when_the_last_link_in_not_the_set()
    {
        $this->getLastLink()->shouldBeNull();
    }

    /**
     * @param \CollectionJson\Entity\Error $error
     */
    function it_should_set_the_error($error)
    {
        $error->getCode()->willReturn("error code");
        $this->setError($error);
        $this->getError()->shouldBeAnInstanceOf('CollectionJson\Entity\Error');
        $this->getError()->getCode()->shouldBeEqualTo("error code");
    }

    function it_should_set_the_error_when_passing_an_array()
    {
        $this->setError([
            'message' => "message code",
            'title' => "title code",
            'code' => "error code",
        ]);
        $this->getError()->shouldBeAnInstanceOf('CollectionJson\Entity\Error');
        $this->getError()->getMessage()->shouldBeEqualTo("message code");
        $this->getError()->getTitle()->shouldBeEqualTo("title code");
        $this->getError()->getCode()->shouldBeEqualTo("error code");
    }

    /**
     * @param \CollectionJson\Entity\Template $template
     */
    function it_should_set_the_template($template)
    {
        $this->setTemplate($template);
        $this->getTemplate()->shouldBeAnInstanceOf('CollectionJson\Entity\Template');
    }

    function it_should_set_the_template_when_passing_an_array()
    {
        $this->setTemplate([
            'data' => [
                [
                    'name' => 'name 1',
                    'value' => 'value 1'
                ]
            ]
        ]);
        $this->getTemplate()->shouldBeAnInstanceOf('CollectionJson\Entity\Template');
        $this->getTemplate()->getDataSet()->shouldHaveCount(1);
    }
}
