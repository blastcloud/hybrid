<?php

namespace Tests\Filters;

use Symfony\Component\HttpClient\HttpClient;
use PHPUnit\Framework\TestCase;
use BlastCloud\Hybrid\UsesHybrid;
use Symfony\Component\HttpClient\Response\MockResponse;
use PHPUnit\Framework\AssertionFailedError;
use BlastCloud\Hybrid\Expectation;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Tests\ExceptionMessageRegex;

class WithFormTest extends TestCase
{
    use UsesHybrid, ExceptionMessageRegex;

    /** @var HttpClient */
    public $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'https://webapi.app/api/']);
    }

    public function testFormParamsContains()
    {
        $this->hybrid->queueResponse(new MockResponse());

        $form = [
            'first' => 'a value',
            'second' => 'another value'
        ];

        $this->hybrid->expects($this->once())
            ->withFormField('first', 'a value');

        $this->client->request('POST', '/the-form', [
            'body' => $form
        ]);

        $this->hybrid->assertFirst(function (Expectation $expect) use ($form) {
            return $expect->withForm($form);
        });

        $this->expectException(AssertionFailedError::class);
        $this->{self::$regexMethodName}("/\bForm\b/");

        $this->hybrid->assertLast(function (Expectation $expect) {
            return $expect->withFormField('doesntexist', 'Some value');
        });
    }

    public function testFormParamsExclusive()
    {
        $this->hybrid->queueMany(new MockResponse(), 2);

        $form = [
            'first' => 'value',
            'second' => 'something else'
        ];

        $this->client->request('POST', '/aoiwoiu', [
            'body' => $form + ['third' => 'different'],
        ]);

        $this->client->request('POST', '/caowei', ['body' => $form]);

        $this->hybrid->assertNotFirst(function (Expectation $e) use ($form) {
            return $e->withForm($form, true);
        });
        $this->hybrid->assertLast(function (Expectation $e) use ($form) {
            return $e->withForm($form, true);
        });
    }

    public function testWithFormMultipart()
    {
        $this->hybrid->queueResponse(new MockResponse());

        $form = new FormDataPart([
            'first' => 'value',
            'second' => 'another',
            'test-file' => DataPart::fromPath(__DIR__. '/../helpers/text-file.txt')
                ->setName('rewrite-name.txt'),
            'test-image' => DataPart::fromPath(__DIR__.'/../helpers/blast-cloud.jpg')
                ->setName('overwrite.svg')
        ]);

        $this->client->request('POST', '/aoweiu', [
            'body' => $form->bodyToIterable(),
            'headers' => $form->getPreparedHeaders()->toArray()
        ]);

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withForm([
                'second' => 'another',
                'first' => 'value'
            ]);
        });

        $this->expectException(AssertionFailedError::class);
        $this->{self::$regexMethodName}("/\bForm\b/");

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withFormField('third', 'doesnt exist');
        });
    }
}