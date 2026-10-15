# Fork of opis/json-schema

This fork is the base that polyspec depends on; it is not assumed that
upstream accepts its fixes. Upstream is
[opis/json-schema](https://github.com/opis/json-schema).

## What it fixes

- In the collect-all-errors mode (`setStopAtFirstError(false)`, max errors
  above 1), a property that failed its `properties` subschema kept the
  keyword from recording the checked properties, so
  `additionalProperties: false` reported every property, valid ones
  included, as not allowed. A failing keyword group also stopped the later
  groups, so `unevaluatedProperties` never ran. Both keywords now report
  only the properties no keyword applies to (`tests/CollectAllErrorsTest.php`).

Releases are tagged `v<upstream version>-p<n>` (for example `v2.6.0-p1`),
which Composer reads as a stable patch release of the upstream version.
