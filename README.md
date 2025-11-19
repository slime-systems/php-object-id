# `SlimeSystems\ObjectId`

A feature-packed ObjectId implementation for PHP.

---

## Usage Guide

`SlimeSystems\ObjectId` is a BSON-compatible identifier similar to MongoDB's `ObjectId`.
It provides a 12-byte binary id based on the specification, along with handy utilities.

### Creating ObjectIds

#### Create a new ObjectId

When no data is provided, a new unique identifier is generated.

~~~php
use SlimeSystems\ObjectId;

$id = new ObjectId;

var_dump($id->toString()); // 24-char hex string
~~~

#### From raw binary data

Accepts a binary string of exactly 12 bytes.

~~~php
$id = ObjectId::fromBinary($raw);
~~~

Invalid lengths (anything other than 12 bytes) will throw `SlimeSystems\ObjectId\Exception\Invalid`.

#### From a hex string

Accepts a 24-character hexadecimal string.

~~~php
$id = ObjectId::fromString($hex);
~~~

Invalid hex or invalid lengths will throw `SlimeSystems\ObjectId\Exception\Invalid`.

#### From a timestamp

You can generate an ObjectId from either a DateTime object or a Unix timestamp.

~~~php
$id = ObjectId::fromTime($dateTime);
// or
$id = ObjectId::fromTime($timestamp);
~~~

#### ObjectIds for time comparison

If you need an ObjectId for time comparisons, you can add `unique: false` to zeroes the last 8 bytes out:

~~~php
$id = ObjectId::fromTime($time, unique: false);
// Last 8 bytes of the hex string will be "0000000000000000"
~~~

### Conversions

#### Convert to string

~~~php
$id->toString() // return 24-digit hexadecimal string
~~~

#### Convert to binary

~~~php
$id->toBinary() // return 12 byte binary string
~~~

#### Extracting the time

You can retrieve the timestamp embedded in the ObjectId:

~~~php
$id = new ObjectId;

// Returns a DateTime object
$time = $id->toTime();
~~~

#### Human-readable inspection

`inspect()` returns a more detailed string containing the hex form and extra info.

~~~php
$id->inspect() // return "SlimeSystems\ObjectId(<hexadecimal representation>)"
~~~

### Comparison

#### Equality

~~~php
$id->equals($id); // true
~~~

Comparing to a non-ObjectId always returns `false`.

#### Lexicographic comparison

`compareTo()` works similarly to `strcmp()`:

~~~php
$id1->compareTo($id2); // -1 (smaller)
$id2->compareTo($id1); // 1  (larger)
$id1->compareTo($id1); // 0  (equal)
~~~

## `ObjectId` provides:

* Unique ID generation
* Creation from raw data, hex strings, or timestamps
* Deterministic time-based IDs
* String/binary conversion
* Timestamp extraction
* Comparison helpers (`equals()`, `compareTo()`)
* Human-readable inspection

This makes it a flexible utility for systems that need compact, sortable, BSON-compatible identifiers.

## Tests

~~~bash
composer run test
~~~

or if you have containerd:

~~~bash
make test
~~~

## License

[BSD 2-Clause License](./LICENSE.md)
