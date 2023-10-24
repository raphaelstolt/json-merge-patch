<?php

namespace Rs\Json\Merge\Tests;

use PHPUnit\Framework\TestCase as PHPUnit;
use Rs\Json\Merge\Patch;

class PatchTest extends PHPUnit
{
    /**
     * @test
     */
    public function shouldHaveExpectedMediaTypeDefined()
    {
        $this->assertEquals('application/merge-patch+json', Patch::MEDIA_TYPE);
    }

    /**
     * @test
     * @dataProvider patchProvider
     */
    public function applyWorksAsExpected($targetDocument, $patchDocument, $expectedPatchedDocument)
    {
        $this->assertEquals(
            \json_decode($expectedPatchedDocument),
            (new Patch())->apply(\json_decode($targetDocument), \json_decode($patchDocument))
        );
    }

    /**
     * @test
     * @dataProvider generateProvider
     */
    public function generateWorksAsExpected($sourceDocument, $targetDocument, $expectedPatch)
    {
        $this->assertEquals(
            \json_decode($expectedPatch),
            (new Patch())->generate(\json_decode($sourceDocument), \json_decode($targetDocument))
        );
    }

    /**
     * @test
     * @dataProvider mergeProvider
     */
    public function mergeWorksAsExpected($patchDocument1, $patchDocument2, $expectedMerge)
    {
        $this->assertEquals(
            \json_decode($expectedMerge),
            (new Patch())->merge(\json_decode($patchDocument1), \json_decode($patchDocument2))
        );
    }

    /**
     * @return array
     */
    public function patchProvider()
    {
        return [
            ['{"a":"foo"}', 1, 1],
            ['{"a":"b","c":{"d":"e","f":"g"}}', '{"a":"z","c":{"f":null}}', '{"a":"z","c":{"d":"e"}}'],
            ['{"title":"Goodbye!","author":{"givenName":"John","familyName":"Doe"},"tags":["example","sample"],"content":"This will be unchanged"}',
             '{"title":"Hello!","phoneNumber":"+01-123-456-7890","author":{"familyName":null},"tags":["example"]}',
             '{"title":"Hello!","author":{"givenName":"John"},"tags":["example"],"content":"This will be unchanged","phoneNumber":"+01-123-456-7890"}'],
            ['{"a":"b"}', '{"a":"c"}', '{"a":"c"}'],
            ['{"a":"b"}', '{"b":"c"}', '{"a":"b","b":"c"}'],
            ['{"a":"b"}', '{"a":null}', '{}'],
            ['{"a":"b","b":"c"}', '{"a":null}','{"b":"c"}'],
            ['{"a":["b"]}', '{"a":"c"}','{"a":"c"}'],
            ['{"a":"c"}', '{"a":["b"]} ','{"a":["b"]}'],
            ['{"a":{"b":"c"}}', '{"a":{"b":"d","c":null}}','{"a":{"b":"d"}}'],
            ['{"a":[{"b":"c"}]}', '{"a":[1]}','{"a":[1]}'],
            ['["a","b"]', '["c","d"]','["c","d"]'],
            ['{"a","b"}', '["c"]','["c"]'],
            ['{"a":"foo"}', null, null],
            ['{"a":"foo"}', 'bar', 'bar'],
            ['{"e":null}', '{"a":1}', '{"e":null,"a":1}'],
            ['[1,2]', '{"a":"b","c":null}', '{"a":"b"}'],
            ['{}', '{"a":{"bb":{"ccc":null}}}', '{"a":{"bb":{}}}'],
            ['{"a":{"b":["c","d","e"]}}', '{"a":{"c":["a","b"],"e":["a"]}}', '{"a":{"b":["c","d","e"],"c":["a","b"],"e":["a"]}}'],

        ];
    }

    /**
     * @return array
     */
    public function generateProvider()
    {
        return [
            ['{"title":"Goodbye!","author":{"givenName":"John","familyName":"Doe"},"tags":["example","sample"],"content":"This will be unchanged"}',
             '{"title":"Hello!","author":{"givenName":"John"},"tags":["example"],"content":"This will be unchanged","phoneNumber":"+01-123-456-7890"}',
             '{"title":"Hello!","phoneNumber":"+01-123-456-7890","author":{"familyName":null},"tags":["example"]}'
            ],
            ['{"a":"b","b":"c"}', '{"b":"c"}', '{"a":null}'],
            ['{"a":"b"}', '{"a":"c"}', '{"a":"c"}'],
            ['{"a":"b"}', '{"a":"b","b":"c"}', '{"b":"c"}'],
            ['{"a":"b"}', '{}', '{"a":null}'],
            ['{"a":["b"]}', '{"a":"c"}', '{"a":"c"}'],
            ['{"a":"c"}', '{"a":["b"]}', '{"a":["b"]}'],
            ['{"a":[{"b":"c"}]}', '{"a": ["1"]}', '{"a":[1]}'],
            ['["a","b"]', '["c","d"]', '["c","d"]'],
            ['["a","b"]', '["a"]', '["a"]'],
            ['["a":"b"]', '["c"]', '["c"]'],
            ['["a":"foo"]', 'null', 'null'],
            ['["a":"foo"]', 'bar', 'bar'],
            ['{"e":null}', '{"e":null,"a":1}', '{"a":1}'],
            ['{}', '{"a":{"bb":{}}}', '{a:{bb:{}}}'],
            ['{"a":"a"}', '{"a":"a"}', null],
            ['{"a":{"b":"c"}}', '{"a":{"b":"c"}}', null],
            ['[1,2,3]', '[1,2,3]', null],
            ['{"a":{"b":["c","d","e"]}}', '{"a":{"b":["c","d","e"],"c":["a","b"],"e":["a"]}}', '{"a":{"c":["a","b"],"e":["a"]}}'],
        ];
    }

    /**
     * @return array
     */
    public function mergeProvider()
    {
        return [
             ['{"a":{"b":{"c":"d"},"d":"e"}}', '{"a":{"b":{"c":"e"}}}', '{"a":{"b":{"c":"e"},"d":"e"}}'],
             ['{"a":"b"}', '{"b":"c"}', '{"a":"b","b":"c"}'],
             ['{"a":"b"}', '{"a":"c"}', '{"a":"c"}'],
             ['{"a":"b","b":"d"}', '{"a":"c"}', '{"a":"c","b":"d"}'],
             ['{"a":null}', '{"b":"c"}', '{"a":null,"b":"c"}'],
             ['{"a":null}', '{"a":"b"}', '{"a":"b"}'],
             ['{"a":"b"}', '{"a":null}', '{"a":null}'],
             ['[]', '{"a":"b"}', '{"a":"b"}'],
             ['{"a":{"b":{"c":"d"}},"d":"e"}', '{"a":{"b":"a"}}', '{"a":{"b":"a"},"d":"e"}'],
             ['{"a":"b"}', null, null],
         ];
    }
}
