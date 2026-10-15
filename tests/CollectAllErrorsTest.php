<?php
/* ============================================================================
 * Copyright 2020 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\JsonSchema\Test;

use Opis\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use Opis\JsonSchema\Errors\ErrorFormatter;

/**
 * In the collect-all-errors mode a property that fails its `properties`
 * subschema must still count as checked (and evaluated by `properties`
 * annotations), so `additionalProperties: false` and
 * `unevaluatedProperties: false` report only the properties that no
 * keyword applies to.
 */
class CollectAllErrorsTest extends TestCase
{
    private function errors(string $closing, string $data): array
    {
        $schema = '{"$schema":"https://json-schema.org/draft/2020-12/schema","type":"object",'
            . '"properties":{"version":{"const":1},"name":{"type":"string"},"items":{"type":"array"}},'
            . '"required":["version","name","items"],"' . $closing . '":false}';

        $validator = new Validator();
        $validator->setMaxErrors(10);
        $validator->setStopAtFirstError(false);

        $result = $validator->validate(json_decode($data), $schema);
        $this->assertFalse($result->isValid());

        return (new ErrorFormatter())->format($result->error(), true);
    }

    public function closingKeywords(): array
    {
        return [
            'additionalProperties' => ['additionalProperties', 'Additional object properties are not allowed: extra'],
            'unevaluatedProperties' => ['unevaluatedProperties', 'Unevaluated object properties not allowed: extra'],
        ];
    }

    /**
     * @dataProvider closingKeywords
     */
    public function testFailingPropertyStaysChecked(string $closing, string $message)
    {
        $errors = $this->errors($closing, '{"version":2,"name":"x","items":[],"extra":true}');

        $this->assertSame(['/version', '/'], array_keys($errors));
        $this->assertCount(1, $errors['/version']);
        $this->assertSame([$message], $errors['/']);
    }

    /**
     * @dataProvider closingKeywords
     */
    public function testValidPropertiesWithoutExtra(string $closing)
    {
        $errors = $this->errors($closing, '{"version":2,"name":"x","items":[]}');

        $this->assertSame(['/version'], array_keys($errors));
    }
}
