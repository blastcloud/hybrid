<?php

namespace tests\Filters;

use BlastCloud\Hybrid\{Expectation, UsesHybrid};
use BlastCloud\Chassis\Helpers\File;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use PHPUnit\Framework\TestCase;

class WithFileTest extends TestCase
{
    use UsesHybrid;

    const TEXT_FILE = __DIR__.'/../testFiles/test-file.txt';
    const IMG_FILE = __DIR__.'/../testFiles/blast-cloud.jpg';

    /** @var HttpClient */
    public $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->hybrid->getClient(['base_uri' => 'https://www.theplace.org/v3']);
    }

    public function testFileThrowsExceptionForNonExistentProperty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp("/\bsomething property does not exist\b/");

        $file = new File();
        $file->something = 'aoiweuowiue';
    }

    public function testMultipartEliminatesNoFile()
    {
        $this->hybrid->expects($this->never())
            ->withFile('first', File::create(['contents' => 'something']))
            ->will(new MockResponse());

        $this->client->request('POST', '/woeiw', [
            'multipart' => [
                [
                    'name' => 'first',
                    'contents' => 'value'
                ]
            ]
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

        $this->client->request('POST', '/awoeiu', [
            'multipart' => [
                [
                    'name' => 'file1',
                    'contents' => fopen(self::TEXT_FILE, 'r'),
                    'filename' => $filename
                ]
            ]
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

        $this->client->request('POST', '/awoeiu', [
            'multipart' => [
                [
                    'name' => 'avatar',
                    'contents' => fopen(self::IMG_FILE, 'r')
                ]
            ]
        ]);
    }

    public function testFileExclusive()
    {
        $this->hybrid->queueMany(new MockResponse(), 2);

        $this->client->request('POST', '/aoiwoiu', [
            'multipart' => [
                [
                    'name' => 'text',
                    'contents' => fopen(self::TEXT_FILE, 'r')
                ],
                [
                    'name' => 'avatar',
                    'contents' => fopen(self::IMG_FILE, 'r')
                ]
            ]
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

    public function testHeaderComparing()
    {
        $this->hybrid->queueResponse(new MockResponse());

        $this->client->request('POST', '/aoeiu', [
            'multipart' => [
                [
                    'name' => 'something',
                    'contents' => 'aowieuw',
                    'filename' => 'overset',
                    'headers' => [
                        'Foo' => 'Baz'
                    ]
                ]
            ]
        ]);

        $this->hybrid->assertFirst(function (Expectation $e) {
            return $e->withFile('something', File::create([
                'headers' => ['Foo' => 'Baz']
            ]));
        });
    }
}