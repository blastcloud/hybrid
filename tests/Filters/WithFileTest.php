<?php

namespace Tests\Filters;

use BlastCloud\Hybrid\{Expectation, UsesHybrid};
use BlastCloud\Chassis\Helpers\File;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Part\{Multipart\FormDataPart, DataPart};

class WithFileTest extends TestCase
{
    use UsesHybrid;

    const TEXT_FILE = __DIR__.'/../helpers/text-file.txt';
    const IMG_FILE = __DIR__.'/../helpers/blast-cloud.jpg';

    /** @var HttpClient */
    public $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'https://www.theplace.org/v3']);
    }

    public function testMultipartEliminatesNoFile()
    {
        $this->hybrid->expects($this->never())
            ->withFile('first', File::create(['contents' => 'something']))
            ->will(new MockResponse());

        $formData = new FormDataPart(
            [
                'first' => 'value',
                'second' => 'another',
                'third' => DataPart::fromPath('./tests/helpers/text-file.txt')
            ]
        );

        $this->client->request('POST', '/woeiw', [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable()
        ]);
    }

    public function testWithoutMultipart()
    {
        $this->hybrid->expects($this->never())
            ->withFile('first', File::create(['contents' => 'something']))
            ->will(new MockResponse());

        $this->client->request('POST', '/aoweiu', [
            'form_params' => [
                'first' => 'something'
            ]
        ]);
    }

    public function testWithFileUsingStringResourceAndFileLocation()
    {
        $this->hybrid->queueResponse(new MockResponse());
        $filename = 'spikity-spockity.txt';

        $form = new FormDataPart([
            'file1' => DataPart::fromPath(self::TEXT_FILE)
        ]);

        $this->client->request('POST', '/awoeiu', [
            'body' => $form->bodyToIterable(),
            'headers' => $form->getPreparedHeaders()->toArray()
        ]);

        // File Location
        $this->hybrid->assertLast(function (Expectation $e) {
            return $e->withFiles([
                'file1' => File::create([
                    'contents' => fopen(self::TEXT_FILE, 'r')
                ])
            ]);
        });

        // Resource
        $this->hybrid->assertFirst(function (Expectation $e) use ($filename) {
            return $e->withFile('file1', File::create([
                'contents' => fopen(self::TEXT_FILE, 'r'),
                'filename' => $filename,
                'contentType' => 'text/plain'
            ]));
        });
    }

    public function testFilesWithImageFileAndManualFileFields()
    {
        $file = new File();
        $file->contents = fopen(self::IMG_FILE, 'r');

        $this->hybrid->expects($this->once())
            ->withFiles([
                'avatar' => $file
            ])->will(new MockResponse());

        $form = new FormDataPart([
            'avatar' => DataPart::fromPath(self::IMG_FILE)
        ]);

        $this->client->request('POST', '/awoeiu', [
            'body' => $form->bodyToIterable(),
            'headers' => $form->getPreparedHeaders()->toArray()
        ]);
    }

    public function testFileExclusive()
    {
        $this->hybrid->queueMany(new MockResponse(), 2);

        $form = new FormDataPart([
           'text' => DataPart::fromPath(self::TEXT_FILE),
           'avatar' => DataPart::fromPath(self::IMG_FILE)
        ]);

        $this->client->request('POST', '/aoiwoiu', [
            'headers' => $form->getPreparedHeaders()->toArray(),
            'body' => $form->bodyToIterable()
        ]);

        $this->hybrid->assertNotFirst(function (Expectation $e) {
            return $e->withFiles([
                'text' => File::create([
                    'name' => 'text',
                    'contents' => fopen(self::TEXT_FILE, 'r')
                ])
            ], true);
        });

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withFiles([
                'text' => File::create([
                    'name' => 'text',
                    'contents' => fopen(self::TEXT_FILE, 'r')
                ])
            ]);
        });
    }
}