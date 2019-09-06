<?php

namespace BlastCloud\Hybrid;

use BlastCloud\Chassis\Helpers\File;

/**
 * BlastCloud\Chassis\Expectation
 * @package BlastCloud\Hybrid
 *
 * @method $this withBody(string $body, bool $exclusive = false)
 * @method $this withHeader(string $key, $value)
 * @method $this withHeaders(array $values)
 * @method $this withQuery(array $query)
 * @method $this withOption(string $key, $value)
 * @method $this withOptions(array $values)
 * @method $this withFile(string $key, File $file)
 * @method $this withFiles(array $files, bool $exclusive = false)
 * @method $this withFormField(string $key, string $value)
 * @method $this withForm(array $fields, bool $exclusive = false)
 * @method $this withJson(array $json, bool $exclusive = false)
 * @method $this withProtocol(float $protocol)
 * @method $this withEndpoint(string $url, string $verb)
 * @method $this get(string $url)
 * @method $this post(string $url)
 * @method $this put(string $url)
 * @method $this delete(string $url)
 * @method $this options(string $url)
 * @method $this patch(string $url)
 */
class Expectation extends \BlastCloud\Chassis\Expectation
{

}